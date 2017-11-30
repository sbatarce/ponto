<?php
//	ausAutRem( faauid ):
//						remove uma sequencia de ausência autorizada
//		faauid	id da sequencia a remover

//	remove as FDTF com FAAU_ID=faauid
//	remove as FAAU com FAAU_ID=faauid

include '../partes/fmtErro.php';
include 'ambiente.php';
include 'ORAConn.php';

if( isset( $_GET["dbg"]) )
	$dbg = true;
else
	$dbg = false;
//	prepara o query
$sql	=	"";
//	verifica parametros
if( !isset( $_GET["faauid"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro faauid obrigatorio" }';
	return;
	}
	
//	obtem parametros
$faauid = $_GET["faauid"];
//	prepara a conexão
$ora = new ORAConn();
$res = $ora->connect($userb, $passb, $amb, $chset, "");
if( $dbg )
	var_dump( $res );
if( $res != "OK" )
	{
	echo $res;
	return;
	}
//	remove todas as FDTFs 
$sql = "DELETE FROM BIOMETRIA.FDTF_FUNCDIATRABALHO_FAAU
					WHERE FAAU_ID=$faauid";
$res = $ora->execDelUpd( $sql );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "remove FDTF SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Iniciando a alocacao: $jres->erro" );
	$ora->rollback();
	$ora->disconnect();
	return;
	}	
//	remove todas as FAAUs 
$sql = "DELETE FROM BIOMETRIA.FAAU_FUNCAUSENCIAAUTORIZADA
					WHERE FAAU_ID=$faauid";
$res = $ora->execDelUpd( $sql );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "remove FDTF SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Iniciando a alocacao: $jres->erro" );
	$ora->rollback();
	$ora->disconnect();
	return;
	}	
//	commit e encerramento
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
