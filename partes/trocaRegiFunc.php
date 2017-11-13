<?php
/**
 * trocaRegiFunc( funiid, retrid )
 *		funiid		-	id do uncionário no ponto
 *		retrid		- id do novo regime de trabalho
 * troca o regime de trabalho do funcionário para DTUPRC
 * 
 *		-verifica se o novo regime existe
 *		-obtem a data do último processamento dtupro
 *		-obtem a data do último fechamento do funcionário dtufech
 *		-verifica se o dtuprc e dtufech são iguais
 *		-obtem o id do FRTR ativo (se existir) do funcionário
 *		-inicia uma transaction
 *		-caso pre-exista FRTR, atualiza o DTFIM para a data do fechamento
 */

//	Verifica parametro FUNI_ID
if( !isset( $_GET["funiid"] ) )
	{
	echo	'{ "data": [{"erro": "parametro funiid obrigatório"}] }';
	return;
	}
$funiid = $_GET["funiid"];

//	verifica parametro retrid = ID do novo regime
if( !isset( $_GET["retrid"] ) )
	{
	echo	'{ "data": [{"erro": "parametro retrid obrigatório"}] }';
	return;
	}
$retrid = $_GET["retrid"];

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
$sql = "SELECT RETR_DLNOME FROM  BIOMETRIA.RETR_REGIMETRABALHO WHERE RETR_ID=$retrid";
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

//	obtem a data do último processamento
$sql = "SELECT TO_CHAR( PAGL_DTPROCESSADA, 'YYYYMMDD' ) DTUPRC
					FROM BIOMETRIA.PAGL_PARAMETROSGLOBAIS";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "obtem DTUPRC sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Obtendo data do ultimo processamento: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	{
	fmtErro( "erro", "Obtendo data do ultimo processamento: não há registros" );
	$ora->disconnect();
	return;
	}
$dtuprc = $jres->dados[0]->DTUPRC;
if( $dbg )
	echo "DTUPRC: $dtuprc<br>";
$ora->libStmt();

//	obtem a data do último fechamento do funcionário no formato YYYYMMDD
$sql = "SELECT	TO_CHAR( MAX(FSHM_DTREFERENCIA), 'YYYYMMDD' ) as DTUFECH,
								TO_CHAR( MAX(FSHM_DTREFERENCIA), 'DD/MM/YYYY' ) as STUFECH
					FROM BIOMETRIA.FSHM_FUNCSALDOHORAMENSAL
					WHERE FUNI_ID=$funiid";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "obtem fechamento sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "obtem fechamento: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas > 0 )
	{
	}
$ora->libStmt();
$dtufech = $jres->dados[0]->DTUFECH;
if( $dbg )
	echo "dtuprc=$dtuprc dtufech=$dtufech<br>";
if( $dtuprc != $dtufech )
	{
	$stufech = $jres->dados[0]->STUFECH;
	$aux = "Não há um fechamento do funcionário no dia $stufech";
	fmtErro( "erro", $aux );
	$ora->disconnect();
	return;
	}

//	verifica se há um FRTR aberto para o funcionário
$sql = "SELECT FRTR_ID, TO_CHAR( FRTR_DTINICIO, 'YYYYMMDD' ) AS DTINI
					FROM BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO 
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
$dtinireg = "";
if( $jres->linhas > 0 )
	{
	$dado = $jres->dados[0];
	if( $dbg )
		var_dump( $dado );
	$idregiant = $dado->FRTR_ID;
	$dtinireg = $dado->DTINI;
	}
if( $dbg )
	echo "uor anterior=$idregiant<br>";
$ora->libStmt();

//	inicia uma transação 
$ora->beginTransaction();

//	fecha o eventual Regime corrente
if( $dbg )
	{
	echo "id do regime anterior: $idregiant<br>";
	echo "dtinireg=$dtinireg dtuprc=$dtuprc<br>";
	}
if( $idregiant >= 0 )
	{
	if( $dtinireg <= $dtuprc )
		{
		$sql = "UPDATE BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO 
							SET FRTR_DTFIM=TO_DATE( '$dtuprc', 'YYYYMMDD' )
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
	else
		{
		$sql = "UPDATE BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO 
							SET RETR_ID=$retrid
							WHERE FRTR_ID=$idregiant";
		$res = $ora->execDelUpd($sql);
		$jres = json_decode( $res );
		if( $dbg )
			{
			echo "update FUOR atual SQL=$sql/resultado:";
			var_dump($jres);
			}
		if( $jres->status != "OK" )
			{
			fmtErro( "erro", "alterando alocacao anterior: $jres->erro" );
			$ora->rollback();
			$ora->disconnect();
			return;
			}
		//	commita e encerra
		if( $dbg )
			{
			echo "{ \"status\": \"warn\", \"warn\": \"debug ativado=>rollback\", \"id\": \"$idregiant\" }";
			$ora->rollback();
			}
		else
			{
			echo "{ \"status\": \"OK\", \"id\": \"$idregiant\" }";
			$ora->commit();
			}
		$ora->disconnect();
		return;
		}
	}
	
//	cria a nova alocação
$sql = "INSERT INTO BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO VALUES
					( BIOMETRIA.SQ_FRTR.NEXTVAL, $funiid, $retrid, null, 
						TO_DATE( '$dtuprc', 'YYYYMMDD' )+1, null )";
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
