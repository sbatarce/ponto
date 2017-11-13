<?php
//	seleção de dados com retorno para o DataTables
//	funcfuor		-	funcionários em uma UOR do ponto
//	funuorbio		-	tabela de pessoas em uma UOR do SAU com contagem de BIOMETRIA
//	autfuuor		-	tabela de funcionários de uma UOR para inclusão
//	ausaut			-	tabela de ausências autorizadas de um funcionário
//	funcindex		-	índice dos funcionários de um autorizador com contagem de eventos
//	pendencias	- obtem todas as pendencias de um sshd
//	funaces			-	dados de acesso dos funcionários comuns
//	funcapar		-	funcionários da funi em um aparelho dado pelo apalid
//	fuornapar		- funcionários de uma FUOR que não esteja no IDAPAL
header('Content-Type: text/html; charset=UTF8');

if( !isset( $_GET["query"] )  )
	{
	echo	'{ "data": [{"erro": "parametro query obrigatório"}] }';
	return;
	}
//	prepara o select
$qry = $_GET["query"];
$dbg = FALSE;
if( isset( $_GET["dbg"] ) )
	$dbg = TRUE;
$sql	=	"";

////////////////////////////////////////////////////////////////////////////////
//	xpto	- 
if( $qry == "xpto" )
	{
	if( !isset( $_GET["xpto"] ) )
		{
		echo	'{ "data": [{"erro": "parametro xpto obrigatorio"}] }';
		return;
		}
	$xpto = $_GET["xpto"];
	
	$sql	=	"";
	}
	
////////////////////////////////////////////////////////////////////////////////
//	funcfuor		-	funcionários em uma UOR do ponto
//	
if( $qry == "funcfuor" )
	{
	if( !isset( $_GET["uor"] ) )
		{
		echo	'{ "data": [{"erro": "parametro uor obrigatorio"}] }';
		return;
		}
	$uor = $_GET["uor"];
	
	$sql	=	"SELECT	FUNI.PMS_IDPMSPESSOA AS SSHD,
									(SELECT PESS.NOME FROM SAU.VWPESSOA_SSHD PESS 
										WHERE PESS.REGISTRO_FUNCIONAL_ATIVO = 1 AND
													PESS.IUN = FUNI.PMS_IDPMSPESSOA AND
													ROWNUM = 1) AS NOME,
									TO_CHAR( FSHM.DTFECH, 'DD/MM/YYYY' ) AS FECHAMENTO, 0 AS FECHAR
						FROM        BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL FUOR
						INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
												FUNI.FUNI_ID=FUOR.FUNI_ID AND
												FUNI.FUNI_STATIVO=1
						INNER JOIN  ( SELECT FUNI_ID AS ID, MAX( FSHM_DTREFERENCIA ) AS DTFECH
														FROM BIOMETRIA.FSHM_FUNCSALDOHORAMENSAL 
														GROUP BY FUNI_ID) FSHM ON
												FSHM.ID=FUNI.FUNI_ID
						WHERE FUOR.FUOR_DTFIM IS NULL AND
									FUOR.PMS_IDSAUUOR=$uor";
	}
	
