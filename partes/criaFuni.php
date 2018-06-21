<?php

//	Verifica parametro sshd
if( !isset( $_GET["sshd"] ) )
	{
	echo	'{ "data": [{"erro": "parametro sshd obrigatório"}] }';
	return;
	}
$sshd = $_GET["sshd"];

//	verifica parametro idretr id do regime de trabalho
if( !isset( $_GET["idretr"] ) )
	{
	echo	'{ "data": [{"erro": "parametro idretr obrigatório"}] }';
	return;
	}
$idretr = $_GET["idretr"];

//	verifica parametro ualoc	-	uor de alocação no PONTO
if( !isset( $_GET["ualoc"] ) )
	{
	echo	'{ "data": [{"erro": "parametro ualoc obrigatório"}] }';
	return;
	}
$ualoc = $_GET["ualoc"];
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
	
//	verifica se a nova UOR de alocação existe no SAU
$sql = "SELECT UOR_DTINICIAL from SAU.VWUORPUBLICA 
					WHERE UOR_DTFINAL IS NULL AND
								UOR_IDUNIDADEORGANIZACIONAL=$ualoc";
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
	fmtErro( "erro", "UOR de alocação não existe" );
	$ora->disconnect();
	return;
	}
$ora->libStmt();

//	verifica se este sshd já está no PONTO
$sql = "SELECT FUNI_ID FROM BIOMETRIA.FUNI_FUNCIONARIO
					WHERE PMS_IDPMSPESSOA='$sshd'";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "resultado verifica existencia de sshd sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Obtendo sshd: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas > 0 )
	{
	fmtErro( "erro", "SSHD já existe no PONTO" );
	$ora->disconnect();
	return;
	}
$ora->libStmt();

//	verifica idretr
$sql = "SELECT RETR_ID FROM BIOMETRIA.RETR_REGIMETRABALHO
					WHERE RETR_ID='$idretr'";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "resultado verifica existencia de retr sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Obtendo retr: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 0 )
	{
	fmtErro( "erro", "Regime não existe" );
	$ora->disconnect();
	return;
	}
$ora->libStmt();

//	inicia uma transação 
$ora->beginTransaction();

//	insere FUNI
$sql = "INSERT INTO BIOMETRIA.FUNI_FUNCIONARIO ( FUNI_ID, PMS_IDPMSPESSOA, FUNI_STATIVO, FUNI_STMENS ) VALUES
				  ( BIOMETRIA.SQ_FUNI.NEXTVAL, '$sshd', 1, 0 )";
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FUNI" );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "insere funi SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "inserindo funi: $jres->erro" );
	$ora->rollback();
	$ora->disconnect();
	return;
	}	
$idfuni = $jres->idnovo;
if( $dbg )
	echo "FUNI_ID novo: $idfuni<br>";

//	insere FUOR
$sql = "INSERT INTO BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL VALUES
				  ( BIOMETRIA.SQ_FUOR.NEXTVAL, $idfuni, $ualoc, TRUNC( SYSDATE+1 ), null )";
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FUOR" );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "insere fuor SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "inserindo fuor: $jres->erro" );
	$ora->rollback();
	$ora->disconnect();
	return;
	}	

//	insere FRTR
$sql = "INSERT INTO BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO VALUES
				  ( BIOMETRIA.SQ_FRTR.NEXTVAL, $idfuni, $idretr, NULL, TRUNC( SYSDATE+1 ), NULL )";
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FUOR" );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "insere fuor SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "inserindo fuor: $jres->erro" );
	$ora->rollback();
	$ora->disconnect();
	return;
	}	

//	insere FSHM
$sql = "INSERT INTO BIOMETRIA.FSHM_FUNCSALDOHORAMENSAL VALUES
				  ( BIOMETRIA.SQ_FSHM.NEXTVAL, $idfuni, TRUNC( SYSDATE ),  
					0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 )";
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FSHM" );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "insere FSHM SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "inserindo FSHM: $jres->erro" );
	$ora->rollback();
	$ora->disconnect();
	return;
	}	

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
