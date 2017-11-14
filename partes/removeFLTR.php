<?php
if( !isset( $_GET["funiid"] ) )
	{
	echo	'{ "data": [{"erro": "parametro funiid obrigatório"}] }';
	return;
	}
$funiid = $_GET["funiid"];

if( !isset( $_GET["apalid"] ) )
	{
	echo	'{ "data": [{"erro": "parametro apalid obrigatório"}] }';
	return;
	}
$apalid = $_GET["apalid"];

//
include '../partes/fmtErro.php';
include '../partes/ambiente.php';
include '../partes/ORAConn.php';

if( isset( $_SERVER['PHP_AUTH_USER'] ) )
	{
	$userb = $_SERVER['PHP_AUTH_USER'];
	$passb = $_SERVER['PHP_AUTH_PW'];
	}

if(isset($_GET["amb"]))
  $amb = $_GET["amb"];
if(isset($_GET["chs"]))
  $chset = $_GET["chs"];
if(isset($_GET["rle"]))
  $rle = $_GET["rle"];

$dbg = isset( $_GET["dbg"] );
//	abre o ORACLE
$ora = new ORAConn();
$res = $ora->connect( $userb, $passb, $amb, $chset, $role );
if( $res != "OK" )
	{
	echo $res;
	return;
	}
if( $dbg )
	{
	echo "conectou amb=$amb use=$userb role=$role<br>";
	}
	
//	encontra o id da LOTR a partir do apalid
$sql = "select LOTR_ID from BIOMETRIA.LOTR_LOCALTRABALHO WHERE APAL_ID=$apalid";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "acha ID da LOTR sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Acessando LOTR: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	{
	fmtErro( "erro", "Não existe este Local de Trabalho" );
	$ora->disconnect();
	return;
	}
$idlotr = $jres->dados[0]->LOTR_ID;
$ora->libStmt();

//	inicia uma transação 
$ora->beginTransaction();

//	fecha a eventual FUOR atual
if( $dbg )
	echo "id LOTR: $idlotr<br>";
//	remove o FLTR 
$sql = "DELETE FROM BIOMETRIA.FLTR_FUNCIONARIOLOCALTRABALHO
					WHERE FUNI_ID=$funiid AND LOTR_ID=$idlotr";
$res = $ora->execDelUpd( $sql );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "remove FLTR SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Iniciando a alocacao: $jres->erro" );
	$ora->rollback();
	$ora->disconnect();
	return;
	}	
//	commita e encerra
if( $dbg )
	{
	echo "{ \"status\": \"warn\", \"warn\": \"debug ativado=>rollback\" }";
	$ora->rollback();
	}
else
	{
	echo "{ \"status\": \"OK\" }";
	$ora->commit();
	}
$ora->disconnect();
