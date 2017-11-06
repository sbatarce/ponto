<?php
//	fecha funcionário
//	funiid	-	FUNI_ID
//	data		-	data do fechamento

if( !isset( $_GET["funiid"] ) )
	{
	echo	'{ "data": [{"erro": "parametro funiid obrigatório"}] }';
	return;
	}
$funiid = $_GET["funiid"];

//	verifica parametro reginovo = ID do novo regime
if( !isset( $_GET["data"] ) )
	{
	echo	'{ "data": [{"erro": "parametro data obrigatório"}] }';
	return;
	}
$data = $_GET["data"];
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
	
//	verifica o fechamento do funcionário
$sql = "SELECT FSHM_ID AS FSHMID, TO_CHAR( FSHM_DTREFERENCIA, 'YYYYMMDD' ) AS DTUFECH
					FROM BIOMETRIA.FSHM_FUNCSALDOHORAMENSAL 
					WHERE FUNI_ID==$funiid";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "verifica UOR sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Acessando FSHM: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	{
	fmtErro( "erro", "ID do Funcionario nao existe" );
	$ora->disconnect();
	return;
	}
$dtufch = $jres->dados[0]->DTUFECH;
$ora->libStmt();

//	verifica se há pendências desde a data do fechamento até a nova data
$sql = "SELECT COUNT(1) AS QTD
					FROM BIOMETRIA.FDTR_FUNCIONARIODIATRABALHO FDTR
					INNER JOIN  BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR ON
											FRTR.FRTR_ID=FDTR.FRTR_ID AND
											FRTR.FRTR_DTFIM IS NULL
					WHERE (FDTR.TSDT_ID=1 OR FDTR.TSDT_ID=4) AND
								FDTR.FDTR_DTREFERENCIA BETWEEN 
										TO_DATE( '$dtufech', 'YYYYMMDD' ) AND 
										TO_DATE( '$data', 'YYYYMMDD' ) AND
								FRTR.FUNI_ID=$funiid";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "resultado verifica pendencias sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Obtendo pendencias: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	{
	fmtErro( "erro", "Retornou 0 pendencias" );
	$ora->disconnect();
	return;
	}
$qtpend = $jres->dados[0]->QTD;
if( $dbg )
	echo "QTD pendencias=$qtpend<br>";
$ora->libStmt();
if( $qtpend > 0 )
	{
	fmtErro( "erro", "Funcionário tem pendencias neste período" );
	$ora->disconnect();
	return;
	}
	
//	calcula os saldos no dia do fechamento para compor o novo FSHM

//	inicia uma transação 
$ora->beginTransaction();

//	atualiza o FSHM antigo
	$sql = "UPDATE BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO 
						SET FRTR_DTFIM=SYSDATE
						WHERE FRTR_ID=$idregiant";
	$res = $ora->execDelUpd($sql);
	$jres = json_decode( $res );
	if( $dbg )
		{
		echo "update FUOR anterio SQL=$sql/resultado:";
		var_dump($jres);
		}
	if( $jres->status != "OK" )
		{
		fmtErro( "erro", "Encerrando alocacao anterior: $jres->erro" );
		$ora->rollback();
		$ora->disconnect();
		return;
		}	
	}
	
//	cria a nova alocação
$sql = "INSERT INTO BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO VALUES
					( BIOMETRIA.SQ_FRTR.NEXTVAL, $funiid, $reginovo, null, 
						SYSDATE+1, null )";
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FRTR" );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "insere o novo regime SQL=$sql / resultado:";
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