////////////////////////////////////////////////////////////////////////////////
//	uorautos	- todos os autorizadores de uma determinada FUOR
//			uorid	-	PMS_IDSAUUOR
if( $qry == "uorautos" )
	{
	if( !isset( $_GET["uorid"] ) )
		{
		echo	'{ "data": [{"erro": "parametro uorid obrigatorio"}] }';
		return;
		}
	$uorid = $_GET["uorid"];
	/*
	$sql	=	"SELECT	FUAU.FUAU_ID AS FUAUID, FUAU.FUNI_ID, PESS.IUN AS SSHD, PESS.NOME, 
									TO_CHAR( FUAU.FUAU_DTINICIO, 'DD/MM/YYYY') AS INICIO, 
									TO_CHAR( FUAU.FUAU_DTFIM, 'DD/MM/YYYY') AS TERMINO
						FROM        BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAU
						INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
												FUNI.FUNI_ID=FUAU.FUNI_ID
						INNER JOIN  (SELECT IUN, NOME 
													FROM SAU.VWPESSOA_SSHD 
													WHERE REGISTRO_FUNCIONAL_ATIVO = 1 ) PESS ON
												IUN = FUNI.PMS_IDPMSPESSOA
						WHERE FUAU.PMS_IDSAUUOR=$uorid
					  ORDER BY PESS.NOME, FUAU.FUAU_DTINICIO";
	 */
	$sql	=	"SELECT	FUAU.FUAU_ID AS FUAUID, FUAU.FUNI_ID, FUNI.PMS_IDPMSPESSOA AS SSHD, 
									(SELECT VPSS.NOME
											FROM  SAU.VWPESSOA_SSHD VPSS
											WHERE VPSS.IUN = FUNI.PMS_IDPMSPESSOA
											ORDER BY VPSS.REGISTRO_FUNCIONAL_ATIVO DESC
											FETCH FIRST ROW ONLY) AS NOME,
									TO_CHAR(FUAU.FUAU_DTINICIO, 'DD/MM/YYYY') AS INICIO, 
									TO_CHAR(FUAU.FUAU_DTFIM, 'DD/MM/YYYY') AS TERMINO
						FROM        BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAU
						INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
												FUNI.FUNI_ID = FUAU.FUNI_ID
						WHERE FUAU.PMS_IDSAUUOR = $uorid
						ORDER BY	NVL(FUAU.FUAU_DTFIM, SYSDATE) DESC, 
											FUAU.FUAU_DTINICIO DESC, NOME";
	}
	
