<?php
//	horasdia( sshd, data) - obtem a quantidade de hora do dia de um funcionário
//	ausaut( sshd, data ) -	obtem ids de todos as ausencias autorizadas com data 
//													de término maior
//	dtfecha( sshd ) - obtem a data do último fechamento no formato YYYY-MM-DD
//	qtdbiom( fdtrid ) - conta a quantidade de batidas de biometria de um FDTR 
//	reprpmspessoa( pessoa(sshd), dtinic, dtterm ) - obtem os registro na biometria de uma pessoa num período
//	saldoant( funiid, dtinic ) - obter saldo anterior do FUNI_ID em uma data
//	funiid( sshd ) - obter FUNI_ID do SSHD
//	sshd( funiid ) - obter SSHD do FUNI_ID
//	fuauid( funiid, uorid ) - obtem o FUAU_ID do autorizador da UOR
//	medioperio( funiid, dtini, dtfim )
//	mediofecha( funiid )
if( !isset( $_GET["query"] ) && !isset( $_GET["debug"] ) )
	{
	echo	'{ "status": "erro", "erro": "parametro query obrigatorio" }';
	return;
	}
	
//	prepara o query
$sql	=	"";
$dbg = false;
if( isset( $_GET["query"] ) )
	$qry	=	$_GET["query"];
else
	{
	$qry	=	$_GET["debug"];
	$dbg	=	true;
	}

