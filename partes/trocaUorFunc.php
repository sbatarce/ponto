<?php

//	Verifica parametro FUNI_ID
if( !isset( $_GET["funiid"] ) )
	{
	echo	'{ "data": [{"erro": "parametro funiid obrigatório"}] }';
	return;
	}
$funiid = $_GET["funiid"];

//	verifica parametro uornova = ID da nova UOR do funcionário
if( !isset( $_GET["uornova"] ) )
	{
	echo	'{ "data": [{"erro": "parametro uornova obrigatório"}] }';
	return;
	}
$uornova = $_GET["uornova"];
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
	
//	verifica se a nova UOR existe no SAU
$sql = "SELECT UOR_DTINICIAL from SAU.VWUORPUBLICA 
					WHERE UOR_DTFINAL IS NULL AND
								UOR_IDUNIDADEORGANIZACIONAL=$uornova";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "verifica UOR sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Verificando existencia da UOR: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	{
	fmtErro( "erro", "Esta UOR não existe" );
	$ora->disconnect();
	return;
	}
$ora->libStmt();

//	verifica se há um FUOR aberto para o funcionário
$sql = "SELECT FUOR_ID, PMS_IDSAUUOR FROM BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL
					WHERE FUOR_DTFIM IS NULL AND
								FUNI_ID=$funiid";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "resultado verifica FUOR anterior sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Obtendo alocacao anterior: $jres->erro" );
	$ora->disconnect();
	return;
	}
$idfuorant = -1;
if( $jres->linhas > 0 )
	{
	$dado = $jres->dados[0];
	if( $dbg )
		var_dump( $dado );
	$idfuorant = $dado->FUOR_ID;
	}
if( $dbg )
	echo "uor anterior=$idfuorant<br>";
$ora->libStmt();

//	inicia uma transação 
$ora->beginTransaction();

//	fecha a eventual FUOR atual
if( $dbg )
	echo "id da uor anterior: $idfuorant<br>";
if( $idfuorant >= 0 )
	{
	$sql = "UPDATE BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL 
						SET FUOR_DTFIM=SYSDATE 
						WHERE FUOR_ID=$idfuorant";
	$res = $ora->execDelUpd($sql);
	$jres = json_decode( $res );
	if( $dbg )
		{
		echo "update FUOR anterio SQL=$sql/resultado:";
		var_dump($jres);
		}
	if( $jres->status != "OK" )
		{
		fmtErro( "erro", "Encerrando alocacao anterior: $jres->erro" );
		$ora->rollback();
		$ora->disconnect();
		return;
		}	
	}
	
//	cria a nova alocação
$sql = "INSERT INTO BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL VALUES
				  ( BIOMETRIA.SQ_FUOR.NEXTVAL, $funiid, $uornova, SYSDATE+1, null )";
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FUOR" );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "insere a nova alocação SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Iniciando a alocacao: $jres->erro" );
	$ora->rollback();
	$ora->disconnect();
	return;
	}	
$id = $jres->idnovo;
//	commita e encerra
if( $dbg )
	{
	echo "{ \"status\": \"warn\", \"warn\": \"debug ativado=>rollback\", \"id\": \"$id\" }";
	$ora->rollback();
	}
else
	{
	echo "{ \"status\": \"OK\", \"id\": \"$id\" }";
	$ora->commit();
	}
$ora->disconnect();