////////////////////////////////////////////////////////////////////////////////
//	fuornapar - funcionários de uma FUOR que não esteja no IDAPAL
if( $qry == "fuornapar" )
	{
	if( !isset( $_GET["idapal"] ) )
		{
		echo	'{ "data": [{"erro": "parametro idapal obrigatorio"}] }';
		return;
		}
	$idapal = $_GET["idapal"];
	
	if( !isset( $_GET["fuor"] ) )
		{
		echo	'{ "data": [{"erro": "parametro fuor obrigatorio"}] }';
		return;
		}
	$fuor = $_GET["fuor"];
	
	$sql	=	"SELECT FUNI.FUNI_ID, FUNI.PMS_IDPMSPESSOA AS SSHD, VFAT.NOME, 
									VFAT.IDUOR AS IDUORSAU, VFAT.DCSIGLAUOR AS SIGLAUORSAU,
									RETR.RETR_ID AS IDRETR, RETR.RETR_DLNOME AS REGIME,
									FUOR.PMS_IDSAUUOR AS IDUORPONTO, 
									SUOR.UOR_DLSIGLAUNIDADE AS SIGALUORPONTO,
									(SELECT COUNT(1) 
											FROM BIOMETRIA.PEBI_PESSOABIOMETRIA PEBI
											WHERE PEBI.SIIN_ID = 100 AND 
														PEBI.PMS_IDPMSPESSOA = 
														TO_NUMBER(SUBSTR(FUNI.PMS_IDPMSPESSOA, 2, 8))) AS QTBIO
						FROM	BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL FUOR 
						INNER JOIN	BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
												FUNI.FUNI_ID = FUOR.FUNI_ID
						INNER JOIN	BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR ON
												TRUNC(SYSDATE + 1) BETWEEN FRTR.FRTR_DTINICIO AND 
												NVL(FRTR.FRTR_DTFIM, TRUNC(SYSDATE + 1)) AND
												FRTR.FUNI_ID = FUOR.FUNI_ID
						INNER JOIN	BIOMETRIA.RETR_REGIMETRABALHO RETR ON
												RETR.RETR_ID = FRTR.RETR_ID

					 -- PUS LEFT JOIN POR NÃO TER COMO CONTROLAR QUANDO UM SSHD OU 
					 -- UMA UOR SÃO CANCELADAS NO SAU. 
					 -- NESSES CASO, O ERRO FICARÁ VISÍVEL NO SISPONTO E O FUNCIONÁRIO NÃO DEIXARÁ 
					 -- DE APARECER NO SISTEMA. SE PUSERMOS INNER JOIN A QUERY FICA MUITO MAIS LENTA, 
					 -- MAS TEREMOS QUE GARANTIR QUE UM FUNCIONÁRIO OU UMA UOR NÃO SERÃO INATIVADAS 
					 -- SEM QUE O SISPONTO SEJA MANTIDO!!!
					 -- ESSA CONDIÇÃO SERVE PARA O SISTEMA INTEIRO!!!!

						LEFT JOIN		SAU.VWUORPUBLICA SUOR ON
												SUOR.UOR_IDUNIDADEORGANIZACIONAL = FUOR.PMS_IDSAUUOR
						LEFT JOIN		BIOMETRIA.VWFUNCIONARIOATIVO VFAT ON
												VFAT.IUN = FUNI.PMS_IDPMSPESSOA
						WHERE	TRUNC(SYSDATE + 1) BETWEEN 
									FUOR.FUOR_DTINICIO AND NVL(FUOR.FUOR_DTFIM, TRUNC(SYSDATE + 1)) AND
									FUOR.PMS_IDSAUUOR = $fuor AND

					 -- IDENTIFICA SE FUNCIONÁRIO ASSOCIADO À UOR DA FUOR ESTÁ NO APARELHO. 
					 -- TEMOS QUE LER A LOTR PORQUE É ELA QUE POSSUI O APAL.
					 -- QUANDO RETIRARMOS A LOTR, A PRÓPRIA FLTR TERÁ ESSE ID.

									NOT EXISTS (SELECT FLTR.FUNI_ID
																FROM BIOMETRIA.FLTR_FUNCIONARIOLOCALTRABALHO FLTR
																		 INNER JOIN BIOMETRIA.LOTR_LOCALTRABALHO LOTR ON
																								LOTR.APAL_ID = $idapal AND
																								LOTR.LOTR_ID = FLTR.LOTR_ID
																WHERE FLTR.FUNI_ID = FUOR.FUNI_ID)
						ORDER BY NOME";
	}
	
////////////////////////////////////////////////////////////////////////////////
//	funcapar - funcionário da funi em um aparelho dado o idapal
if( $qry == "funcapar" )
	{
	if( !isset( $_GET["idapal"] ) )
		{
		echo	'{ "data": [{"erro": "parametro idapal obrigatorio"}] }';
		return;
		}
	$idapal = $_GET["idapal"];
	
	$sql	=	"SELECT FUNI.FUNI_ID, FUNI.PMS_IDPMSPESSOA AS SSHD,
									FUOR.PMS_IDSAUUOR AS FUOR, (SELECT PESS.NOME
																								FROM SAU.VWPESSOA_SSHD PESS 
																							 WHERE PESS.REGISTRO_FUNCIONAL_ATIVO = 1 AND
																										 PESS.IUN = FUNI.PMS_IDPMSPESSOA AND
																										 ROWNUM = 1) AS NOME, 
									VUOR.UOR_IDUNIDADEORGANIZACIONAL AS IDUORPONTO, 
									VUOR.UOR_DLSIGLAUNIDADE AS SIGLAUORPONTO,
									(SELECT COUNT(1) 
										 FROM BIOMETRIA.PEBI_PESSOABIOMETRIA PEBI
										WHERE PEBI.SIIN_ID = 100 AND 
													PEBI.PMS_IDPMSPESSOA = TO_NUMBER(SUBSTR(FUNI.PMS_IDPMSPESSOA, 2, 8))) AS bios
						 FROM BIOMETRIA.LOTR_LOCALTRABALHO LOTR
									INNER JOIN BIOMETRIA.FLTR_FUNCIONARIOLOCALTRABALHO FLTR ON
														 FLTR.LOTR_ID = LOTR.LOTR_ID
									INNER JOIN BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
														 FUNI.FUNI_ID = FLTR.FUNI_ID 
									INNER JOIN BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL FUOR ON
														 FUOR.FUNI_ID = FUNI.FUNI_ID
									INNER JOIN SAU.VWUORPUBLICA VUOR ON 
														 VUOR.UOR_IDUNIDADEORGANIZACIONAL = FUOR.PMS_IDSAUUOR
						WHERE LOTR.APAL_ID=$idapal";
	}
	
