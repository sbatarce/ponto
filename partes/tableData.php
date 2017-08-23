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
////////////////////////////////////////////////////////////////////////////////
//
if( $qry == "ausaut" )
	{
	if( !isset( $_GET["sshd"] ) )
		{
		echo	'{ "data": [{"erro": "parametro sshd obrigatorio"}] }';
		return;
		}
	if( !isset( $_GET["inicio"] ) )
		{
		echo	'{ "data": [{"erro": "parametro inicio obrigatorio"}] }';
		return;
		}
	$sshd = $_GET["sshd"];
	$inicio = $_GET["inicio"];
	$sql = "select  TAAU.TAAU_DLAUSENCIAAUTORIZADA as TIPO, TAAU.TAAU_STMARCACAO as PODE, 
									FAAU.FAAU_ID, TO_CHAR( FAAU.FAAU_DTINI, 'DD/MM/YYYY' ) as INICIO, 
									TO_CHAR( FAAU.FAAU_DTFIM, 'DD/MM/YYYY' ) AS TERMINO, 
									FAAU.FAAU_NITMPDIARIO AS TMPDIARIO
							FROM  BIOMETRIA.FAAU_FUNCAUSENCIAAUTORIZADA FAAU 
							INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI 
													ON FUNI.FUNI_ID=FAAU.FUNI_ID 
							INNER JOIN  BIOMETRIA.TAAU_TIPOAUSENCIAAUTORIZADA TAAU 
													ON TAAU.TAAU_ID=FAAU.TAAU_ID
						WHERE FUNI.PMS_IDPMSPESSOA='$sshd'
							    AND FAAU.FAAU_DTFIM >= TO_DATE( '$inicio', 'YYYYMMDD' )
						ORDER BY FAAU.FAAU_DTINI";
	}
