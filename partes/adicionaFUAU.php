<?php
//	adicionaFUAU( funiid, uorid, dtini, dtfim )
//	adiciona um período de autorização
//		verifica existencia da UOR
//		verifica se há duplicidade do autorizador na UOR no período
//		adiciona FUAU
if( !isset( $_GET["funiid"] ) )
	{
	echo	'{ "data": [{"erro": "parametro funiid obrigatório"}] }';
	return;
	}
$funiid = $_GET["funiid"];
if( !isset( $_GET["uorid"] ) )
	{
	echo	'{ "data": [{"erro": "parametro uorid obrigatório"}] }';
	return;
	}
$uorid = $_GET["uorid"];
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
	
//	verifica se a uorid existe
$sql = "SELECT UOR_IDUNIDADEORGANIZACIONAL FROM SAU.VWUORPUBLICA 
					WHERE UOR_IDUNIDADEORGANIZACIONAL=$uorid AND
								UOR_DTFINAL IS NULL AND
								UOR_STATIVO=1";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "verifica UOR sql=$sql/resultado:";
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
	fmtErro( "erro", "Não existe esta UOR" );
	$ora->disconnect();
	return;
	}
$ora->libStmt();

//	verifica duplicidade de autorização na UOR e no período
if( $dtfim != 'NULL' && $dtfim != 'null' )
	{
	$sql = "SELECT FUAU_ID 
						FROM  BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR 
						WHERE FUNI_ID=$funiid AND PMS_IDSAUUOR=$uorid AND
									( TO_DATE( '$dtini', 'YYYYMMDD' ) BETWEEN FUAU_DTINICIO AND 
												NVL(FUAU_DTFIM, TO_DATE('30001231', 'YYYYMMDD') ) OR
										TO_DATE( '$dtfim', 'YYYYMMDD' ) BETWEEN FUAU_DTINICIO AND 
												NVL(FUAU_DTFIM, TO_DATE('30001231', 'YYYYMMDD')) )";
	}
else
	{
	$sql = "SELECT FUAU_ID 
						FROM  BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR 
						WHERE FUNI_ID=$funiid AND PMS_IDSAUUOR=$uorid AND
									( TO_DATE( '$dtini', 'YYYYMMDD' ) <= FUAU_DTINICIO OR 
										TO_DATE( '$dtini', 'YYYYMMDD' ) <= 
													NVL(FUAU_DTFIM, TO_DATE('30001231', 'YYYYMMDD')) )";
	}
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "verifica UOR sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Acessando LOTR: $jres->erro" );
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

//	insere o novo 
if( $dtfim != 'NULL' && $dtfim != 'null' )
	{
	$sql = "INSERT INTO BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR VALUES
						( BIOMETRIA.SQ_FUAU.NEXTVAL, $funiid, $uorid, 
						TO_DATE( '$dtini', 'YYYYMMDD' ), TO_DATE( '$dtfim', 'YYYYMMDD' ) )";
	}
else
	{
	$sql = "INSERT INTO BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR VALUES
						( BIOMETRIA.SQ_FUAU.NEXTVAL, $funiid, $uorid, 
						TO_DATE( '$dtini', 'YYYYMMDD' ), NULL )";
	}
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FUAU" );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "insere FUAU SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Erro inserindo Período: $jres->erro" );
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
