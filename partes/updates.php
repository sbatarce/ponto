<?php
if( !isset( $_GET["query"] ) && !isset( $_GET["debug"] ) )
	{
	echo	'{ "data": [{"erro": "parametro query obrigatório"}] }';
	return;
	}
//	prepara o select
if( isset( $_GET["query"] ) )
	{
	$qry = $_GET["query"];
	$dbg = FALSE;
	}
if( isset( $_GET["debug"] ) )
	{
	$qry = $_GET["debug"];
	$dbg = TRUE;
	}
	
//	prepara o query
$sql	=	"";
//	update genérico
if( $qry == "upd" )
	{
	if( !isset( $_GET["tbl"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro tbl obrigatorio" }';
		return;
		}
	if( !isset( $_GET["alter"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro alter obrigatorio" }';
		return;
		}
	if( !isset( $_GET["selec"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro selec obrigatorio" }';
		return;
		}	
	$sql	=	"update ".$_GET["tbl"]." set ".$_GET["alter"]." where ".$_GET["selec"];
	}
//	delete fdte
if( $qry == "delfdte" )
	{
	if( !isset( $_GET["fdteid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro fdteid obrigatorio" }';
		return;
		}
	$fdteid = $_GET["fdteid"];
	$sql	=	"DELETE BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO 
						WHERE FDTE_ID=$fdteid"; 
	}
//	seta FDTR em analise
if( $qry == "settsdt" )
	{
	if( !isset( $_GET["fdtrid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro fdtrid obrigatorio" }';
		return;
		}
	if( !isset( $_GET["tsdt"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro tsdt obrigatorio" }';
		return;
		}
	$fdtrid = $_GET["fdtrid"];
	$tsdt = $_GET["tsdt"];
	$sql	=	"UPDATE BIOMETRIA.FDTR_FUNCIONARIODIATRABALHO 
						SET TSDT_ID=$tsdt
						WHERE FDTR_ID=$fdtrid"; 
	}
//	update FDTE
if( $qry == "upfdte" )
	{
	if( !isset( $_GET["fdteid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro fdteid obrigatorio" }';
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

	$fdteid = $_GET["fdteid"];
	$operid = $_GET["operid"];
	$origid = $_GET["origid"];
	$hora = $_GET["hora"];
						
	$sql	=	"UPDATE BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO 
							SET TORG_ID=$operid, TORE_ID=$origid, 
							FDTE_DTHORARIO=TO_DATE( '$hora', 'DD/MM/YYYY HH24:mi' )
							WHERE FDTE_ID=$fdteid";
	}
//	update diálogo
if( $qry == "updial" )
	{
	if( !isset( $_GET["fdtmid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro fdtmid obrigatorio" }';
		return;
		}
	if( !isset( $_GET["msg"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro msg obrigatorio" }';
		return;
		}

	$fdtmid = $_GET["fdtmid"];
	$msg = $_GET["msg"];
						
	$sql	=	"UPDATE BIOMETRIA.FDTM_FUNCDIATRABALHOMENSAGEM 
							SET FDTM_DLMENS='$msg', FDTM_DTMENS=sysdate, 
									FDTM_TPFUNCAUT='F', FDTM_STEMAIL=0
							WHERE FDTM_ID=$fdtmid";
	}
//
//	executa o SQL
include 'ambiente.php';
include 'ORAConn.php';

if( $dbg )
	{
	echo "QUERY:$sql<br>";
	return;
	}

$ora = new ORAConn();
$res = $ora->connect($userb, $passb, $amb, $chset);
if( $res != "OK" )
	{
	echo $res;
	return;
	}
//	executa o query
$res = $ora->execDelUpd($sql);
echo $res;
$ora->disconnect();

