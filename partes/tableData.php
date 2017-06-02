<?php
//	seleção de dados com retorno para o DataTables
header('Content-Type: text/html; charset=UTF8');

if( !isset( $_GET["query"] ) && !isset( $_GET["debug"] ) )
	{
	echo	'{ "data": [{"erro": "parametro query obrigatório"}] }';
	return;
	}
//	prepara o select
if( isset( $_GET["query"] ) )
	{
	$qry = $_GET["query"];
	$dbg = FALSE;
	}
if( isset( $_GET["debug"] ) )
	{
	$qry = $_GET["debug"];
	$dbg = TRUE;
	}
$sql	=	"";
//
if( $qry == "xpto" )
	{
	if( !isset( $_GET["xpto"] ) )
		{
		echo	'{ "data": [{"erro": "parametro data obrigatorio"}] }';
		return;
		}
	$xpto = $_GET["xpto"];
	$sql	=	"SELECT ";
	}
if( $qry == "teste" )
	{
	$sql	=	"SELECT * from BIOMETRIA.FUNI_FUNCIONARIO";
	}
//
if( $qry == "pendencias" )
	{
	if( !isset( $_GET["sshd"] ) )
		{
		echo	'{ "data": [{"erro": "parametro sshd obrigatorio"}] }';
		return;
		}
	if( !isset( $_GET["dtini"] ) )
		{
		echo	'{ "data": [{"erro": "parametro dtini obrigatorio"}] }';
		return;
		}
	if( !isset( $_GET["dtfim"] ) )
		{
		echo	'{ "data": [{"erro": "parametro dtfim obrigatorio"}] }';
		return;
		}
	if( isset( $_GET["ok"] ) )
		$ok = true;
	else
		$ok = false;
	if( isset( $_GET["pen"] ) )
		$pen = true;
	else
		$pen = false;
	if( isset( $_GET["ana"] ) )
		$ana = true;
	else
		$ana = false;
	if( isset( $_GET["ace"] ) )
		$ace = true;
	else
		$ace = false;
	if( isset( $_GET["neg"] ) )
		$neg = true;
	else
		$neg = false;
	
	$stts = "";
	$nnul = "";
	$snul = "";
	if( $pen || $ana || $ace || $neg )
		{
		$ins = "";
		if( $pen )
			{
			if( strlen( $ins ) > 0 )
				$ins .= ",";
			$ins .= "1";
			}
		if( $ace )
			{
			if( strlen( $ins ) > 0 )
				$ins .= ",";
			$ins .= "2";
			}
		if( $neg )
			{
			if( strlen( $ins ) > 0 )
				$ins .= ",";
			$ins .= "3";
			}
		if( $ana )
			{
			if( strlen( $ins ) > 0 )
				$ins .= ",";
			$ins .= "4";
			}
		$nnul = "FDTR.TSDT_ID in ($ins)";
		}
	if( $ok )
		$snul = "FDTR.TSDT_ID is null";
	if( strlen( $nnul ) > 0 && strlen( $snul ) > 0 )
		$stts = "AND ($nnul OR $snul)";
	else
		{
		if( strlen( $snul ) > 0 )
			$stts = "AND $snul ";
		if( strlen( $nnul ) > 0 )
			$stts = "AND $nnul ";
		}
	
	$dtini = $_GET["dtini"];
	$dtfim = $_GET["dtfim"];
	$sshd = $_GET["sshd"];
	$sql	=	"SELECT		FUNI.FUNI_ID, FDTR.FDTR_ID, FDTR.TSDT_ID, FUNI.PMS_IDPMSPESSOA as SSHD, PESS.NOME,
										to_char( FDTR.FDTR_DTREFERENCIA, 'DD/MM/YYYY' ) as DATA,
										to_char( FDTR.FDTR_DTREFERENCIA, 'YYYYMMDD' ) as ISODATE,
										(SELECT LISTAGG(TO_CHAR(FDTE.FDTE_DTHORARIO, 'HH24:MI'), ';') WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO)
														FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
														WHERE FDTE.FDTR_ID = FDTR.FDTR_ID) AS HORARIOS,
										(SELECT LISTAGG(NVL( FDTE.TORG_ID, '0'), ';') WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO)
														FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
														WHERE FDTE.FDTR_ID = FDTR.FDTR_ID) AS OPERACOES,
										(SELECT LISTAGG(NVL( FDTE.TORE_ID, '0'), ';') WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO)
														FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
														WHERE FDTE.FDTR_ID = FDTR.FDTR_ID) AS BIOMET_FUNCION,
										BIOMETRIA.SF_CALCULATEMPODIATRABALHO( FUNI.FUNI_ID, FDTR.FDTR_DTREFERENCIA ) as MINUTOSDIA,
										(SELECT LISTAGG(TMEN.TMEN_DCMENS, '; ') WITHIN GROUP (ORDER BY FDTN.FDTN_ID)
                             FROM BIOMETRIA.FDTN_FDTR_TMEN FDTN
                             INNER JOIN BIOMETRIA.TMEN_TIPOMENSAGEM TMEN ON
                                        TMEN.TMEN_ID = FDTN.TMEN_ID
                             WHERE FDTN.FDTR_ID = FDTR.FDTR_ID) AS MENSPADRAO,
										FDTM_ID, FDTM.FDTM_DLMENS,
										(SELECT SUM(FDTF_NITMPREAL) FROM BIOMETRIA.FDTF_FUNCDIATRABALHO_FAAU 
														WHERE FDTR_ID=FDTR.FDTR_ID) AS AUTORIZADAS,
										(SELECT CASE FUCO_DCDBCR
															WHEN 'DB' THEN -FUCO_NITMP
															WHEN 'CR' THEN FUCO_NITMP END
												FROM BIOMETRIA.FUCO_FUNCCORRECAOHORAS 
												WHERE FUCO_DTREFERENCIA=FDTR.FDTR_DTREFERENCIA) AS CORRECAO
							FROM        BIOMETRIA.FUNI_FUNCIONARIO FUNI
							INNER JOIN  SAU.VWPESSOA_SSHD PESS ON
													PESS.REGISTRO_FUNCIONAL_ATIVO = 1 
													AND PESS.IUN = FUNI.PMS_IDPMSPESSOA
							INNER JOIN  BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR ON
													FRTR.FUNI_ID=FUNI.FUNI_ID
							INNER JOIN  BIOMETRIA.FDTR_FUNCIONARIODIATRABALHO FDTR ON
													FDTR.FRTR_ID = FRTR.FRTR_ID 
													AND FDTR.FDTR_DTREFERENCIA BETWEEN 
															TO_DATE('$dtini', 'YYYYMMDD') AND TO_DATE('$dtfim', 'YYYYMMDD') + 1 
                          $stts
							LEFT JOIN   BIOMETRIA.FDTM_FUNCDIATRABALHOMENSAGEM FDTM ON
													FDTM.FDTR_ID = FDTR.FDTR_ID
							WHERE       FUNI.PMS_IDPMSPESSOA <> '$sshd'";

	}