//	índice dos funcionários de um autorizador
//	query=funcindex&autid=funi_id
if( $qry == "funcindex" )
	{
	if( !isset( $_GET["autsshd"] ) )
		{
		echo	'{ "data": [{"erro": "parametro autid obrigatorio"}] }';
		return;
		}
	$autsshd = $_GET["autsshd"];
	$sql	=	
		"SELECT FUNI.FUNI_ID AS IDFUNC, FUNI.PMS_IDPMSPESSOA AS SSHDFUNC, VUPU.UOR_DLSIGLAUNIDADE AS UNIDADE, 
       (SELECT VPSS.NOME FROM SAU.VWPESSOA_SSHD VPSS
         WHERE VPSS.REGISTRO_FUNCIONAL_ATIVO = 1 AND
               VPSS.IUN = FUNI.PMS_IDPMSPESSOA AND
               ROWNUM = 1) AS NOFUNC, 
        TO_CHAR( UOFM.UOFM_DTREFERENCIA, 'DD/MM/YYYY') AS DTUFECHAMENTO, 
        SUM(CASE WHEN FDTR.TSDT_ID IS NULL THEN 1 ELSE 0 END) AS QTOK,
        SUM(CASE WHEN FDTR.TSDT_ID = 1 THEN 1 ELSE 0 END) AS QTPENDENTE, 
        SUM(CASE WHEN FDTR.TSDT_ID = 2 THEN 1 ELSE 0 END) AS QTACEITO,
        SUM(CASE WHEN FDTR.TSDT_ID = 3 THEN 1 ELSE 0 END) AS QTNEGADO, 
        SUM(CASE WHEN FDTR.TSDT_ID = 4 THEN 1 ELSE 0 END) AS QTANALISE
		FROM BIOMETRIA.FUNI_FUNCIONARIO FUNIA
		INNER JOIN BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAU ON
										FUAU.FUNI_ID = FUNIA.FUNI_ID
		INNER JOIN (SELECT FUAUI.PMS_IDSAUUOR, UOFMI.UOFM_ID, UOFMI.UOFM_DTREFERENCIA
									FROM BIOMETRIA.UOFM_UORFECHAMENTOMENSAL UOFMI
									INNER JOIN  BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAUI ON
															FUAUI.FUAU_ID = UOFMI.FUAU_ID) UOFM ON
															UOFM.PMS_IDSAUUOR = FUAU.PMS_IDSAUUOR                  
		INNER JOIN  BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL FUOR ON
								FUOR.FUOR_DTINICIO <= SYSDATE AND
								(FUOR.FUOR_DTFIM >= UOFM.UOFM_DTREFERENCIA OR 
										FUOR.FUOR_DTFIM IS NULL) AND
								FUOR.PMS_IDSAUUOR = FUAU.PMS_IDSAUUOR AND
								FUOR.FUNI_ID <> FUNIA.FUNI_ID
		INNER JOIN  SAU.VWUORPUBLICA VUPU ON
								VUPU.UOR_IDUNIDADEORGANIZACIONAL = FUOR.PMS_IDSAUUOR
		INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
								FUNI.FUNI_ID = FUOR.FUNI_ID
		INNER JOIN  BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR ON
								FRTR.FRTR_DTINICIO <= SYSDATE AND
								(FRTR.FRTR_DTFIM >= UOFM.UOFM_DTREFERENCIA OR
										 FRTR.FRTR_DTFIM IS NULL) AND
								FRTR.FUNI_ID = FUOR.FUNI_ID     
		INNER JOIN  BIOMETRIA.FDTR_FUNCIONARIODIATRABALHO FDTR ON
								FDTR.FDTR_DTREFERENCIA BETWEEN UOFM.UOFM_DTREFERENCIA AND SYSDATE AND
								FDTR.FRTR_ID = FRTR.FRTR_ID
		WHERE SYSDATE BETWEEN FUAU.FUAU_DTINICIO AND NVL(FUAU.FUAU_DTFIM, TO_DATE('31/12/3000', 'DD/MM/YYYY')) AND
					FUNIA.PMS_IDPMSPESSOA = '$autsshd' AND
					UOFM.UOFM_ID = (SELECT UOFMF.UOFM_ID FROM 
													(SELECT UOFMI.UOFM_ID, FUAUI.PMS_IDSAUUOR, UOFMI.UOFM_DTREFERENCIA
														FROM BIOMETRIA.UOFM_UORFECHAMENTOMENSAL UOFMI
														INNER JOIN  BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAUI ON
																				FUAUI.FUAU_ID = UOFMI.FUAU_ID
														ORDER BY UOFMI.UOFM_DTREFERENCIA DESC) UOFMF
												WHERE UOFMF.PMS_IDSAUUOR = FUAU.PMS_IDSAUUOR AND
																ROWNUM = 1)
		GROUP BY  FUNI.FUNI_ID, FUNI.PMS_IDPMSPESSOA, VUPU.UOR_DLSIGLAUNIDADE, 
							UOFM.UOFM_DTREFERENCIA
		ORDER BY  UNIDADE, QTPENDENTE DESC";
	}
//	pendencias( sshd, dtini, dtfim, ok, pen, ana, ace, neg, sshdfunc )
//	sshd								sshd do autorizador
//	dtini/dtfim					período
//	ok/pen/ana/ace/neg	status desejados
//	sshdfunc						seleciona apenas este funcionário
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
	if( !isset( $_GET["sshdfunc"] ) )
		$sshdfunc = $_GET["sshdfunc"];
	else
		$sshdfunc = null;
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
	$sshdfunc = $_GET["sshdfunc"];
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
													FDTM.FDTR_ID = FDTR.FDTR_ID ";
	if( $sshdfunc == null )
		$sql .=	"WHERE       FUNI.PMS_IDPMSPESSOA <> '$sshd'";
	else
		$sql .= "WHERE       FUNI.PMS_IDPMSPESSOA = '$sshdfunc'";
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