////////////////////////////////////////////////////////////////////////////////
//	funuorbio	tabela de pessoas em uma UOR do SAU (VWFUNCIONARIOATIVO) 
//						com contagem de BIOMETRIA
//						
//	Parametros 
//			iduor		-	id da UOR a selecionar pessoas
//			todos		- traz ou não as pessoas ja alocadas em iduor
if( $qry == "funuorbio" )
	{
	if( !isset( $_GET["iduor"] ) )
		{
		echo	'{ "data": [{"erro": "parametro iduor obrigatorio"}] }';
		return;
		}
	$iduor = $_GET["iduor"];

	if( !isset( $_GET["sguor"] ) )
		{
		echo	'{ "data": [{"erro": "parametro sguor obrigatorio"}] }';
		return;
		}
	$sguor = $_GET["sguor"];

	if( isset( $_GET["janafuor"] ) )
		{
		$sql =	"SELECT	FUNI.FUNI_ID, IUN, NOME, IDUOR AS IDUORSAU, DCSIGLAUOR AS SIGLAUORSAU,
										RETR.RETR_ID AS IDRETR, RETR.RETR_DLNOME AS REGIME,
										FUOR.PMS_IDSAUUOR AS IDUORPONTO, SUOR.UOR_DLSIGLAUNIDADE AS SIGALUORPONTO,
										(SELECT COUNT(1)
											FROM BIOMETRIA.PEBI_PESSOABIOMETRIA PEBI
											WHERE PEBI.SIIN_ID = 100 AND
														PEBI.PMS_IDPMSPESSOA = TO_NUMBER(SUBSTR(VFAT.IUN, 2, 8))) AS QTBIO
								FROM BIOMETRIA.VWFUNCIONARIOATIVO VFAT
								LEFT JOIN BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
													FUNI.FUNI_STATIVO = 1 AND
													FUNI.PMS_IDPMSPESSOA = VFAT.IUN
								LEFT JOIN BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR ON
													TRUNC(SYSDATE + 1) BETWEEN FRTR.FRTR_DTINICIO AND NVL(FRTR.FRTR_DTFIM, TRUNC(SYSDATE + 1)) AND
													FRTR.FUNI_ID = FUNI.FUNI_ID
								LEFT JOIN BIOMETRIA.RETR_REGIMETRABALHO RETR ON
													RETR.RETR_ID = FRTR.RETR_ID
								LEFT JOIN BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL FUOR ON
													TRUNC(SYSDATE + 1) BETWEEN FUOR.FUOR_DTINICIO AND NVL(FUOR.FUOR_DTFIM, TRUNC(SYSDATE + 1)) AND
													FUOR.FUNI_ID = FUNI.FUNI_ID
								LEFT JOIN SAU.VWUORPUBLICA SUOR ON
													SUOR.UOR_IDUNIDADEORGANIZACIONAL=FUOR.PMS_IDSAUUOR
								WHERE ((VFAT.IDUOR = $iduor OR
												 VFAT.DCSIGLAUOR = '$sguor') AND
												 FUNI.FUNI_ID IS NULL) OR
												FUOR.PMS_IDSAUUOR = $iduor
								ORDER BY NOME";
		}
	else
		{
		$sql = "SELECT  FUNI.FUNI_ID, VFAT.IUN, VFAT.NOME, VFAT.IDUOR AS IDUORSAU, VFAT.DCSIGLAUOR AS SIGLAUORSAU,
										NULL AS IDRETR, NULL AS REGIME,
										NULL AS IDUORPONTO, NULL AS SIGALUORPONTO,
										(SELECT COUNT(1) 
												FROM  BIOMETRIA.PEBI_PESSOABIOMETRIA PEBI
												WHERE PEBI.SIIN_ID = 100 AND 
															PEBI.PMS_IDPMSPESSOA = TO_NUMBER(SUBSTR(VFAT.IUN, 2, 8))) AS QTBIO
							FROM  BIOMETRIA.VWFUNCIONARIOATIVO VFAT
										LEFT JOIN BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
															FUNI.FUNI_STATIVO = 1 AND
															FUNI.PMS_IDPMSPESSOA = VFAT.IUN
							WHERE VFAT.IDUOR = $iduor AND
										FUNI.FUNI_ID IS NULL
							ORDER BY NOME";
		}
	}
	
