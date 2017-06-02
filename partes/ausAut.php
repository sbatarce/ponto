<?php
$sequence =	"";
if( isset( $_GET["sequence"] ) )
	$sequence =	$_GET["sequence"];
//	prepara o query
$sql	=	"";
//	verifica parametros
if( !isset( $_GET["funiid"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro funiid obrigatorio" }';
	return;
	}
if( !isset( $_GET["taauid"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro taauid obrigatorio" }';
	return;
	}
if( !isset( $_GET["fuauid"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro fuauid obrigatorio" }';
	return;
	}
if( !isset( $_GET["dtini"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro dtini obrigatorio" }';
	return;
	}
if( !isset( $_GET["dtfim"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro dtfim obrigatorio" }';
	return;
	}
if( !isset( $_GET["mins"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro mins obrigatorio" }';
	return;
	}
//	obtem parametros
$funiid = $_GET["funiid"];
$taauid = $_GET["taauid"];
$fuauid = $_GET["fuauid"];
$dtini = $_GET["dtini"];
$dtfim = $_GET["dtfim"];
$mins = $_GET["mins"];
//	prepara a conexão e inicia transaction
include 'ambiente.php';
include 'ORAConn.php';

$ora = new ORAConn();
$res = $ora->connect($userb, $passb, $amb, $chset);
if( $res != "OK" )
	{
	echo $res;
	return;
	}
$ora->beginTransaction();

//	insere FAAU
$sql	=	"insert into BIOMETRIA.FAAU_FUNCAUSENCIAAUTORIZADA VALUES( 
					BIOMETRIA.SQ_FAAU.NEXTVAL, $funiid, $taauid, $fuauid, 
					TO_DATE( '$dtini', 'YYYYMMDD' ), 
					TO_DATE( '$dtfim', 'YYYYMMDD' ), $mins )";
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FAAU" );
$jres	= json_decode($res);
if( $jres->status != "OK" )
	{
	echo $res;
	$ora->rollback();
	$ora->disconnect();
	return;
	}
$faauid = $jres->idnovo;
$ora->libStmt();
//	insere as eventuais FDTFs necessárias
$sql = "INSERT INTO BIOMETRIA.FDTF_FUNCDIATRABALHO_FAAU
					SELECT BIOMETRIA.SQ_FDTF.NEXTVAL, $faauid, FDTR.FDTR_ID, $fuauid, $mins
						FROM BIOMETRIA.FDTR_FUNCIONARIODIATRABALHO FDTR
						INNER JOIN  BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR ON
												FRTR.FRTR_ID=FDTR.FRTR_ID
						INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
												FUNI.FUNI_ID=FRTR.FUNI_ID
						WHERE FUNI.FUNI_ID=$funiid AND FDTR.FDTR_DTREFERENCIA 
										BETWEEN TO_DATE( '$dtini', 'YYYYMMDD' ) 
												AND TO_DATE( '$dtfim', 'YYYYMMDD' )";
$res = $ora->execInsert( $sql, "" );
$jres	= json_decode($res);
if( $jres->status != "OK" )
	{
	echo $res;
	$ora->rollback();
	$ora->disconnect();
	return;
	}
//	commit e encerramento
$ora->commit();
$ora->disconnect();
echo $res;
return;

