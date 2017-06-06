<?php
if( !isset( $_GET["query"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro query obrigatorio" }';
	return;
	}
$sequence =	"";
if( isset( $_GET["sequence"] ) )
	$sequence =	$_GET["sequence"];
//	prepara o query
$sql	=	"";
$qry	=	$_GET["query"];
//
if( $qry == "insger" )
	{
	if( !isset( $_GET["tbl"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro tbl obrigatorio" }';
		return;
		}
	if( !isset( $_GET["cmps"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro cmps obrigatorio" }';
		return;
		}
	if( !isset( $_GET["vals"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro vals obrigatorio" }';
		return;
		}
	$tbl	=	$_GET["tbl"];
	$cmps	=	$_GET["cmps"];
	$vals	=	$_GET["vals"];
	$sql	=	"insert into $tbl($cmps) values($vals)";
	}
//	insert diálogo
if( $qry == "indial" )
	{
	if( !isset( $_GET["fdtrid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro fdtrid obrigatorio" }';
		return;
		}
	if( !isset( $_GET["msg"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro msg obrigatorio" }';
		return;
		}
	if( !isset( $_GET["vals"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro vals obrigatorio" }';
		return;
		}
	$fdtrid	=	$_GET["fdtrid"];
	$msg	=	$_GET["msg"];

	$sql	=	"INSERT INTO BIOMETRIA.FDTM_FUNCDIATRABALHOMENSAGEM
						( FDTM_ID, FDTR_ID, FDTM_TPFUNCAUT, FDTM_DLMENS, FDTM_DTMENS, FDTM_STEMAIL )
            VALUES( BIOMETRIA.SQ_FDTM.NEXTVAL, $fdtrid, 'F', '$msg', sysdate, 0  )";
	}
//	insert FDTE
if( $qry == "infdte" )
	{
	if( !isset( $_GET["fdtrid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro fdtrid obrigatorio" }';
		return;
		}
	if( !isset( $_GET["operid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro operid obrigatorio" }';
		return;
		}
	if( !isset( $_GET["origid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro origid obrigatorio" }';
		return;
		}
	if( !isset( $_GET["hora"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro hora obrigatorio" }';
		return;
		}

	$fdtrid = $_GET["fdtrid"];
	$operid = $_GET["operid"];
	$origid = $_GET["origid"];
	$hora = $_GET["hora"];
						
	$sql	=	"INSERT INTO BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO
						( FDTE_ID, FDTR_ID, TORG_ID, TORE_ID, REPR_ID, FDTE_DTHORARIO )
						VALUES( BIOMETRIA.SQ_FDTE.NEXTVAL, $fdtrid, $operid, $origid, 
										NULL, 
										TO_DATE( '$hora', 'DD/MM/YYYY HH24:mi' ) )";
	}
//	insert FAAU
if( $qry == "infaau" )
	{
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

	$funiid = $_GET["funiid"];
	$taauid = $_GET["taauid"];
	$fuauid = $_GET["fuauid"];
	$dtini = $_GET["dtini"];
	$dtfim = $_GET["dtfim"];
	$mins = $_GET["mins"];
						
	$sql	=	"insert into BIOMETRIA.FAAU_FUNCAUSENCIAAUTORIZADA 
						( FAAU_ID, FUNI_ID, TAAU_ID, FUAU_ID, FAAU_DTINI, FAAU_DTFIM, FAAU_NITMPDIARIO )
						VALUES( BIOMETRIA.SQ_FAAU.NEXTVAL, $funiid, $taauid, $fuauid, 
										TO_DATE( '$dtini', 'YYYYMMDD' ), 
										TO_DATE( '$dtfim', 'YYYYMMDD' ), $mins )";
	}
//	insert FUCO
if( $qry == "infuco" )
	{
	if( !isset( $_GET["funiid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro funiid obrigatorio" }';
		return;
		}
	if( !isset( $_GET["fuauid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro fuauid obrigatorio" }';
		return;
		}
	if( !isset( $_GET["dtref"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro dtref obrigatorio" }';
		return;
		}
	if( !isset( $_GET["dbcr"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro dbcr obrigatorio" }';
		return;
		}
	if( !isset( $_GET["mins"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro mins obrigatorio" }';
		return;
		}

	$funiid = $_GET["funiid"];
	$taauid = $_GET["taauid"];
	$fuauid = $_GET["fuauid"];
	$dtref = $_GET["dtref"];
	$dbcr = $_GET["dbcr"];
	$mins = $_GET["mins"];
						
	$sql	=	"INSERT INTO BIOMETRIA.FUCO_FUNCCORRECAOHORAS
						( FUCO_ID, FUNI_ID, FUAU_ID, FUCO_DTREFERENCIA, FUCO_DCDBCR, FUCO_NITMP )
						VALUES(	BIOMETRIA.SQ_FUCO.NEXTVAL, $funiid, $fuauid, 
										TO_DATE( '$dtref', 'YYYYMMDD' ), '$dbcr', $mins )";
	}
////////////////////////////////////////////////////////////////////////////////
if( isset( $_GET["debug"] ) )
	{
	echo $sql . "<br>";
	return;
	}
//	executa o SQL
include 'ambiente.php';
include 'ORAConn.php';

$ora = new ORAConn();
$res = $ora->connect($userb, $passb, $amb, $chset);
if( $res != "OK" )
	{
	echo $res;
	return;
	}
//	executa o query
$res = $ora->execInsert( $sql, $sequence );
echo $res;
$ora->disconnect();