////////////////////////////////////////////////////////////////////////////////
//	autfuuor	tabela de funcionários de uma UOR para inclusão
//						excluidos os funcionários já presentes na FUNI
//	Parametros 
//			iduor		-	id da UOR do SAU a selecionar pessoas
if( $qry == "autfuuor" )
	{
	if( !isset( $_GET["iduor"] ) )
		{
		echo	'{ "data": [{"erro": "parametro iduor obrigatorio"}] }';
		return;
		}
	$iduor = $_GET["iduor"];
	
	$sql = "SELECT	FUNI.FUNI_ID, VFAT.IUN, VFAT.NOME, VFAT.IDUOR AS IDUORSAU, 
									VFAT.DCSIGLAUOR AS SIGLAUORSAU,
									NULL AS IDRETR, NULL AS REGIME,
									NULL AS IDUORPONTO, NULL AS SIGALUORPONTO,
									(SELECT COUNT(1) 
										 FROM BIOMETRIA.PEBI_PESSOABIOMETRIA PEBI
										WHERE PEBI.SIIN_ID = 100 AND 
													PEBI.PMS_IDPMSPESSOA = TO_NUMBER(SUBSTR(VFAT.IUN, 2, 8))) AS QTBIO
						FROM			BIOMETRIA.VWFUNCIONARIOATIVO VFAT
						LEFT JOIN BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
											FUNI.FUNI_STATIVO = 1 AND
											FUNI.PMS_IDPMSPESSOA = VFAT.IUN
						WHERE VFAT.IDUOR = $iduor AND
									FUNI.FUNI_ID IS NULL
						ORDER BY NOME";
	}
	
