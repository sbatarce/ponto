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
					WHERE FUNI_ID=$funiid
					ORDER BY FSHM_DTREFERENCIA DESC";
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
$dtufech = $jres->dados[0]->DTUFECH;
$fshmid =  $jres->dados[0]->FSHMID;
$ora->libStmt();
if( $dbg )
	{
	echo "Ultimo fechamento=$dtufech / id fshm=$fshmid<br>";
	}

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
	
//	obtem saldo e médias no dia do novo fechamento para compor o novo FSHM
$sql = "SELECT  BIOMETRIA.SF_CALCULASALDOINICIAL( $funiid, TO_DATE('$data','YYYYMMDD')) AS SALDO,
								NIQUANTIDADEHORARIO1 AS QTDENTRA, NISOMAHORARIO1 AS MEDENTRA, 
								NIQUANTIDADEHORARIO2 AS QTDINTER, NISOMAHORARIO2 AS MEDINTER, 
								NIQUANTIDADEHORARIO3 AS QTDVOLTA, NISOMAHORARIO3 AS MEDVOLTA, 
								NIQUANTIDADEHORARIO4 AS QTDSAIDA, NISOMAHORARIO4 AS MEDSAIDA
					FROM	TABLE(BIOMETRIA.SF_CALCULAMEDIAHORARIOBATIDAS
										($funiid, TO_DATE( '$dtufech', 'YYYYMMDD' ), 
															TO_DATE( '$data', 'YYYYMMDD' )))";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "resultado obtem medias sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Obtendo medias: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	{
	fmtErro( "erro", "Retornou 0 linhas de media" );
	$ora->disconnect();
	return;
	}
$ora->libStmt();

//	inicia uma transação 
$ora->beginTransaction();
//	cria o FSHM novo
$sql = "INSERT INTO BIOMETRIA.FSHM_FUNCSALDOHORAMENSAL VALUES
					( BIOMETRIA.SQ_FSHM.NEXTVAL, $funiid, TO_DATE( '$data', 'YYYYMMDD' ), ".
						$jres->dados[0]->SALDO.", 0, 0, ".
						$jres->dados[0]->QTDENTRA.", ".$jres->dados[0]->MEDENTRA.",".
						$jres->dados[0]->QTDINTER.", ".$jres->dados[0]->MEDINTER.",".
						$jres->dados[0]->QTDVOLTA.", ".$jres->dados[0]->MEDVOLTA.",".
						$jres->dados[0]->QTDSAIDA.", ".$jres->dados[0]->MEDSAIDA." )";
$res = $ora->execInsert($sql, 'BIOMETRIA.SQ_FSHM' );
$jres = json_decode( $res );
if( $dbg )
	{
	echo "INSERT de FSHM SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Inserindo FSHM: $jres->erro" );
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
