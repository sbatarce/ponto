<?php
//	seleçao no formato adequado para SELECT2 (combo box web)
//		selectData.php?query=xpto
//			parametros opcionais
//				semzero		-	não gerar o { "id": "0", "text": "Escolha abaixo:" }
//				maisum e numais - gerar { "id": "$numais", "text": "$maisum" }
//
//	candidauto	- candidatos a autorizador de uma fuor
//	fuors				-	todas as UORS (fuors) de um autorizador ou todas UORS (fuors)
//	funcsauto		-	funcionários de um autorizador
//	funcsshd		-	todos os funcionários que tenham SSHD
//	funcfuni		- todos os funcionários na funi
//	regimes			-	regimes de trabalho
//	aparelhos		- lista de todos os aparelhos associados ao PONTO siin=100 
//	uor					- todas as UORS do sau
//	funcfuor		- funcionários na funi pertencentes a uma FUOR
//	funcsuor		- funcionários na funi pertencentes a uma UOR do SAU
//	uorsaut			- uors que um autorizador autoriza
//	tiaus				- tipos de ausencias autorizadas

function toHex($string)
	{
	$hex='';
	for ($i=0; $i < strlen($string); $i++)
		{
		echo $string[$i].":".dechex(ord($string[$i]))."<br>";
		}
	return $hex;
	}

header('Content-Type: text/html; charset=utf-8');

if( !isset( $_GET["query"] ) )
	{
	echo '[ {"id": "00", "text": "parametro query obrigatorio" } ]';
	return;
	}
	
if( isset( $_GET["semzero"] ) )
	$sem0 = true;
else
	$sem0 = false;

if( isset( $_GET["comtodos"] ) )
	$comt = true;
else
	$cont = false;

if( isset( $_GET["maisum"] ))
	{
	$maisum = $_GET["maisum"];
	if( !isset( $_GET["numais"] ) )
		{
		echo '[ {"id": "00", "text": "parametro numais obrigatorio com maisum" } ]';
		return;
		}
	$numais = $_GET["numais"];
	}
else
	{
	$maisum = "";
	$numais = -1;
	}

//	prepara o select
$sql	=	"";

if( isset( $_GET["dbg"] ) )
	$dbg	=	true;
else
	$dbg = false;

$qry = $_GET["query"];

////////////////////////////////////////////////////////////////////////////////
//	
if( $qry == "xpto" )
	{
	if( !isset( $_GET["sshd"] ) )
		{
		echo '[ {"id": "00", "text": "parametro sshd obrigatorio" } ]';
		return;
		}
	$sshd	=	$_GET["sshd"];

	$sql	=	"";
	}
	
////////////////////////////////////////////////////////////////////////////////
//	
if( $qry == "naosim" )
	{
	echo '[ {"id":"0", "text":"nao"},{"id": "1", "text": "sim"} ]';
	return;
	}

////////////////////////////////////////////////////////////////////////////////
//	
if( $qry == "dbcr" )
	{
	echo '[ {"id":"0", "text":"Escolha abaixo:"},{"id": "1", "text": "DB"},{"id": "2", "text": "CR"} ]';
	return;
	}