////////////////////////////////////////////////////////////////////////////////
//	autcadas	tabela de funcionários de um autorizador para cadastro
//	Parametros SSHD de um autorizador
if( $qry == "autcadas" )
	{
	if( !isset( $_GET["sshd"] ) )
		{
		echo	'{ "data": [{"erro": "parametro sshd obrigatorio"}] }';
		return;
		}
	$sshd = $_GET["sshd"];

	$sql = "SELECT  SUOR.UOR_DLSIGLAUNIDADE AS UNIDADE, FUNI.FUNI_ID AS IDFUNI,
									FUNI.PMS_IDPMSPESSOA AS SSHD, 
									FUOR.PMS_IDSAUUOR as IDLOTADO, PESS.NOME AS NOFUNC,
									RETR.RETR_ID AS IDREG, RETR.RETR_DLNOME AS NOREG,
                  ( SELECT TO_CHAR( MAX( FSHM_DTREFERENCIA ), 'DD/MM/YYYY' )
                      FROM BIOMETRIA.FSHM_FUNCSALDOHORAMENSAL
                      WHERE FUNI_ID=FUNI.FUNI_ID ) AS DTFECHA
						FROM        BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAU
						INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNIA ON
												FUNIA.FUNI_ID=FUAU.FUNI_ID AND
												FUNIA.PMS_IDPMSPESSOA='$sshd'
						INNER JOIN  BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL FUOR ON
												FUOR.PMS_IDSAUUOR=FUAU.PMS_IDSAUUOR AND
												FUOR.FUOR_DTFIM IS NULL
						INNER JOIN  SAU.VWUORPUBLICA SUOR ON
												SUOR.UOR_IDUNIDADEORGANIZACIONAL=FUOR.PMS_IDSAUUOR AND
												SUOR.UOR_DTFINAL IS NULL
						INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI ON
												FUOR.FUNI_ID=FUNI.FUNI_ID
						INNER JOIN  SAU.VWPESSOA_SSHD PESS ON
												PESS.IUN = FUNI.PMS_IDPMSPESSOA AND 
												PESS.REGISTRO_FUNCIONAL_ATIVO = 1
						INNER JOIN  BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR ON
												FRTR.FUNI_ID=FUNI.FUNI_ID AND
												FRTR.FRTR_DTFIM IS NULL
						INNER JOIN  BIOMETRIA.RETR_REGIMETRABALHO RETR ON
												RETR.RETR_ID=FRTR.RETR_ID
						WHERE   FUAU.FUAU_DTFIM IS NULL
						ORDER BY  FUOR.PMS_IDSAUUOR, PESS.NOME";
	}
