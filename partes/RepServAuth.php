<?php
include 'ambiente.php';

$meth = $_SERVER['REQUEST_METHOD'];
if( $meth != "POST" )
	{
	header("HTTP/1.1 610 metodo nao permitido");
	echo '{ "erro": "610 - metodo nao permitido. Somente POST 1" }';
	return;
	}
//	obtem usuario e senha e valida
if( isset( $_SERVER['PHP_AUTH_USER'] ) )
	$username = $_SERVER['PHP_AUTH_USER'];
else
	{
	header("HTTP/1.1 615 acesso negado");
	echo '{ "erro": "615 - acesso negado" }';
	return;
	}
if( isset( $_SERVER['PHP_AUTH_PW'] ) )
	$password = $_SERVER['PHP_AUTH_PW'];	
else
	{
	header("HTTP/1.1 615 acesso negado");
	echo '{ "erro": "615 - acesso negado" }';
	return;
	}
$conn = @oci_connect( $username, $password, $amb, $chset, OCI_DEFAULT );
if( !$conn )
	{
	header("HTTP/1.1 615 acesso negado");
	echo '{ "erro": "615 - acesso negado" }';
	return;
	}
oci_close( $conn );
//
if( isset($_SERVER['PATH_INFO']))
	$path = $_SERVER['PATH_INFO'];
else
	{
	header("HTTP/1.1 607 falta qualificacao do recurso");
	echo '{ "erro": "607 - falta qualifica��o do recurso" }';
	return;
	}
//	headers do RepService
$repip = "";
$idapal = "";
$repmac = "";
$repsis = "";
$caddig = "";
$cdigs = "";
$toconn = "";
$totroca = "";
$repcon = "";
$reptroca = "";

$headers = apache_request_headers();
$bodystr = @file_get_contents('php://input');
$jbody = json_decode( $bodystr, true );
if( isset( $jbody["debug"] ) )
	$debug = true;
else
	$debug = false;
if( isset( $_SERVER["QUERY_STRING"] ) )
	{
	$parmn = $_SERVER["QUERY_STRING"];
	$parm = urlencode($parmn);
	$parm = str_replace(" ", "+", $parmn );
	}
else
	$parm = "";
if( $debug )
	echo "query_string=$parm\n";
if( !isset($jbody["metodo"]))
	{
	$repmeth = "GET";
	}
else
	{
	$repmeth	= $jbody["metodo"];
	if( $repmeth != "GET" && $repmeth != "POST" && $repmeth != "PUT" && $repmeth != "DELETE" )
		{
		header("HTTP/1.1 610 metodo invalido");
		header('Content-Type: application/json; charset=utf-8');
		echo '{ "erro": "610 - metodo invalido" }';
		return;
		}
	}
if( $debug )
	echo "path=$path\n";
if( !isset( $jbody["url"] ) )
	{
	if( $amb == "CRSPROD1" )
		$repurl = "http://vmp-webserv03.santos.sp.gov.br/cgi-bin/RepService.cgi";
	else
		$repurl = "http://vmd-webserv03.santos.sp.gov.br/cgi-bin/RepService.cgi";
	}
else
	$repurl = $jbody["url"];
if( isset($jbody["corpo"]))
	$corpo = json_encode( $jbody["corpo"] );
else
	$corpo = "";
$repheaders = array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($corpo));
foreach( $headers as $header => $value )
	{
	if( strtolower($header) == "url" )
		{
		$repurl = $value;
		}
	if( strtolower($header) == "idapal" )
		{
		$repheaders[] = "IDAPAL:".$value;
		}
	if( strtolower($header) == "repip" )
		{
		$repheaders[] = "REPIP:".$value;
		}
	if( strtolower($header) == "repmac" )
		{
		$repheaders[] = "REPMAC:".$value;
		}
	if( strtolower($header) == "repsis" )
		{
		$repheaders[] = "REPSIS:".$value;
		}
	if( strtolower($header) == "caddig" )
		{
		$repheaders[] = "CADDIG:".$value;
		}
	if( strtolower($header) == "cdigs" )
		{
		$repheaders[] = "CDIGS:".$value;
		}
	if( strtolower($header) == "toconn" )
		{
		$repheaders[] = "TOCONN:".$value;
		}
	if( strtolower($header) == "totroca" )
		{
		$repheaders[] = "TOTROCA:".$value;
		}
	if( strtolower($header) == "repconn" )
		{
		$repheaders[] = "REPCONN:".$value;
		}
	if( strtolower($header) == "reptroca" )
		{
		$repheaders[] = "REPTROCA:".$value;
		}
	}
//	prepara a chamada do repservice ou outro php
$ch = curl_init();
	$repurl .= $path;
if( $parm != "" )
	$repurl .= "?".$parm;
if( $debug )
	echo "URL Resultante:$repurl\n";
curl_setopt($ch, CURLOPT_URL, $repurl );
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $repmeth );

curl_setopt($ch, CURLOPT_POSTFIELDS, $corpo);
curl_setopt($ch, CURLOPT_HTTPHEADER, $repheaders);

curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);

$ret = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);
$jbody = json_decode($body,true);
if( $ret < 300 )
	header("HTTP/1.1 ".$ret." OK");
else
	{
	$erro = $jbody["ocorr"];
	header("HTTP/1.1 ".$ret." ".$erro);
	}
header('Content-Type: application/json; charset=utf-8');
echo $body;
/*
echo "\r\n================== Header recebido ===================";
echo "\r\n";
var_dump($header);
echo "\r\n================== Response recebido ===================";
echo "\r\n";
var_dump($response);

echo "<br>ERRO: $nuerr - $txerr<br>";

$info = curl_getinfo($ch);
var_dump($info);
*/
curl_close($ch);
