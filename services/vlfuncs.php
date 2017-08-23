<?php
include '../partes/fmtErro.php';
include '../partes/ambiente.php';
include '../partes/ORAConn.php';

$meth = $_SERVER['REQUEST_METHOD'];
if( $meth != 'POST' )
	{
	fmtErro( "erro", "HTTP-method GET não permitido" );
	return;
	}

$dbg = isset( $_GET["dbg"] );

//	
if( $dbg )
	{
	echo "ambiente=$amb / user=$userb <br>";
	}

//	obtem e decodifica o json do corpo
include '../partes/trataJSON.php';
if( $dbg )
	{
	var_dump( $body );
	var_dump($json );
	}
if( $json == null )
	{
	fmtErro( "erro", "falta o JSON no corpo da mensagem" );
	return;
	}
//	verifica os dados do JSON
if( !isset($json->sshd) )
	{
	fmtErro( "erro", "sshd&senha obrigatorios no corpo da mensagem" );
	return;
	}
if( !isset($json->pass) )
	{
	fmtErro( "erro", "sshd&senha obrigatorios no corpo da mensagem" );
	return;
	}
$user = $json->sshd;
$pass = $json->pass;
//	verifica o SSHD
$conn = @oci_connect( $user, $pass, $amb, $chset, OCI_DEFAULT );
if( !$conn )
  {
  $e = oci_error();
  $mes = $e[ 'message' ];
  echo "{ \"status\": \"erro\", \"erro\":\"$mes\", \"local\": \"conectando no oracle\" }";
  return;
  }
//	verifica no FUNI
$sql	=	"SELECT FUNI.FUNI_ID, FUAU.PMS_IDSAUUOR
					FROM BIOMETRIA.FUNI_FUNCIONARIO FUNI
					LEFT JOIN BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAU ON
										FUAU.FUNI_ID=FUNI.FUNI_ID AND
										FUAU.FUAU_DTFIM IS NULL
					WHERE FUNI.PMS_IDPMSPESSOA='$user'";
$stid = oci_parse( $conn, $sql );
if( !$stid )
  {
  $e = oci_error($conn);
  $mes = $e[ 'message' ];
  echo "{ \"status\": \"erro\", \"erro\":\"$mes\", \"local\": \"oci_parse\" }";
  return;
  }
// Perform the logic of the query
$r = oci_execute( $stid );
if( !$r )
  {
  $e = oci_error($stid);
  $mes = $e[ 'message' ];
  echo "{ \"status\": \"erro\", \"erro\":\"$mes\", \"local\": \"oci_execute\" }";
  return;
  }
$row = oci_fetch_array( $stid, OCI_ASSOC + OCI_RETURN_NULLS );
if( !$row )
	{
  echo "{ \"status\": \"erro\", \"erro\":\"usuario não localizado\" }";
	return;
	}
$funiid = $row['FUNI_ID'];
oci_free_statement( $stid );
oci_close( $conn );
echo "{ \"status\": \"OK\", \"id\": \"$funiid\" }";