////////////////////////////////////////////////////////////////////////////////
//	ausaut	tabela de ausências autorizadas de um funcionário
//	Parametros SSHD & data de início
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
	$sql = "select  TAAU.TAAU_ID AS IDTIPO, TAAU.TAAU_DLAUSENCIAAUTORIZADA as TIPO, 
									TAAU.TAAU_STMARCACAO as PODE, 
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
////////////////////////////////////////////////////////////////////////////////
//	funcindex	-	índice dos funcionários de um autorizador com contagem de eventos
//	query=funcindex&autid=funi_id
if( $qry == "funcindex" )
	{
	if( !isset( $_GET["autsshd"] ) )
		{
		echo	'{ "data": [{"erro": "parametro autid obrigatorio"}] }';
		return;
		}
	$autsshd = $_GET["autsshd"];
	$sql	=	"SELECT	FUNI.FUNI_ID AS IDFUNC, FUNI.PMS_IDPMSPESSOA AS SSHDFUNC, 
									VUPU.UOR_DLSIGLAUNIDADE AS UNIDADE, 
									(SELECT VPSS.NOME 
										FROM SAU.VWPESSOA_SSHD VPSS 
										WHERE VPSS.REGISTRO_FUNCIONAL_ATIVO = 1 AND 
													VPSS.IUN = FUNI.PMS_IDPMSPESSOA AND 
													ROWNUM = 1) AS NOFUNC, 
									TO_CHAR(FSHM.FSHM_DTREFERENCIA, 'DD/MM/YYYY') AS DTUFECHAMENTO, 
										SUM(CASE WHEN FDTR.FDTR_ID IS NOT NULL AND FDTR.TSDT_ID IS NULL THEN 1 ELSE 0 END) AS QTOK, 
										SUM(CASE WHEN FDTR.TSDT_ID = 1 THEN 1 ELSE 0 END) AS QTPENDENTE, 
										SUM(CASE WHEN FDTR.TSDT_ID = 2 THEN 1 ELSE 0 END) AS QTACEITO, 
										SUM(CASE WHEN FDTR.TSDT_ID = 3 THEN 1 ELSE 0 END) AS QTNEGADO, 
										SUM(CASE WHEN FDTR.TSDT_ID = 4 THEN 1 ELSE 0 END) AS QTANALISE 
						FROM        BIOMETRIA.FUNI_FUNCIONARIO FUNIA 
						INNER JOIN  BIOMETRIA.FUAU_FUNCIONARIOAUTORIZADOR FUAU ON 
												SYSDATE BETWEEN FUAU.FUAU_DTINICIO AND 
														NVL(FUAU.FUAU_DTFIM, TO_DATE('31/12/3000', 'DD/MM/YYYY')) AND 
												FUAU.FUNI_ID = FUNIA.FUNI_ID 
						INNER JOIN  BIOMETRIA.FUOR_FUNCUNIDADEORGANIZACIONAL FUOR ON 
												FUOR.FUOR_DTFIM IS NULL AND 
												FUOR.PMS_IDSAUUOR = FUAU.PMS_IDSAUUOR AND 
												FUOR.FUNI_ID <> FUNIA.FUNI_ID 
						INNER JOIN  SAU.VWUORPUBLICA VUPU ON 
												VUPU.UOR_IDUNIDADEORGANIZACIONAL = FUOR.PMS_IDSAUUOR 
						INNER JOIN  BIOMETRIA.FUNI_FUNCIONARIO FUNI ON 
												FUNI.FUNI_ID = FUOR.FUNI_ID 
						INNER JOIN  BIOMETRIA.FRTR_FUNCIONARIOREGIMETRABALHO FRTR ON 
												FRTR.FRTR_DTFIM IS NULL AND 
												FRTR.FUNI_ID = FUOR.FUNI_ID 
						INNER JOIN  BIOMETRIA.FSHM_FUNCSALDOHORAMENSAL FSHM ON
												FSHM.FUNI_ID = FUNI.FUNI_ID AND
												FSHM.FSHM_DTREFERENCIA = 
													(SELECT FSHM1.FSHM_DTREFERENCIA
														FROM 
															(SELECT FSHMI.FUNI_ID, FSHMI.FSHM_DTREFERENCIA
																FROM BIOMETRIA.FSHM_FUNCSALDOHORAMENSAL FSHMI
																ORDER BY FSHMI.FSHM_DTREFERENCIA DESC) FSHM1
														WHERE FSHM1.FUNI_ID = FUNI.FUNI_ID AND
																	ROWNUM = 1) 
						LEFT JOIN   BIOMETRIA.FDTR_FUNCIONARIODIATRABALHO FDTR ON 
												FDTR.FDTR_DTREFERENCIA BETWEEN FSHM.FSHM_DTREFERENCIA AND SYSDATE AND 
												FDTR.FRTR_ID = FRTR.FRTR_ID 
						WHERE     FUNIA.PMS_IDPMSPESSOA = '$autsshd' 
						GROUP BY  FUNI.FUNI_ID, FUNI.PMS_IDPMSPESSOA, VUPU.UOR_DLSIGLAUNIDADE, FSHM.FSHM_DTREFERENCIA 
						ORDER BY  UNIDADE, QTPENDENTE DESC";
	}
////////////////////////////////////////////////////////////////////////////////
//	pendencias - obtem todas as pendencias de um sshd
//	( sshd, dtini, dtfim, ok, pen, ana, ace, neg, sshdfunc )
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
		$sql .=	"WHERE       FUNI.PMS_IDPMSPESSOA <> '$sshd' ";
	else
		$sql .= "WHERE       FUNI.PMS_IDPMSPESSOA = '$sshdfunc' ";
	$sql .= "ORDER BY FDTR.FDTR_DTREFERENCIA DESC";
	}