//
if( $qry == "funaces" )
	{
	if( !isset( $_GET["sshd"] ) )
		$sshd	=	"";
	else
		$sshd = $_GET["sshd"];
	if( !isset( $_GET["dtini"] ) )
		{
		echo	'{ "data": [{"erro": "parametro dtini obrigatorio"}] }';
		return;
		}
	if( !isset( $_GET["dtfim"] ) )
		{
		echo	'{ "data": [{"erro": "parametro dtfim obrigatorio"}] }';
		return;
		}
	
	$dtini = $_GET["dtini"];
	$dtfim = $_GET["dtfim"];
	$sql	=	"SELECT		FUNI.FUNI_ID, FDTR.FDTR_ID, FDTR.TSDT_ID, FUNI.PMS_IDPMSPESSOA as SSHD, PESS.NOME,
										to_char( FDTR.FDTR_DTREFERENCIA, 'DD/MM/YYYY' ) as Data,
										(SELECT LISTAGG(FDTE.FDTE_ID, ';') WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO)
														FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
														WHERE FDTE.FDTR_ID = FDTR.FDTR_ID) AS FDTEIDS,
										(SELECT LISTAGG(TO_CHAR(FDTE.FDTE_DTHORARIO, 'HH24:MI'), ';') WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO)
														FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
														WHERE FDTE.FDTR_ID = FDTR.FDTR_ID) AS HORARIOS,
										(SELECT LISTAGG(NVL( FDTE.TORG_ID, '0'), ';') WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO)
														FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
														WHERE FDTE.FDTR_ID = FDTR.FDTR_ID) AS OPERACOES,
										(SELECT LISTAGG(NVL( FDTE.TORE_ID, '0'), ';') WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO)
														FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
														WHERE FDTE.FDTR_ID = FDTR.FDTR_ID) AS ORIGENS,
										to_char( to_date(BIOMETRIA.SF_CALCULATEMPODIATRABALHO( FUNI.FUNI_ID, FDTR.FDTR_DTREFERENCIA )*60, 'SSSSS' ), 'HH24:MI' ) AS TOTAL,
										(SELECT LISTAGG(TMEN.TMEN_DCMENS, '; ') WITHIN GROUP (ORDER BY FDTN.FDTN_ID)
                             FROM BIOMETRIA.FDTN_FDTR_TMEN FDTN
                             INNER JOIN BIOMETRIA.TMEN_TIPOMENSAGEM TMEN ON
                                        TMEN.TMEN_ID = FDTN.TMEN_ID
                             WHERE FDTN.FDTR_ID = FDTR.FDTR_ID) AS TIPOMENSAGEM, 
										FDTM_ID, FDTM.FDTM_DLMENS,
										(SELECT  LISTAGG( TAAU.TAAU_DLAUSENCIAAUTORIZADA || '=' || FDTF.FDTF_NITMPREAL, ';')
																WITHIN GROUP( ORDER BY FDTF.FDTF_NITMPREAL )
											FROM BIOMETRIA.FDTF_FUNCDIATRABALHO_FAAU FDTF
											INNER JOIN BIOMETRIA.FAAU_FUNCAUSENCIAAUTORIZADA FAAU ON FAAU.FAAU_ID=FDTF.FAAU_ID
											INNER JOIN BIOMETRIA.TAAU_TIPOAUSENCIAAUTORIZADA TAAU ON TAAU.TAAU_ID=FAAU.TAAU_ID 
											WHERE FDTR_ID=FDTR.FDTR_ID) AS AUTORIZADAS,
										CASE FUCO_DCDBCR 
														WHEN 'DB' THEN -FUCO_NITMP
														WHEN 'CR' THEN FUCO_NITMP END  AS CORRECAO, 
										BIOMETRIA.SF_CALCULASALDOINICIAL( FUNI.FUNI_ID, FDTR.FDTR_DTREFERENCIA+1) AS SALDO
							FROM        BIOMETRIA.FUNI_FUNCIONARIO FUNI
							INNER JOIN  SAU.VWPESSOA_SSHD PESS ON
													PESS.REGISTRO_FUNCIONAL_ATIVO = 1 
													AND PESS.IUN = FUNI.PMS_IDPMSPESSOA
							INNER JOIN  BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR ON
													FRTR.FUNI_ID=FUNI.FUNI_ID
							INNER JOIN  BIOMETRIA.FDTR_FUNCIONARIODIATRABALHO FDTR ON
													FDTR.FRTR_ID = FRTR.FRTR_ID
							LEFT JOIN   BIOMETRIA.FDTM_FUNCDIATRABALHOMENSAGEM FDTM ON
													FDTM.FDTR_ID = FDTR.FDTR_ID
							LEFT JOIN		BIOMETRIA.FUCO_FUNCCORRECAOHORAS FUCO ON 
													FUCO.FUNI_ID=FUNI.FUNI_ID AND
													FUCO.FUCO_DTREFERENCIA=FDTR.FDTR_DTREFERENCIA
							WHERE       FDTR.FDTR_DTREFERENCIA BETWEEN TO_DATE('$dtini', 'YYYYMMDD') 
													AND TO_DATE('$dtfim', 'YYYYMMDD') + 1 ";
	if( $sshd != "" )
		$sql .= "AND FUNI.PMS_IDPMSPESSOA='$sshd' 
						ORDER BY FUNI.FUNI_ID, FDTR.FDTR_DTREFERENCIA";
	else
		$sql .= "ORDER BY FDTR.FDTR_DTREFERENCIA";
	}
//	
if( $sql == "" )
	{
	echo	'{ "data": [{"erro": "query desconhecido"}] }';
	return;	
	}
if( $dbg )
	{
	echo "$sql<br>";
	echo "=========================================<br>";
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
$res = $ora->execSelect($sql);
//	monta a resposta { "data": ["cmp": "val"
$jsres	= json_decode($res);
$qtcmp	=	$jsres->linhas;
$data		=	"{\"data\":[";
for( $irec=0; $irec<$qtcmp; $irec++ )
	{
	$rec = "{";
	foreach( $jsres->dados[$irec] as $nom=>$val )
		{
		if( $nom == "_linha" )
			continue;
		if( strlen( $rec ) > 1 )
			$rec	.=	",";
		$rec	.=	"\"$nom\":\"$val\"";
		}
	$rec	.=	"}";
	if( $irec > 0 )
		$data	.= ",";
	$data	.=	"{$rec}";
	}
$data	.=	"]}";
echo $data;
$ora->disconnect();
