<?php
//	seleçao no formato adequado para SELECT2 (combo box web)
//		selectData.php?query=xpto
//			parametros opcionais
//				semzero		-	não gerar o { "id": "0", "text": "Escolha abaixo:" }
//				maisum e numais - gerar { "id": "$numais", "text": "$maisum" }
//				
//	aparelhos - lista de aparelhos associados ao PONTO siin=100
//	uor - lista de todas as UORS do sau
//	funcfuni	-	todos os funcionários na funi
//	funcsuor - funcionários na funi pertencentes a uma UOR
//	funcfuor - funcionário pertencentes a uma FUOR
//	funcapar - funcionários em um aparelho
//	uorsaut - uors que um autorizador autoriza
//	tiaus - tipos de ausencias autorizadas

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

if( !isset( $_GET["query"] ) && !isset( $_GET["debug"] ) )
	{
	echo '[ {"id": "00", "text": "parametro query obrigatorio" } ]';
	return;
	}
if( isset( $_GET["semzero"] ) )
	$sem0 = true;
else
	$sem0 = false;

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
$debug = false;

if( isset( $_GET["query"] )  )
	$qry = $_GET["query"];
else
	{
	$qry = $_GET["debug"];
	$debug = true;
	}
	
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
//	
if( $qry == "regimes" )
	{
	$sql	=	"SELECT RETR_ID, RETR_DLNOME FROM  BIOMETRIA.RETR_REGIMETRABALHO";
	}

////////////////////////////////////////////////////////////////////////////////
//	aparelhos - lista de aparelhos associados ao PONTO siin=100
if( $qry == "aparelhos" )
	{
	$sql	=	"SELECT APAL.APAL_ID, VUPU.UOR_DLSIGLAUNIDADE || 
									'-' || APAL.APAL_DLLOCALIZACAO
							FROM BIOMETRIA.AASI_APARELHOALOCACAO_SIIN AASI
							INNER JOIN BIOMETRIA.APAL_APARELHOALOCACAO APAL ON
										APAL.APAL_DTFIM IS NULL AND  -- É DESNECESSÁRIO E DEPOIS DEVEMOS TIRAR
										APAL.APAL_ID = AASI.APAL_ID
							INNER JOIN BIOMETRIA.LOTR_LOCALTRABALHO LOTR ON
										LOTR.APAL_ID = APAL.APAL_ID
							INNER JOIN  SAU.VWUORPUBLICA VUPU ON
											VUPU.UOR_IDUNIDADEORGANIZACIONAL = APAL.PMS_IDSAUUOR 
							WHERE AASI.AASI_STEMUSO = 1 AND
										AASI.SIIN_ID = 100
							ORDER BY VUPU.UOR_DLSIGLAUNIDADE, APAL.APAL_DLLOCALIZACAO";
	
	
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
	$sql	=	"SELECT TAAU_ID, TAAU_DLAUSENCIAAUTORIZADA
							FROM  BIOMETRIA.TAAU_TIPOAUSENCIAAUTORIZADA";
	}
	
//
include 'ambiente.php';
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
if( $debug )
	echo "SQL=$sql<br>\r\n";
$res = $ora->execSelect($sql);
if( $debug )
	{
	$alf = trim( preg_replace("/([\\x00-\\x1f])/e", "", $res) );
	echo "alfa: $alf<br>";
	//toHex($alf);
	echo "<br>\r\n";
	}
//	monta a resposta { "data": ["cmp": "val"
$jsres	= json_decode(preg_replace("/([\\x00-\\x1f])/e", "", $res));
if( $debug )
	{
	echo "=================json convertido======================<br>\r\n";
	var_dump( $jsres );
	echo "<br>\r\n";
	}
$qtcmp	=	$jsres->linhas;
if( $debug )
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
