<?php
//	trocaUorFunc( funiid, uornova, dtefet )
//	Verifica parametro FUNI_ID
if( !isset( $_GET["dbg"] ) )
	$dbg=false;
else
	$dbg=true;

if( !isset( $_GET["funiid"] ) )
	{
	echo	'{ "data": [{"erro": "parametro funiid obrigatório"}] }';
	return;
	}
$funiid = $_GET["funiid"];

//	verifica parametro uornova = ID da nova UOR do funcionário
if( !isset( $_GET["uornova"] ) )
	{
	echo	'{ "data": [{"erro": "parametro uornova obrigatório"}] }';
	return;
	}
$uornova = $_GET["uornova"];

//	verifica parametro uornova = ID da nova UOR do funcionário
if( !isset( $_GET["dtefet"] ) )
	{
	echo	'{ "data": [{"erro": "parametro dtefet obrigatório"}] }';
	return;
	}
$dtefet = $_GET["dtefet"];
if( $dbg )
	{
	echo "funiid=$funiid/uornova=$uornova/dtefet=$dtefet/<br>";
	}

if( strlen( $dtefet ) != 8 )
	{
	echo	'{ "data": [{"erro": "parametro dtefet invalido (YYYYMMDD)"}] }';
	return;
	}

$ano = substr( $dtefet, 0, 4 );
$mes = substr( $dtefet, 4, 2 );
$dia = substr( $dtefet, 6, 2 );
$dt = new DateTime();
try
	{
	$dt->setDate($ano, $mes, $dia);
	} 
catch (Exception $ex) 
	{
	echo	'{ "data": [{"erro": "parametro dtefet invalido (YYYYMMDD)"}] }';
	return;
	}

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
$role = "";
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
	
//	verifica se a nova UOR existe no SAU
$sql = "SELECT UOR_DTINICIAL from SAU.VWUORPUBLICA 
					WHERE UOR_DTFINAL IS NULL AND
								UOR_IDUNIDADEORGANIZACIONAL=$uornova";
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
	fmtErro( "erro", "Esta UOR não existe" );
	$ora->disconnect();
	return;
	}
$ora->libStmt();

//	obtem a data do dia seguinte ao último fechamento no formato YYYYMMDD
$sql = "SELECT TO_CHAR( MAX(FSHM_DTREFERENCIA)+1, 'YYYYMMDD' ) as POSFECH
					FROM BIOMETRIA.FSHM_FUNCSALDOHORAMENSAL
					WHERE FUNI_ID=$funiid";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "verifica fechamento sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "verifica fechamento: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas > 0 )
	{
	}
$ora->libStmt();
$posfech = $jres->dados[0]->POSFECH;
if( $dbg )
	echo "dia seguinte ao fechamento=$posfech<br>";

//	
if( $posfech < $dtefet )
	{
	fmtErro( "erro", "a data de transferencia de UOR deve estar em um período fechado" );
	$ora->disconnect();
	return;
	}

//	verifica se houve interferencias do autorizador após a data de efetivaçãp
$sql = "SELECT COUNT(1) AS QTINTER
					FROM BIOMETRIA.FDTR_FUNCIONARIODIATRABALHO FDTR
					INNER JOIN  BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR ON
											FRTR.FRTR_ID=FDTR.FRTR_ID
					WHERE FRTR.FUNI_ID=$funiid AND
								FDTR.FDTR_DTREFERENCIA >= TO_DATE( '$dtefet', 'YYYYMMDD' ) AND
								FDTR.TSDT_ID BETWEEN 2 and 3";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "verifica interferencias sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "verifica interferencias: $jres->erro" );
	$ora->disconnect();
	return;
	}
$ora->libStmt();
$qtinter = $jres->dados[0]->QTINTER;
if( $qtinter > 0 )
	{
	fmtErro( "erro", "ha interferencias do autorizador atual após a data de efetivação" );
	$ora->disconnect();
	return;
	}

//	obtem o ID de uma eventual FUOR aberta para o funcionário
$sql = "SELECT	FUOR_ID, PMS_IDSAUUOR, 
								TO_CHAR( FUOR_DTINICIO, 'YYYYMMDD' ) AS INI
					FROM	BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL
					WHERE	FUOR_DTFIM IS NULL AND
								FUNI_ID=$funiid";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "resultado verifica FUOR anterior sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Obtendo alocacao anterior: $jres->erro" );
	$ora->disconnect();
	return;
	}
$idfuorant = -1;
$iniant = '';
if( $jres->linhas > 0 )
	{
	$dado = $jres->dados[0];
	if( $dbg )
		var_dump( $dado );
	$idfuorant = $dado->FUOR_ID;
	$iniant = $dado->INI;
	}
if( $dbg )
	echo "uor anterior=$idfuorant inicio anterior=$iniant<br>";
$ora->libStmt();

//	inicia uma transação 
$ora->beginTransaction();

//	fecha a eventual FUOR atual
if( $dbg )
	echo "id da uor anterior: $idfuorant<br>";
if( $idfuorant >= 0 )
	{
	if( $iniant != $dtefet )
		{
		$sql = "UPDATE BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL 
							SET FUOR_DTFIM=TO_DATE( '$dtefet', 'YYYYMMDD' ) -1
							WHERE FUOR_ID=$idfuorant";
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
	else
		{
		$sql = "UPDATE	BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL 
							SET		FUOR_DTFIM=NULL,
										PMS_IDSAUUOR=$uornova
							WHERE FUOR_ID=$idfuorant";
		$res = $ora->execDelUpd($sql);
		$jres = json_decode( $res );
		if( $dbg )
			{
			echo "Substitui FUOR anterior SQL=$sql/resultado:";
			var_dump($jres);
			}
		if( $jres->status != "OK" )
			{
			fmtErro( "erro", "Substituindo alocacao anterior: $jres->erro" );
			$ora->rollback();
			$ora->disconnect();
			return;
			}
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
		return;
		}
	}
	
//	cria a nova alocação
$sql = "INSERT INTO BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL VALUES
				  ( BIOMETRIA.SQ_FUOR.NEXTVAL, $funiid, $uornova, 
						TO_DATE( '$dtefet', 'YYYYMMDD' ), null )";
$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FUOR" );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "insere a nova alocação SQL=$sql/resultado:";
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