////////////////////////////////////////////////////////////////////////////////
//	candidauto	- candidatos a autorizador de uma fuor
//	
if( $qry == "candidauto" )
	{
	if( !isset( $_GET["fuorid"] ) )
		{
		echo '[ {"id": "00", "text": "parametro fuorid obrigatorio" } ]';
		return;
		}
	$fuorid	=	$_GET["fuorid"];

	$sql =	"SELECT	FUAT.FUNI_ID, 
									FUAT.IUN || '-' || FUAT.NOME || ' - ' || FUAT.UOR_DLSIGLAUNIDADE ||
									CASE 
										WHEN FUAT.DSSIMBOLO IS NOT NULL THEN ' - ' || FUAT.DSSIMBOLO 
										ELSE '' 
										END AS DLNOMEFUNC
						FROM  (SELECT DISTINCT FUNI.FUNI_ID, FUNI.PMS_IDPMSPESSOA, 
													VFAT.NOME, VFAT.IUN, VUPU.UOR_DLSIGLAUNIDADE, VVNE.DSSIMBOLO,
													CASE 
														WHEN  FUOR.PMS_IDSAUUOR = $fuorid THEN '   ' || 
																	VUPU.UOR_DLSIGLAUNIDADE 
														ELSE VUPU.UOR_DLSIGLAUNIDADE 
														END AS SGORDEM
											FROM      BIOMETRIA.FUNI_FUNCIONARIO FUNI
											LEFT JOIN BIOMETRIA.VWFUNCIONARIOATIVO VFAT ON
																VFAT.IUN = FUNI.PMS_IDPMSPESSOA
											LEFT JOIN BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL FUOR ON
																SYSDATE BETWEEN 
																	FUOR.FUOR_DTINICIO AND 
																	NVL(FUOR.FUOR_DTFIM, TO_DATE('31/12/3000', 'DD/MM/YYYY')) AND
																FUOR.FUNI_ID = FUNI.FUNI_ID
											LEFT JOIN SAU.VWUORPUBLICA VUPU ON
																VUPU.UOR_IDUNIDADEORGANIZACIONAL = FUOR.PMS_IDSAUUOR
											LEFT JOIN SIGEP.VWVAGASNIVELESPECIAL VVNE ON
																VUPU.UOR_IDUNIDADEORGANIZACIONAL = $fuorid AND
																( VVNE.CDCENTROCUSTO = 
																	REPLACE(REPLACE(VUPU.UOR_CDCENTROCUSTO, '.', ''), ',', '') OR
																VVNE.SGUNIDADEORGANIZACIONAL = VUPU.UOR_DLSIGLAUNIDADE) AND
																VFAT.REGISTRO_FUNCIONAL = VVNE.NRREGISTROFUNCIONAL) FUAT
						ORDER BY FUAT.DSSIMBOLO, FUAT.SGORDEM, FUAT.NOME";
	}

////////////////////////////////////////////////////////////////////////////////
//	fuors			-	todas as UORS (fuors) de um autorizador ou todas UORS (fuors)
//							data fim do autorizador deve ser nula ou não mostra nada
//			sshd dado:
//				obtem todas a UORS que o autorizador autoriza
//			sshd omitido:
//				obtem todas as UORS contantes da FUOR
if( $qry == "fuors" )
	{
	if( isset( $_GET["sshd"] ) )
		{
		$sshd	=	$_GET["sshd"];
		$sql	=	"SELECT  PUOR.UOR_IDUNIDADEORGANIZACIONAL, PUOR.UOR_DLSIGLAUNIDADE
							FROM        BIOMETRIA.FUNI_FUNCIONARIO FUNIA
							INNER JOIN  BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAU ON 
													SYSDATE BETWEEN 
														FUAU.FUAU_DTINICIO AND 
														NVL(FUAU.FUAU_DTFIM, TO_DATE('31/12/3000', 'DD/MM/YYYY')) AND 
													FUAU.FUNI_ID = FUNIA.FUNI_ID 
							INNER JOIN  SAU.VWUORPUBLICA PUOR ON 
													PUOR.UOR_IDUNIDADEORGANIZACIONAL=FUAU.PMS_IDSAUUOR
							WHERE FUNIA.PMS_IDPMSPESSOA='$sshd'";
		}
	else
		{
		$sql = "SELECT DISTINCT UOR_IDUNIDADEORGANIZACIONAL, UOR_DLSIGLAUNIDADE
							FROM	SAU.VWUORPUBLICA
							WHERE	UOR_DTFINAL IS NULL AND
										UOR_STATIVO=1							
							ORDER BY	UOR_DLSIGLAUNIDADE";
		}
	}

////////////////////////////////////////////////////////////////////////////////
//	funcsauto( sshd do atutorizador )
if( $qry == "funcsauto" )
	{
	if( !isset( $_GET["sshd"] ) )
		{
		echo '[ {"id": "00", "text": "parametro sshd obrigatorio" } ]';
		return;
		}
	$sshd	=	$_GET["sshd"];

	$sql	=	"SELECT	FUNI.FUNI_ID AS IDFUNC, 
									(SELECT IUN || '-' || NOME FROM SAU.VWPESSOA_SSHD
										WHERE	REGISTRO_FUNCIONAL_ATIVO = 1 AND 
													IUN = FUNI.PMS_IDPMSPESSOA AND 
													ROWNUM = 1) AS NOFUNC
						FROM        BIOMETRIA.FUNI_FUNCIONARIO FUNIA
						INNER JOIN  BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAU ON 
												SYSDATE BETWEEN  FUAU.FUAU_DTINICIO AND 
												NVL(FUAU.FUAU_DTFIM, TO_DATE('31/12/3000', 'DD/MM/YYYY')) AND 
												FUAU.FUNI_ID = FUNIA.FUNI_ID 
						INNER JOIN  BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL FUOR ON 
												SYSDATE BETWEEN FUOR.FUOR_DTINICIO AND 
												NVL(FUOR.FUOR_DTFIM, TO_DATE('31/12/3000', 'DD/MM/YYYY')) AND 
												FUOR.PMS_IDSAUUOR = FUAU.PMS_IDSAUUOR AND 
												FUOR.FUNI_ID <> FUNIA.FUNI_ID    
						INNER JOIN BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
											 FUNI.FUNI_ID = FUOR.FUNI_ID 
						WHERE	FUNIA.PMS_IDPMSPESSOA = '$sshd' 
						ORDER BY NOFUNC";
	}

