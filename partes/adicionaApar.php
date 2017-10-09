<?php
//	adicionaApar	-	 adiciona o funcionário FUNI ao aparelho IDAPAL
//	verifica FUNIID e APALID
//	insere LRTR
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
	fmtErro( "erro", "Não existe este Local de Trabalho" );
	$ora->disconnect();
	return;
	}
$idlotr = $jres->dados[0]->LOTR_ID;
$ora->libStmt();

//	verifica a existencia do FUNIID
$sql = "select PMS_IDPMSPESSOA from BIOMETRIA.FUNI_FUNCIONARIO WHERE FUNI_ID=$funiid";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "verifica UOR sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Acessando FUNI: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	{
	fmtErro( "erro", "Este funcionario não está cadastrado no ponto" );
	$ora->disconnect();
	return;
	}
$sshd = $jres->dados[0]->PMS_IDPMSPESSOA;

$ora->libStmt();

//	verifica a existencia de FLTR base
$sql = "select FLTR_STBASE AS BASE from BIOMETRIA.FLTR_FUNCIONARIOLOCALTRABALHO
					WHERE FUNI_ID=$funiid AND FLTR_STBASE=1";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "verifica BASE sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Verificando BASE: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	$base = "1";
else
	$base = "0";
$ora->libStmt();

//	inicia uma transação 
$ora->beginTransaction();
//	
if( $dbg )
	{
	echo "id LOTR: $idlotr<br>";
	echo "SSHD: $sshd<br>";
	echo "Base: $base<br>";
	}
//	insere o novo 
$sql = "INSERT INTO BIOMETRIA.FLTR_FUNCIONARIOLOCALTRABALHO VALUES
					( BIOMETRIA.SQ_FLTR.NEXTVAL, $funiid, $idlotr, $base )";
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FLTR" );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "insere FLTR SQL=$sql/resultado:";
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