////////////////////////////////////////////////////////////////////////////////
//	funaces		-	dados de acesso dos funcionários comuns
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
	$sql	=	"SELECT		FUNI.FUNI_ID, FDTR.FDTR_ID, FDTR.TSDT_ID, 
										FUNI.PMS_IDPMSPESSOA as SSHD, PESS.NOME,
										to_char( FDTR.FDTR_DTREFERENCIA, 'DD/MM/YYYY' ) as Data,
										(SELECT LISTAGG(FDTE.FDTE_ID, ';') 
														WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO)
												FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
												WHERE FDTE.FDTR_ID = FDTR.FDTR_ID) AS FDTEIDS,
										(SELECT LISTAGG(TO_CHAR(FDTE.FDTE_DTHORARIO, 'HH24:MI'), ';') 
														WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO)
												FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
												WHERE FDTE.FDTR_ID = FDTR.FDTR_ID) AS HORARIOS,
										(SELECT LISTAGG(NVL( FDTE.TORG_ID, '0'), ';') 
														WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO)
												FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
												WHERE FDTE.FDTR_ID = FDTR.FDTR_ID) AS OPERACOES,
										(SELECT LISTAGG(NVL( FDTE.TORE_ID, '0'), ';') 
														WITHIN GROUP (ORDER BY FDTE.FDTE_DTHORARIO)
												FROM BIOMETRIA.FDTE_FUNCDIATRABALHOREGISTRO FDTE
												WHERE FDTE.FDTR_ID = FDTR.FDTR_ID) AS ORIGENS,
										to_char( to_date
												(BIOMETRIA.SF_CALCULATEMPODIATRABALHO( 
														FUNI.FUNI_ID, FDTR.FDTR_DTREFERENCIA )*60, 'SSSSS' ), 
														'HH24:MI' ) AS TOTAL,
										(SELECT LISTAGG(TMEN.TMEN_DCMENS, '; ') 
														WITHIN GROUP (ORDER BY FDTN.FDTN_ID)
                        FROM				BIOMETRIA.FDTN_FDTR_TMEN FDTN
                        INNER JOIN	BIOMETRIA.TMEN_TIPOMENSAGEM TMEN ON
                                    TMEN.TMEN_ID = FDTN.TMEN_ID
                        WHERE FDTN.FDTR_ID = FDTR.FDTR_ID) AS TIPOMENSAGEM, 
										FDTM_ID, FDTM.FDTM_DLMENS,
										(SELECT	LISTAGG(	TAAU.TAAU_DLAUSENCIAAUTORIZADA || '=' || 
																			FDTF.FDTF_NITMPREAL, ';')
														WITHIN GROUP( ORDER BY FDTF.FDTF_NITMPREAL )
											FROM				BIOMETRIA.FDTF_FUNCDIATRABALHO_FAAU FDTF
											INNER JOIN	BIOMETRIA.FAAU_FUNCAUSENCIAAUTORIZADA FAAU ON 
																	FAAU.FAAU_ID=FDTF.FAAU_ID
											INNER JOIN	BIOMETRIA.TAAU_TIPOAUSENCIAAUTORIZADA TAAU ON 
																	TAAU.TAAU_ID=FAAU.TAAU_ID 
											WHERE FDTR_ID=FDTR.FDTR_ID) AS AUTORIZADAS,
										CASE FUCO_DCDBCR 
														WHEN 'DB' THEN -FUCO_NITMP
														WHEN 'CR' THEN FUCO_NITMP END  AS CORRECAO, 
										BIOMETRIA.SF_CALCULASALDOINICIAL( 
															FUNI.FUNI_ID, FDTR.FDTR_DTREFERENCIA+1) AS SALDO
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
////////////////////////////////////////////////////////////////////////////////	
////////////////////////////////////////////////////////////////////////////////
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
if( isset( $_SERVER['PHP_AUTH_USER'] ) )
	{
	$userb = $_SERVER['PHP_AUTH_USER'];
	$passb = $_SERVER['PHP_AUTH_PW'];
	}
	
include 'ORAConn.php';

if( $dbg )
	{
	echo "user=$userb, pas=$passb, amb=$amb, chset=$chset<br>";
	}
//	obre o oracle
$ora = new ORAConn();
if( $dbg )
	{
	$ora->setDebug();
	}
$res = $ora->connect( $userb, $passb, $amb, $chset, "" );
if( $res != "OK" )
	{
	echo $res;
	return;
	}
if( $dbg )
	{
	echo "resultado do connect:";
	var_dump($res);
	}
//	executa o query
$res = $ora->execSelect($sql);
if( $dbg )
	{
	echo "resultado do execSelect:";
	var_dump($res);
	}
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