////////////////////////////////////////////////////////////////////////////////
//	obttaau( taauid ) obtem o TAAU dado o ID
if( $qry == "obttaau" )
	{
	if( !isset( $_GET["taauid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro taauid obrigatorio" }';
		return;
		}
	$taauid	=	$_GET["taauid"];
	$sql = "select TAAU_STMARCACAO, TAAU_NIVARHORAS 
						FROM  BIOMETRIA.TAAU_TIPOAUSENCIAAUTORIZADA
						WHERE TAAU_ID=$taauid";
	}

////////////////////////////////////////////////////////////////////////////////
//	horasdia( sshd, data) - obtem a quantidade de hora do dia de um funcionário
if( $qry == "horasdia" )
	{
	if( !isset( $_GET["sshd"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro sshd obrigatorio" }';
		return;
		}
	if( !isset( $_GET["data"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro data obrigatorio" }';
		return;
		}
	$sshd	=	$_GET["sshd"];
	$data	=	$_GET["data"];
	$sql = "SELECT	RETR.RETR_DLNOME AS REGIME, RTAT.RTAT_NIDIASEMANA AS DIASEMANA, 
									RTAT.RTAT_NITMPDIARIO AS HORASDIA,
									( 
									SELECT NVL( SUM( FAAU.FAAU_NITMPDIARIO ), 0 )
										FROM        BIOMETRIA.FUNI_FUNCIONARIO FUNI
										INNER JOIN  BIOMETRIA.FAAU_FUNCAUSENCIAAUTORIZADA FAAU ON
																FAAU.FUNI_ID=FUNI.FUNI_ID AND 
																TO_DATE( '$data', 'YYYYMMDD' ) BETWEEN
																		FAAU.FAAU_DTINI AND FAAU.FAAU_DTFIM
										WHERE FUNI.PMS_IDPMSPESSOA='$sshd'
									) AS TOTAUT
						FROM        BIOMETRIA.FUNI_FUNCIONARIO FUNI
						INNER JOIN  BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR ON
												FRTR.FUNI_ID=FUNI.FUNI_ID AND
												( TO_DATE( '$data', 'YYYYMMDD' ) BETWEEN
													FRTR.FRTR_DTINICIO AND FRTR.FRTR_DTFIM  OR
													( TO_DATE( '$data', 'YYYYMMDD' ) >= FRTR.FRTR_DTINICIO AND
														FRTR.FRTR_DTFIM IS NULL ) )
						INNER JOIN  BIOMETRIA.RETR_REGIMETRABALHO RETR ON
												RETR.RETR_ID=FRTR.RETR_ID
						INNER JOIN  BIOMETRIA.RTAT_REGIMETRABALHOATRIBUTO RTAT ON
												RTAT.RETR_ID=RETR.RETR_ID AND
												TO_CHAR( TO_DATE( '$data', 'YYYYMMDD' ), 'd' )=RTAT.RTAT_NIDIASEMANA
						WHERE FUNI.PMS_IDPMSPESSOA='$sshd'";
	}

////////////////////////////////////////////////////////////////////////////////
//	ausaut( sshd, data ) - verifica se na data há uma ausencia autorizada do tipo dado
if( $qry == "ausaut" )
	{
	if( !isset( $_GET["sshd"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro sshd obrigatorio" }';
		return;
		}
	if( !isset( $_GET["data"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro data obrigatorio" }';
		return;
		}
	if( !isset( $_GET["taauid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro taauid obrigatorio" }';
		return;
		}
	$sshd	=	$_GET["sshd"];
	$data	=	$_GET["data"];
	$taauid = $_GET["taauid"];
	$sql = "select  FAAU.FAAU_ID AS FAAUID
						FROM        BIOMETRIA.FAAU_FUNCAUSENCIAAUTORIZADA FAAU
						INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
												FUNI.FUNI_ID=FAAU.FUNI_ID
						WHERE FUNI.PMS_IDPMSPESSOA='$sshd' AND
									TO_DATE( '$data', 'YYYYMMDD' ) BETWEEN 
												FAAU.FAAU_DTINI AND FAAU.FAAU_DTFIM
									AND FAAU.TAAU_ID=$taauid";
	}

////////////////////////////////////////////////////////////////////////////////
//	dtfecha( sshd ) - obtem a data do último fechamento no formato YYYY-MM-DD
if( $qry == "dtfecha" )
	{
	if( !isset( $_GET["sshd"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro sshd obrigatorio" }';
		return;
		}
	$sshd	=	$_GET["sshd"];
	$sql = "select TO_CHAR( FSHM.FSHM_DTREFERENCIA, 'YYYY-MM-DD' ) AS DTFECHA
    FROM BIOMETRIA.FSHM_FUNCSALDOHORAMENSAL FSHM
    INNER JOIN BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
                FUNI.FUNI_ID=FSHM.FUNI_ID
    WHERE FUNI.PMS_IDPMSPESSOA='$sshd'";
	}

////////////////////////////////////////////////////////////////////////////////
//	qtdbiom( fdtrid ) - conta a quantidade de batidas de biometria de um FDTR 
if( $qry == "qtdbiom" )
	{
	if( !isset( $_GET["fdtrid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro fdtrid obrigatorio" }';
		return;
		}
	$fdtrid = $_GET["fdtrid"];
	$sql	=	"SELECT TORE_ID AS ORIGEM, COUNT( TORE_ID ) AS QTD, 
									LISTAGG(TO_CHAR( FDTE.FDTE_DTHORARIO, 'HH24:MI' ), ';' ) 
									  WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO) AS HORARIOS
							FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
							WHERE FDTE.FDTR_ID=$fdtrid 
							GROUP BY FDTE.TORE_ID";
	}

////////////////////////////////////////////////////////////////////////////////
//	reprpmspessoa( pessoa(sshd), dtinic, dtterm ) - obtem os registro na biometria de uma pessoa num período
if( $qry == "reprpmspessoa" )
	{
	if( !isset( $_GET["pessoa"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro pessoa obrigatorio" }';
		return;
		}
	if( !isset( $_GET["dtinic"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro dtinic obrigatorio" }';
		return;
		}
	if( !isset( $_GET["dtterm"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro dtterm obrigatorio" }';
		return;
		}
	$pessoa	=	$_GET["pessoa"];
	$dtinic	=	$_GET["dtinic"];
	$dtterm	=	$_GET["dtterm"];
	$sql = "SELECT DISTINCT to_char( REPR.REPR_DTREGISTROPRESENCA, 'DD/MM/YYYY HH24:mi' ) as ponto, " .
					"to_char( REPR.REPR_DTREGISTROPRESENCA, 'YYYYMMDDHH24mi' ) as ordem " .
					"FROM BIOMETRIA.REPR_REGISTROPRESENCA REPR  " .
					"WHERE REPR.PMS_IDPMSPESSOA='" . $pessoa . "' " .
					"and trunc( REPR_DTREGISTROPRESENCA ) >= to_date( '" .
					$dtinic . "', 'YYYYMMDD' ) " .
					"and trunc( REPR_DTREGISTROPRESENCA ) <= to_date( '" . 
					$dtterm . "', 'YYYYMMDD' ) " .
					"ORDER BY ordem";
	}

////////////////////////////////////////////////////////////////////////////////
//	saldoant( funiid, dtinic ) - obter saldo anterior do FUNI_ID em uma data
if( $qry == "saldoant" )
	{
	if( !isset( $_GET["funiid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro funiid obrigatorio" }';
		return;
		}
	if( !isset( $_GET["dtinic"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro dtinic obrigatorio" }';
		return;
		}
	$funiid	=	$_GET["funiid"];
	$dtinic	=	$_GET["dtinic"];
	$sql = "SELECT BIOMETRIA.SF_CALCULASALDOINICIAL( $funiid, TO_DATE('$dtinic','YYYYMMDD')) 
						AS MINUTOS FROM DUAL";
	}


////////////////////////////////////////////////////////////////////////////////
//	funiid( sshd ) - obter FUNI_ID do SSHD
if( $qry == "funiid" )
	{
	if( !isset( $_GET["sshd"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro sshd obrigatorio" }';
		return;
		}
	$sshd	=	$_GET["sshd"];
	$sql = "SELECT FUNI_ID FROM BIOMETRIA.FUNI_FUNCIONARIO 
						WHERE PMS_IDPMSPESSOA='$sshd'";
	}
	

////////////////////////////////////////////////////////////////////////////////
//	funiid( sshd ) - obter FUNI_ID do SSHD
if( $qry == "sshd" )
	{
	if( !isset( $_GET["funiid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro funiid obrigatorio" }';
		return;
		}
	$funiid	=	$_GET["funiid"];
	$sql = "SELECT PMS_IDPMSPESSOA AS SSHD FROM BIOMETRIA.FUNI_FUNCIONARIO 
						WHERE FUNI_ID=$funiid";
	}
	
////////////////////////////////////////////////////////////////////////////////
//	fuauid( funiid, uorid ) - obtem o FUAU_ID do autorizador da UOR
if( $qry == "fuauid" )
	{
	if( !isset( $_GET["sshd"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro sshd obrigatorio" }';
		return;
		}
	if( !isset( $_GET["uorid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro uorid obrigatorio" }';
		return;
		}
	$sshd	=	$_GET["sshd"];
	$uorid	=	$_GET["uorid"];
	$sql = "SELECT FUAU_ID AS FUAUID 
						FROM BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAU
						INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
												FUNI.FUNI_ID = FUAU.FUNI_ID
						where FUAU.PMS_IDSAUUOR=$uorid and FUNI.PMS_IDPMSPESSOA='$sshd'";
	}
	
////////////////////////////////////////////////////////////////////////////////
//	medioperio( funiid, dtini, dtfim )
if( $qry == "medioperio" )
	{
	if( !isset( $_GET["funiid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro funiid obrigatorio" }';
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
	$funiid	=	$_GET["funiid"];
	$dtini	=	$_GET["dtini"];
	$dtfim	=	$_GET["dtfim"];
	$sql = "SELECT  NIQUANTIDADEHORARIO1 AS QTDENTRA, NISOMAHORARIO1 AS MEDENTRA, 
									NIQUANTIDADEHORARIO2 AS QTDINTER, NISOMAHORARIO2 AS MEDINTER, 
									NIQUANTIDADEHORARIO3 AS QTDVOLTA, NISOMAHORARIO3 AS MEDVOLTA, 
									NIQUANTIDADEHORARIO4 AS QTDSAIDA, NISOMAHORARIO4 AS MEDSAIDA
							FROM TABLE(BIOMETRIA.SF_CALCULAMEDIAHORARIOBATIDAS
												($funiid,TO_DATE( '$dtini', 'YYYYMMDD' ), 
														TO_DATE( '$dtfim', 'YYYYMMDD' )))";
	}
	
////////////////////////////////////////////////////////////////////////////////
//	mediofecha( funiid )
if( $qry == "mediofecha" )
	{
	if( !isset( $_GET["funiid"] ) )
		{
		echo	'{ "status": "erro", "erro": "parametro funiid obrigatorio" }';
		return;
		}
	$funiid	=	$_GET["funiid"];
	$sql = "SELECT  TO_CHAR( FSHM_DTREFERENCIA, 'DD/MM/YYYY' ) AS DATA, 
									FSHM_NIQTDENTRA AS QTDENTRA, FSHM_NIMEDENTRA AS MEDENTRA,
									FSHM_NIQTDINTER AS QTDINTER, FSHM_NIMEDINTER AS MEDINTER,
									FSHM_NIQTDVOLTA AS QTDVOLTA, FSHM_NIMEDVOLTA AS MEDVOLTA,
									FSHM_NIQTDSAIDA AS QTDSAIDA, FSHM_NIMEDSAIDA AS MEDSAIDA
					  FROM BIOMETRIA.FSHM_FUNCSALDOHORAMENSAL
						WHERE FUNI_ID=$funiid
						ORDER BY FSHM_DTREFERENCIA DESC";
	}
	
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if( $dbg )
	{
	echo $sql . "<br>";
	return;
	}
if( $sql == "" )
	{
	echo	'{ "status": "erro", "erro": "query desconhecido" }';
	return;
	}
//	executa o SQL
include 'ambiente.php';
include 'ORAConn.php';

$ora = new ORAConn();
$res = $ora->connect($userb, $passb, $amb, $chset);
if( $res != "OK" )
	{
	echo $res;
	return;
	}
//	executa o query
$res = $ora->execSelect($sql);
echo $res;
$ora->disconnect();