////////////////////////////////////////////////////////////////////////////////
//	funcfuni - todos os funcionários na funi
if( $qry == "funcfuni" )
	{
	$sql	=	"SELECT  FUNI.PMS_IDPMSPESSOA AS SSHD, PESS.NOME AS NOME
							FROM        BIOMETRIA.FUNI_FUNCIONARIO FUNI
							INNER JOIN  SAU.VWPESSOA_SSHD PESS ON 
													PESS.IUN=FUNI.PMS_IDPMSPESSOA AND
													PESS.REGISTRO_FUNCIONAL_ATIVO = 1
							WHERE FUNI.FUNI_STATIVO=1";
	}

////////////////////////////////////////////////////////////////////////////////
if( $qry == "regimes" )
	{
	$sql	=	"SELECT RETR_ID, RETR_DLNOME FROM  BIOMETRIA.RETR_REGIMETRABALHO";
	}

////////////////////////////////////////////////////////////////////////////////
//	aparelhos - lista de todos os aparelhos associados ao PONTO siin=100 
if( $qry == "aparelhos" )
	{
	$sql	=	"SELECT APAL.APAL_ID, VUPU.UOR_DLSIGLAUNIDADE || 
									'-' || APAL.APAL_DLLOCALIZACAO
							FROM BIOMETRIA.AASI_APARELHOALOCACAO_SIIN AASI
							INNER JOIN	BIOMETRIA.APAL_APARELHOALOCACAO APAL ON
													APAL.APAL_DTFIM IS NULL AND  -- É DESNECESSÁRIO E DEPOIS DEVEMOS TIRAR
													APAL.APAL_ID = AASI.APAL_ID
							INNER JOIN	BIOMETRIA.LOTR_LOCALTRABALHO LOTR ON
													LOTR.APAL_ID = APAL.APAL_ID
							INNER JOIN  SAU.VWUORPUBLICA VUPU ON
													VUPU.UOR_IDUNIDADEORGANIZACIONAL = APAL.PMS_IDSAUUOR 
							WHERE			AASI.AASI_STEMUSO = 1 AND
												AASI.SIIN_ID = 100
							ORDER BY	VUPU.UOR_DLSIGLAUNIDADE, APAL.APAL_DLLOCALIZACAO";
	
	
	}

//	uor lista de UORS - todas as UORS do sau
if( $qry == "uor" )
	{
	$sql	=	"SELECT UOR_IDUNIDADEORGANIZACIONAL, UOR_DLSIGLAUNIDADE AS NOME 
						FROM SAU.VWUORPUBLICA 
						WHERE EMP_IDEMPRESA = 1 AND UOR_DTFINAL IS NULL";
	}
	
//	funcfuor - funcionários na funi pertencentes a uma FUOR
if( $qry == "funcfuor" )
	{
	if( !isset( $_GET["uor"] ) )
		{
		echo '[ {"id": "00", "text": "parametro uor obrigatorio" } ]';
		return;
		}
	$uor	=	$_GET["uor"];

	$sql	=	"SELECT  FUNI.PMS_IDPMSPESSOA AS SSHD, PESS.NOME AS NOME
							FROM        BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL FUOR
							INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
													FUNI.FUNI_ID=FUOR.FUNI_ID AND
													FUNI.FUNI_STATIVO=1
							INNER JOIN  SAU.VWPESSOA_SSHD PESS ON 
													PESS.IUN=FUNI.PMS_IDPMSPESSOA AND
													PESS.REGISTRO_FUNCIONAL_ATIVO = 1
							WHERE FUOR.PMS_IDSAUUOR=$uor";
	}

