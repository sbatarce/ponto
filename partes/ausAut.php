<?php
//	ausAut( funiid, taauid, autid, iduor, dtini, dtfim, mins ):
//						cria uma nova sequencia de ausências autorizadas
//		funiid	id do funcionário autorizado
//		taauid	id tabela TAAU_TIPOAUSENCIAAUTORIZADA
//		autid		funi_id de quem esta autorizando
//		iduor		id da UOR na tabela FUOR (UOR de lotação do funcionário)
//		dtini		data inicial da sequencia de ausências
//		dtfim		data final
//		mins		minutos de ausência autorizada a cada dia
//		lib4b		libera a exigencia de 4 batidas (opcional)

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
if( !isset( $_GET["autid"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro autid obrigatorio" }';
	return;
	}
if( !isset( $_GET["iduor"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro iduor obrigatorio" }';
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
if( isset( $_GET["mins"] ) )
	$mins = $_GET["mins"];
else
	$mins = 0;

if( isset( $_GET["lib4b"] ) )
	$lib4b = "1";
else
	$lib4b = "0";

if( isset( $_GET["faauid"] ) )
	$faauid = $_GET["faauid"];
else
	$faauid = "-";

//	obtem parametros
$funiid = $_GET["funiid"];
$autid = $_GET["autid"];
$taauid = $_GET["taauid"];
$iduor = $_GET["iduor"];
$dtini = $_GET["dtini"];
$dtfim = $_GET["dtfim"];
if( $dbg )
	echo "funiid=$funiid autid=$autid taauid=$taauid iduor=$iduor dtini=$dtini dtfim=$dtfim<br>";
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
//	verifica a validade da sequencia de ausências
//	obtem a quantidade de horas normais do funcionário
$sql = "SELECT MAX(RTAT.RTAT_NITMPDIARIO) AS TMP
					FROM        BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR
					INNER JOIN  BIOMETRIA.RETR_REGIMETRABALHO RETR ON
											RETR.RETR_ID=FRTR.RETR_ID
					INNER JOIN  BIOMETRIA.RTAT_REGIMETRABALHOATRIBUTO RTAT ON
											RTAT.RETR_ID=FRTR.RETR_ID
					WHERE FRTR.FRTR_DTFIM IS NULL AND
								FRTR.FUNI_ID=$funiid";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "Acessando Regime sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Acessando Regime: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	{
	fmtErro( "erro", "Funcionário sem regime ou inexistente" );
	$ora->disconnect();
	return;
	}
$tmpdia = $jres->dados[0]->TMP;
$ora->libStmt();
	
//	obtem o tipo de ausencia
$sql = "SELECT	TAAU_STMARCACAO AS PODE, TAAU_NIVARHORAS AS HORAS
					FROM	BIOMETRIA.TAAU_TIPOAUSENCIAAUTORIZADA
					WHERE	TAAU_ID=$taauid";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "Acessando TAAU sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Acessando TAAU: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	{
	fmtErro( "erro", "Não existe este tipo de ausência autorizada" );
	$ora->disconnect();
	return;
	}
if( $jres->dados[0]->PODE == '0' )
	$pode = false;
else
	$pode = true;
$varmins = $jres->dados[0]->HORAS*60;
if( $dbg )
	echo "varmins=$varmins mins=$mins<br>";

if( $varmins > 0 )
	{
	if( $mins > 0 && $mins != $varmins )
		{
		fmtErro( "erro", "Este tipo de ausência não permite variação de horas de ausência" );
		$ora->disconnect();
		return;
		}
	else
		$mins = $varmins;
	}
else
	{
	if( $mins == 0 )
		{
		fmtErro( "erro", "Neste tipo de ausência a quantidade de horas é necessária" );
		$ora->disconnect();
		return;
		}
	}
$ora->libStmt();

//	obtem o fuauid usando id do autorizador e iduor
$sql = "SELECT  FUAU_ID AS FUAUID FROM BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR
					WHERE FUAU_DTINICIO<=SYSDATE AND 
								(FUAU_DTFIM>=SYSDATE OR FUAU_DTFIM IS NULL) AND
								FUNI_ID=$autid 
								AND PMS_IDSAUUOR=$iduor";
$res = $ora->execSelect($sql);
$jres = json_decode($res);
if( $dbg )
	{
	echo "Acessando func autorizador sql=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	fmtErro( "erro", "Acessando func autorizador: $jres->erro" );
	$ora->disconnect();
	return;
	}
if( $jres->linhas < 1 )
	{
	fmtErro( "erro", "Autorizador não existe ou não é autorizador desta uor" );
	$ora->disconnect();
	return;
	}
$fuauid = $jres->dados[0]->FUAUID;
$ora->libStmt();

//	verifica pre-existencia de ausências autorizadas neste período
if( !$pode )
	{
	//	não pode haver batidas no período deste tipo de ausência autorizada
	$sql = "SELECT count(1) AS VEZES
						FROM        BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR
						INNER JOIN  BIOMETRIA.FDTR_FUNCIONARIODIATRABALHO FDTR ON
												FDTR.FRTR_ID=FRTR.FRTR_ID
						INNER JOIN  BIOMETRIA.FDTM_FUNCDIATRABALHOMENSAGEM FDTM ON
												FDTM.FDTR_ID=FDTR.FDTR_ID
						INNER JOIN  BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE ON
												FDTE.FDTR_ID=FDTR.FDTR_ID
						WHERE FRTR.FUNI_ID=$funiid AND       
									FDTR.FDTR_DTREFERENCIA BETWEEN 
									TO_DATE( '$dtini', 'YYYYMMDD' ) AND
									TO_DATE( '$dtfim', 'YYYYMMDD' )";
	$res = $ora->execSelect($sql);
	$jres = json_decode($res);
	if( $dbg )
		{
		echo "Verificando nao pode sql=$sql/resultado:";
		var_dump($jres);
		}
	if( $jres->status != "OK" )
		{
		fmtErro( "erro", "Verificando nao pode: $jres->erro" );
		$ora->disconnect();
		return;
		}
	if( $jres->linhas > 0 )
		{
		$qtd = $jres->dados[0]->VEZES;
		if( $qtd > 0 )
			{
			fmtErro( "erro", "Há registros biométricos neste período." );
			$ora->disconnect();
			return;
			}
		}
	$ora->libStmt();
	}

//	verifica os acumulados de tempo de ausência a cada dia entre dtini e dtfim
$refdt = date_create_from_format('Ymd', $dtini );
$fimdt = date_create_from_format('Ymd', $dtfim );
$inter = new DateInterval('P1D');
if( $dbg )
	{
	var_dump($inter);
	var_dump($refdt);
	var_dump($fimdt);
	}
while( $refdt <= $fimdt )
	{
	$dt = $refdt->format('Ymd');
	$sql = "SELECT SUM( FAAU.FAAU_NITMPDIARIO ) AS TOTAL
						FROM BIOMETRIA.FAAU_FUNCAUSENCIAAUTORIZADA FAAU
						WHERE TO_DATE( '$dt', 'YYYYMMDD' ) BETWEEN
									FAAU.FAAU_DTINI AND FAAU.FAAU_DTFIM AND
									FAAU.FUNI_ID=$funiid ";
	if( $faauid != '-' )
		$sql .= "AND FAAU.FAAU_ID <> $faauid ";
	$res = $ora->execSelect($sql);
	$jres = json_decode($res);
	if( $dbg )
		{
		echo "Verificando tempo diario sql=$sql/resultado:";
		var_dump($jres);
		}
	if( $jres->status != "OK" )
		{
		fmtErro( "erro", "Verificando tempo diario: $jres->erro" );
		$ora->disconnect();
		return;
		}
	if( $jres->linhas > 0 )
		{
		$total = $jres->dados[0]->TOTAL;
		if( $total+$mins > $tmpdia )
			{
			$aux = $refdt->format('d/m/Y');
			$tot = $total+$mins;
			fmtErro( "erro",	"No dia $aux o total de ausências ($tot) ".
												"ultrapassa tempo diário ($tmpdia) " );
			$ora->disconnect();
			return;
			}
		}
	$ora->libStmt();
	$refdt->add( $inter );
	}

//	inicia a transaction
$ora->beginTransaction();

//	insere FAAU
if( $faauid == "-" )
	{
	$sql	=	"INSERT INTO BIOMETRIA.FAAU_FUNCAUSENCIAAUTORIZADA VALUES( 
						BIOMETRIA.SQ_FAAU.NEXTVAL, $funiid, $taauid, $fuauid, 
						TO_DATE( '$dtini', 'YYYYMMDD' ), 
						TO_DATE( '$dtfim', 'YYYYMMDD' ), $mins, $lib4b )";
	$res = $ora->execInsert( $sql, "BIOMETRIA.SQ_FAAU" );
	$jres	= json_decode($res);
	if( $dbg )
		{
		echo "Inserindo FAAU sql=$sql/resultado:";
		var_dump($jres);
		}
	if( $jres->status != "OK" )
		{
		echo $res;
		$ora->rollback();
		$ora->disconnect();
		return;
		}
	$faauid = $jres->idnovo;
	$ora->libStmt();
	}
else
	{
	$sql = "UPDATE BIOMETRIA.FAAU_FUNCAUSENCIAAUTORIZADA SET
						TAAU_ID=$taauid, FUAU_ID=$fuauid,
						FAAU_DTINI=TO_DATE( '$dtini', 'YYYYMMDD' ), 
						FAAU_DTFIM=TO_DATE( '$dtfim', 'YYYYMMDD' ), 
						FAAU_NITMPDIARIO=$mins, FAAU_STLIB4BATIDAS= $lib4b
						WHERE FAAU_ID=$faauid";
	$res = $ora->execDelUpd( $sql );
	$jres = json_decode( $res );
	if( $dbg )
		{
		echo "update FAAU SQL=$sql/resultado:";
		var_dump($jres);
		}
	if( $jres->status != "OK" )
		{
		fmtErro( "erro", "alterando FAAU: $jres->erro" );
		$ora->rollback();
		$ora->disconnect();
		return;
		}	
	//	remove todos os FDTF's
	$sql = "DELETE FROM BIOMETRIA.FDTF_FUNCDIATRABALHO_FAAU
						WHERE FAAU_ID=$faauid";
	$res = $ora->execDelUpd( $sql );
	$jres = json_decode( $res );
	if( $dbg )
		{
		echo "removendo FDTF SQL=$sql/resultado:";
		var_dump($jres);
		}
	if( $jres->status != "OK" )
		{
		fmtErro( "erro", "removendo FDTF: $jres->erro" );
		$ora->rollback();
		$ora->disconnect();
		return;
		}	
	}
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
if( $dbg )
	{
	echo "Inserindo FDTF SQL=$sql/resultado:";
	var_dump($jres);
	}
if( $jres->status != "OK" )
	{
	echo $res;
	$ora->rollback();
	$ora->disconnect();
	return;
	}
//	commit e encerramento
if( $dbg )
	{
	echo "{ \"status\": \"warn\", \"warn\": \"debug ativado=>rollback\", \"id\": \"$faauid\" }";
	$ora->rollback();
	}
else
	{
	echo "{ \"status\": \"OK\", \"id\": \"$faauid\" }";
	$ora->commit();
	}
$ora->disconnect();
