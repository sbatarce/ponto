<?php
if( !isset( $_GET["user"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro USER obrigatorio" }';
	return;
	}
if( !isset( $_GET["loguser"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro loguser obrigatorio" }';
	return;
	}
if( !isset( $_GET["logpass"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro logpass obrigatorio" }';
	return;
	}
$loguser	=	$_GET["loguser"];
$user	=	$_GET["user"];
$logpass	=	$_GET["logpass"];

include 'ambiente.php';

//	verifica o SSHD
$conn = @oci_connect( $loguser, $logpass, $amb, $chset, OCI_DEFAULT );
if( !$conn )
  {
  $e = oci_error();
  $mes = $e[ 'message' ];
  echo "{ \"status\": \"erro\", \"erro\":\"acesso negado\", \"dbmens\": \"$mes\" }";
  return;
  }
oci_close( $conn );

//	verifica na biometria
$sql	=	"SELECT FUNI.FUNI_ID, FUAU.PMS_IDSAUUOR
					FROM BIOMETRIA.FUNI_FUNCIONARIO FUNI
					LEFT JOIN BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAU ON
										FUAU.FUNI_ID=FUNI.FUNI_ID AND
										FUAU.FUAU_DTFIM IS NULL
					WHERE FUNI.PMS_IDPMSPESSOA='$user'";
$conn = @oci_connect( $userb, $passb, $amb, $chset, OCI_DEFAULT );
if( !$conn )
  {
  $e = oci_error();
  $mes = $e[ 'message' ];
  echo "{ \"status\": \"erro\", \"erro\":\"$mes\", \"local\": \"oci_connect\" }";
  return;
  }
// Prepare the statement
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
$funiid	=	$row['FUNI_ID'];
$puorid = $row['PMS_IDSAUUOR'];
oci_free_statement( $stid );
oci_close( $conn );
echo "{ \"status\": \"OK\", \"FUNI_ID\": \"$funiid\", \"UOR_ID\": \"$puorid\" }";
