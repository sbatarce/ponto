<?php
//	alteraFUAU( fuauid, [dtini], [dtfim] )
//	adiciona um período de autorização
//		verifica existencia da UOR
//		verifica se há duplicidade do autorizador na UOR no período
//		adiciona FUAU
if( !isset( $_GET["fuauid"] ) )
	{
	echo	'{ "data": [{"erro": "parametro funiid obrigatório"}] }';
	return;
	}
$fuauid = $_GET["fuauid"];

if( !isset( $_GET["dtini"] ) )
	{
	echo	'{ "data": [{"erro": "parametro dtini obrigatório"}] }';
	return;
	}
$dtini = $_GET["dtini"];

if( !isset( $_GET["dtfim"] ) )
	{
	echo	'{ "data": [{"erro": "parametro dtfim obrigatório"}] }';
	return;
	}
$dtfim = $_GET["dtfim"];

if( $dtini == 'null' || $dtini == 'NULL' )
	{
	echo	'{ "data": [{"erro": "parametro dtini nao pode ser nulo"}] }';
	return;
	}
	
if( $dtini == '' || $dtfim == '' )
	{
	echo	'{ "data": [{"erro": "parametro dtini ou dtfim sem conteúdo"}] }';
	return;
	}

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
	
//	verifica duplicidade de autorização na UOR e no período
$auxfim = $dtfim;
if( $dtfim == 'NULL' || $dtfim == 'null' )
	$auxfim = '30001201';
$sql = "SELECT FUAU_ID 
					FROM  BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR 
					WHERE FUAU_ID <> $fuauid AND
								FUNI_ID=(SELECT FUNI_ID 
														FROM BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR 
														WHERE FUAU_ID=$fuauid) AND 
								PMS_IDSAUUOR=(SELECT PMS_IDSAUUOR 
																FROM BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR 
																WHERE FUAU_ID=$fuauid) AND (
								( TO_DATE( '$dtini', 'YYYYMMDD' ) BETWEEN FUAU_DTINICIO AND 
											NVL(FUAU_DTFIM, TO_DATE('30001231', 'YYYYMMDD') ) OR
									TO_DATE( '$auxfim', 'YYYYMMDD' ) BETWEEN FUAU_DTINICIO AND 
											NVL(FUAU_DTFIM, TO_DATE('30001231', 'YYYYMMDD')) ) OR
								( FUAU_DTINICIO BETWEEN
										TO_DATE( '$dtini', 'YYYYMMDD' ) AND
										TO_DATE( '$auxfim', 'YYYYMMDD' ) OR
									NVL(FUAU_DTFIM, TO_DATE('30001231', 'YYYYMMDD')) BETWEEN
										TO_DATE( '$dtini', 'YYYYMMDD' ) AND
										TO_DATE( '$auxfim', 'YYYYMMDD' ) )
								)";

$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "verifica UOR sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Verificando Duplicidade: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas > 0 )
	{
	fmtErro( "erro", "Esta pessoa ja é autorizador desta UOR neste período" );
	$ora->disconnect();
	return;
	}
$ora->libStmt();

//	inicia uma transação 
$ora->beginTransaction();

//	altera
if( $dtfim != "null" && $dtfim != "NULL" )
	$sql = "UPDATE	BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR 
						SET		FUAU_DTINICIO=TO_DATE( '$dtini', 'YYYYMMDD' ), 
									FUAU_DTFIM=TO_DATE( '$dtfim', 'YYYYMMDD' ) 
						WHERE FUAU_ID=$fuauid";
else
	$sql = "UPDATE	BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR 
						SET		FUAU_DTINICIO=TO_DATE( '$dtini', 'YYYYMMDD' ), 
									FUAU_DTFIM=NULL 
						WHERE FUAU_ID=$fuauid";
$res = $ora->execDelUpd( $sql );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "update FUAU SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Erro alterando Período: $jres->erro" );
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
