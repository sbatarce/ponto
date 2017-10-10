<?php

//	Verifica parametro FUNI_ID
if( !isset( $_GET["funiid"] ) )
	{
	echo	'{ "data": [{"erro": "parametro funiid obrigatório"}] }';
	return;
	}
$funiid = $_GET["funiid"];

//	verifica parametro reginovo = ID do novo regime
if( !isset( $_GET["reginovo"] ) )
	{
	echo	'{ "data": [{"erro": "parametro reginovo obrigatório"}] }';
	return;
	}
$reginovo = $_GET["reginovo"];
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
	
//	verifica se o novo regime existe
$sql = "SELECT RETR_DLNOME FROM  BIOMETRIA.RETR_REGIMETRABALHO WHERE RETR_ID=$reginovo";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "verifica UOR sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Verificando existencia do regime novo: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	{
	fmtErro( "erro", "Este regime não existe" );
	$ora->disconnect();
	return;
	}
$ora->libStmt();

//	verifica se há um FRTR aberto para o funcionário
$sql = "SELECT FRTR_ID FROM BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO 
					WHERE FRTR_DTFIM IS NULL AND FUNI_ID=$funiid";
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
$idregiant = -1;
if( $jres->linhas > 0 )
	{
	$dado = $jres->dados[0];
	if( $dbg )
		var_dump( $dado );
	$idregiant = $dado->FRTR_ID;
	}
if( $dbg )
	echo "uor anterior=$idregiant<br>";
$ora->libStmt();

//	inicia uma transação 
$ora->beginTransaction();

//	fecha o eventual Regime corrente
if( $dbg )
	echo "id do regime anterior: $idregiant<br>";
if( $idregiant >= 0 )
	{
	$sql = "UPDATE BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO 
						SET FRTR_DTFIM=SYSDATE
						WHERE FRTR_ID=$idregiant";
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
$sql = "INSERT INTO BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO VALUES
					( BIOMETRIA.SQ_FRTR.NEXTVAL, $funiid, $reginovo, null, 
						SYSDATE+1, null )";
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FRTR" );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "insere o novo regime SQL=$sql / resultado:";
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