//	funcsuor - funcionários na funi pertencentes a uma UOR
if( $qry == "funcsuor" )
	{
	if( !isset( $_GET["uor"] ) )
		{
		echo '[ {"id": "00", "text": "parametro uor obrigatorio" } ]';
		return;
		}
	$uor	=	$_GET["uor"];

	$sql	=	"SELECT FUNI.FUNI_ID, PESS.NOME 
							FROM        BIOMETRIA.FUNI_FUNCIONARIO FUNI
							INNER JOIN  SAU.VWPESSOA_SSHD PESS on 
													PESS.IUN=FUNI.PMS_IDPMSPESSOA AND
													PESS.REGISTRO_FUNCIONAL_ATIVO = 1
							WHERE PESS.IDUOR_ATUAL=$uor
							ORDER BY PESS.NOME";
	}
	
//	uorsaut - uors que um autorizador autoriza
if( $qry == "uorsaut" )
	{
	if( !isset( $_GET["sshd"] ) )
		{
		echo '[ {"id": "00", "text": "parametro sshd obrigatorio" } ]';
		return;
		}
	$sshd	=	$_GET["sshd"];

	$sql	=	"SELECT FUAU.PMS_IDSAUUOR, SUOR.UOR_DLSIGLAUNIDADE
						FROM        BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAU
						INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
												FUNI.FUNI_ID = FUAU.FUNI_ID
						INNER JOIN  SAU.VWUORPUBLICA SUOR ON
												SUOR.UOR_IDUNIDADEORGANIZACIONAL=FUAU.PMS_IDSAUUOR
						WHERE FUNI.PMS_IDPMSPESSOA='$sshd'";
	}
	
//	tiaus - tipos de ausencias autorizadas
if( $qry == "tiaus" )
	{
	$sql	=	"SELECT TAAU_ID, INITCAP(TAAU_DLAUSENCIAAUTORIZADA)
							FROM  BIOMETRIA.TAAU_TIPOAUSENCIAAUTORIZADA
							ORDER BY TAAU_DLAUSENCIAAUTORIZADA";
	}
	
//
include 'ambiente.php';
if( isset( $_SERVER['PHP_AUTH_USER'] ) )
	{
	$userb = $_SERVER['PHP_AUTH_USER'];
	$passb = $_SERVER['PHP_AUTH_PW'];
	}
	
if( $dbg )
	echo "user=$userb<br>";

include 'ORAConn.php';
//	obre o oracle
$ora = new ORAConn();
$res = $ora->connect( $userb, $passb, $amb, $chset, "" );
if( $res != "OK" )
	{
	echo $res;
	return;
	}
//	executa o query
if( $dbg )
	echo "SQL=$sql<br>\r\n";
$res = $ora->execSelect($sql);
if( $dbg )
	{
	$alf = trim( preg_replace("/([\\x00-\\x1f])/e", "", $res) );
	echo "alfa: $alf<br>";
	//toHex($alf);
	echo "<br>\r\n";
	}
//	monta a resposta { "data": ["cmp": "val"
$jsres	= json_decode(preg_replace("/([\\x00-\\x1f])/e", "", $res));
if( $dbg )
	{
	echo "=================json convertido======================<br>\r\n";
	var_dump( $jsres );
	echo "<br>\r\n";
	}
$qtcmp	=	$jsres->linhas;
if( $dbg )
	echo "linhas=$qtcmp<br>\r\n";

$data = '[ ';
if( !$sem0 )
	$data		.=	'{"id":"0", "text":"Escolha abaixo"}';

if( $numais >= 0 )
	{
	if( strlen( $data ) > 3 )
		$data	.=	",";
	$data		.=	"{\"id\":\"$numais\", \"text\":\"$maisum\"}";
	}
	
for( $irec=0; $irec<$qtcmp; $irec++ )
	{
	$col = 0;
	foreach( $jsres->dados[$irec] as $nom=>$val )
		{
		if( $col == 1 )
			$rec 	= "\"id\": \"$val\"";
		if( $col == 2 )
			{
			$rec 	.= ", \"text\": \"$val\"";
			break;
			}
		$col++;
		}
	if( strlen( $data ) > 3 )
		$data	.=	",";
	$data	.=	"{".$rec."}";
	}
$data	.=	"]";
echo $data;
$ora->disconnect();
