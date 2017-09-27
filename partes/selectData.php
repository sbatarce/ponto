<?php
//	seleçao no formato adequado para SELECT2 (combo box web)
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

//	lista de UORS
if( $qry == "uor" )
	{
	$sql	=	"select UOR_IDUNIDADEORGANIZACIONAL, " .
					"CONCAT( CONCAT( UOR_DLSIGLAUNIDADE, '-' ), " .
					"replace( UOR_DLNOMEUNIDADE, '\"', ' ' ) ) as NOME " .
					"from SAU.VWUORPUBLICA where EMP_IDEMPRESA = 1";
	}

//	funcsuor - funcionários pertencentes a uma UOR
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
$res = $ora->connect( $userb, $passb, $amb, $chset, TRUE );
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

if( !$sem0 )
	$data		=	'[ {"id":"0", "text":"Escolha abaixo"}';
else
	$data = '[ ';
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
