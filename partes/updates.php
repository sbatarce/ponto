<?php
//	delfuau	-	delete FUAU
//	update FLTR
//	delete FLTR
//	delete fdte
//	seta FDTR em analise
//	update FDTE
//	update diálogo
//	delfuco - remove FUCO
//	updfuco - altera FUCO
if( !isset( $_GET["query"] ) )
	{
	echo	'{ "data": [{"erro": "parametro query obrigatório"}] }';
	return;
	}
$qry = $_GET["query"];
if( isset( $_GET["dbg"] ) )
	$dbg = true;
else
	$dbg = false;
	
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
	
//	delfuco - remove fuco
if( $qry == "delfuco" )
	{
	if( !isset( $_GET["fucoid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro fucoid obrigatorio" }';
		return;
		}
	$fucoid = $_GET["fucoid"];

	$sql	=	"DELETE FROM BIOMETRIA.FUCO_FUNCCORRECAOHORAS WHERE FUCO_ID=$fucoid";
	}

//	updfuco - altera um fuco
if( $qry == "updfuco" )
	{
	if( !isset( $_GET["fucoid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro fucoid obrigatorio" }';
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
	if( isset( $_GET["obs"] ) )
		$obs = $_GET["obs"];
	else
		$obs = "";

	$fucoid = $_GET["fucoid"];
	$fuauid = $_GET["fuauid"];
	$dtref = $_GET["dtref"];
	$dbcr = $_GET["dbcr"];
	$mins = $_GET["mins"];
						
	$sql	=	"UPDATE  BIOMETRIA.FUCO_FUNCCORRECAOHORAS
						SET FUAU_ID=$fuauid, FUCO_DTREFERENCIA=TO_DATE( '$dtref', 'YYYYMMDD' ), 
								FUCO_DCDBCR='$dbcr', FUCO_NITMP=$mins,
								FUCO_DLOBS='$obs' 
						WHERE FUCO_ID=$fucoid";
	}

//	altmens( funiid, manda ) altera o estado da recepção de mensagens do funcionário
if( $qry == "altmens" )
	{
	if( !isset( $_GET["funiid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro funiid obrigatorio" }';
		return;
		}
	if( !isset( $_GET["manda"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro manda obrigatorio" }';
		return;
		}
	$funiid = $_GET["funiid"];
	$manda = $_GET["manda"];
	if( $manda != '0' && $manda != '1' )
		{
		echo	'{ "status": "erro", "erro": "parametro manda invalido" }';
		return;
		}
		
	$sql	=	"UPDATE BIOMETRIA.FUNI_FUNCIONARIO set FUNI_STMENS=$manda 
							WHERE FUNI_ID=$funiid";
	}

//	delfuau	-	delete FUAU
if( $qry == "delfuau" )
	{
	if( !isset( $_GET["fuauid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro fuauid obrigatorio" }';
		return;
		}
	$fuauid = $_GET["fuauid"];
	$sql	=	"DELETE FROM BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR WHERE FUAU_ID=$fuauid"; 
	}

//	update FLTR
if( $qry == "updfltr" )
	{
	if( !isset( $_GET["idfltr"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro idfltr obrigatorio" }';
		return;
		}
	if( !isset( $_GET["idlotr"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro idlotr obrigatorio" }';
		return;
		}
		
	$idfltr = $_GET["idfltr"];
	$idlotr = $_GET["idlotr"];

	$sql	=	"UPDATE BIOMETRIA.FLTR_FUNCIONARIOLOCALTRABALHO SET LOTR_ID=$idlotr
							WHERE FLTR_ID=$idfltr";
	}

//	delete FLTR
if( $qry == "delfltr" )
	{
	if( !isset( $_GET["idfltr"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro idfltr obrigatorio" }';
		return;
		}
		
	$idfltr = $_GET["idfltr"];

	$sql	=	"DELETE BIOMETRIA.FLTR_FUNCIONARIOLOCALTRABALHO 
							WHERE FLTR_ID=$idfltr";
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
//	executa o SQL
include 'ambiente.php';
include 'ORAConn.php';

if( $dbg )
	{
	echo "QUERY:$sql<br>";
	return;
	}

$ora = new ORAConn();
$res = $ora->connect($userb, $passb, $amb, $chset, "");
if( $res != "OK" )
	{
	echo $res;
	return;
	}
//	executa o query
$res = $ora->execDelUpd($sql);
echo $res;
$ora->disconnect();
