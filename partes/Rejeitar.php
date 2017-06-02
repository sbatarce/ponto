<?php
if( !isset( $_GET["fdtrid"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro fdtrid obrigatorio" }';
	return;
	}
$fdtrid = $_GET["fdtrid"];

include 'ambiente.php';
include 'ORAConn.php';

$ora = new ORAConn();
$res = $ora->connect($userb, $passb, $amb, $chset);
if( $res != "OK" )
	{
	echo $res;
	return;
	}
//	obter todas as batidas que serão excluidas
$sql	=	"SELECT LISTAGG(TO_CHAR( FDTE.FDTE_DTHORARIO, 'HH24:MI' ), ';' ) 
                      WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO) AS HORARIOS
          FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
          WHERE FDTE.FDTR_ID=$fdtrid and FDTE.TORE_ID <> 1";

$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $jres->status != "OK" )
	{
	$ora->disconnect();
	echo "{ \"status\": \"erro\", \"erro\":\"$jres->erro\", \"local\":\"select de presencas\" }";
	return;
	}
if( $jres->linhas == 0 )
	{
	$hors = "";
	}
else
	{
	$hors = $jres->dados[0]->HORARIOS; 
	}
$ora->libStmt();

//	obter as mensagens pre-existentes
$sql = "SELECT FDTM_DLMENS AS MENS FROM BIOMETRIA.FDTM_FUNCDIATRABALHOMENSAGEM 
					WHERE FDTR_ID = $fdtrid";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $jres->status != "OK" )
	{
	$ora->disconnect();
	echo "{ \"status\": \"erro\", \"erro\":\"$jres->erro\", \"local\":\"select de mensagens\" }";
	return;
	}
if( $jres->linhas == 0 )
	{
	$mens = "";
	$temMens = false;
	}
else
	{
	$mens = $jres->dados[0]->MENS;
	$temMens = true;
	}
$data = new Datetime();
$str = $data->format( 'h:i');
	
$mens .= "A $str Mantidos horários da Biometria. Excluidos os demais: $hors";
$ora->libStmt();

//	inicia a transação
$ora->beginTransaction();

//	atualiza o diálogo
if( $temMens )
	{
	$sql = "UPDATE BIOMETRIA.FDTM_FUNCDIATRABALHOMENSAGEM 
					SET FDTM_DLMENS='$mens', FDTM_DTMENS=sysdate, FDTM_TPFUNCAUT='A'
					WHERE FDTR_ID=$fdtrid";
	$res = $ora->execDelUpd($sql);
	}
else
	{
	$sql = "INSERT INTO BIOMETRIA.FDTM_FUNCDIATRABALHOMENSAGEM 
						VALUES( BIOMETRIA.SQ_FDTM.NEXTVAL, $fdtrid, 'A', 
						'$mens', sysdate, 0 )";
	$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FDTM" );
	}
$jres = json_decode($res);
if( $jres->status != "OK" )
	{
	$ora->rollback();
	$ora->disconnect();
	echo "{ \"status\": \"erro\", \"erro\":\"inserindo ou atualizando diálogo: $jres->erro\" }";
	return;
	}
$ora->libStmt();

//	insere uma mensagem padrão na FDTN
$sql = "INSERT INTO BIOMETRIA.FDTN_FDTR_TMEN
					VALUES( BIOMETRIA.SQ_FDTN.NEXTVAL, $fdtrid, 30 )";
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FDTN" );
$jres = json_decode($res);
if( $jres->status != "OK" )
	{
	$ora->rollback();
	$ora->disconnect();
	echo "{ \"status\": \"erro\", \"erro\":\"inserindo mensagem catalogada: $jres->erro\" }";
	return;
	}
$ora->libStmt();

//	remove todas presenças não biometrias devido negação
$sql	=	"DELETE FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO
					WHERE FDTR_ID=$fdtrid AND TORE_ID <> 1";
	
$res = $ora->execDelUpd($sql);
if( $jres->status != "OK" )
	{
	$ora->rollback();
	$ora->disconnect();
	echo "{ \"status\": \"erro\", \"erro\":\"inserindo mensagem catalogada: $jres->erro\" }";
	return;
	}
$ora->libStmt();

//	desexcluir as eventuais presenças excluidas
$sql	=	"UPDATE BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO SET TORG_ID=NULL
						WHERE FDTR_ID = $fdtrid AND TORG_ID=2";
$res = $ora->execDelUpd($sql);
if( $jres->status != "OK" )
	{
	$ora->rollback();
	$ora->disconnect();
	echo "{ \"status\": \"erro\", \"erro\":\"desexcluindo presenças excluidas: $jres->erro\" }";
	return;
	}
$ora->libStmt();

echo "{ \"status\": \"OK\" }";
$ora->commit();
//$ora->rollback();
$ora->disconnect();
