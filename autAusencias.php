<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    <title>Ponto do funcionário</title>
<?php
include 'partes/Head.php';
?>
		<!-- Favicon -->
		<link rel="shortcut icon" href="/imagens/PMSICO.png">
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link href="bootstrap-3.3.1/dist/css/bootstrap-switch.css" rel="stylesheet">
    <style>
			.lab{
				font-size: 18px;
				margin-top: 10px;
				margin-left:30px;
				}
			.inp{
				font-size: 18px;
				margin-left: 15px;
				}
		</style>
	</head>
	
	<body onload="javascript:titulo( '<h4>Autorização de Ausências e Correções de Saldo</h4>' );">
<?php
include 'partes/MenuPri.php';
include 'partes/Cabec.php';
include 'partes/pageheader.php';
include 'partes/pagebody.php';
?>
<!-- Conteúdo específico da página -->
<div class='row form-group' style='margin-top: 10px'>
	<label for="uors" class='lab' style='width:100%; ' >UORS: 
	<input style='width:40%; margin-top: 0px; ' 
				 class='input-small inp' 
				 id="uors" title="Escolha uma das UORS que você autoriza"/></label>
	<br/>
	<div id="divfuncs">
		<label for="funcs" class='lab' style='width:100%; ' >Funcionários:
		<input style='width:40%; ' class='input-small inp' 
					 id="funcs" title="Escolha um dos funcionários"/></label>
	</div>
	<br/>
	<div id="dados" class="row linha" style='margin-top: 10px; margin_left: 30px; '>
		<div class="col-lg-6">
			<label class='lab' >Ausência Autorizada</label>
			<br/>
			<label for="dtini" class='lab' style='width: 40%; '>Período de
			<input id="dtini" class='input-small inp' 
						 style='width: 50%; ' readonly /></label>
			<label for="dtfim" class='lab' style='width: 40%; ' >até 
			<input id="dtfim" class='input-small inp' 
						 style='width: 50%; ' readonly /></label>
			<br/>
			<label for="tiaus" class='lab sim' style='width:100%; '>Tipo de ausência: 
			<input style='width:50%; ' class='input-small inp' 
						 id="tiaus" title="Escolha o tipo de ausencia"/></label>
			<br/>
			<label for="qtaus" class='lab sim' style='width:100%; ' >Horas de ausência:
			<input style='width:20%; ' class='input-small inp' 
						 id="qtaus" title="quantidade de horas de ausência a autorizar no formato HH:MM"/>
			</label>
			<br/>
			<button id='btauto' class='btn-lg' style='margin-left:30px; margin-top: 20px;' 
							onclick="javascript:autorizar()">Autorizar</button>
		</div>
		<div class="col-lg-6">
			<label class='lab' >Correções de Saldo</label>
			<br/>
			<div id="divdbcr" class="checkbox-inline make-switch has-switch" 
					 style="font-size: 24px; margin-left:30px " >
				<input id="ckdbcr" type="checkbox" style="font-size: 24px; "
							 data-label-text='DB/CR'
							 data-off-color="danger" data-on-color="success"
							 data-off-text="Subtrair" data-on-text="Adicionar"
							 title="indica se deve-se adicionar ou subtrais a quantidade de horas do saldo">
			</div>
			<br/>
			<label for="dtcor" class='lab' style='width: 100%; ' >Data
			<input id="dtcor" class='input-small inp' style='width: 40%; ' 
						 title="data da aplicação da correção" readonly /></label>
			<br/>
			<label for="qtcor" class='lab' style='width: 100%; ' >Horas
				<input id="qtcor" class='input-small inp' style='width: 20%; ' readonly="true" 
						 title="quantidade de horas a adicionar ou subtrais no formato HH:MM"/></label>
			<br/>
			<button id='btcor' class='btn-lg' style='margin-left:30px; margin-top: 20px;' 
							onclick="javascript:corrigir()">Corrigir</button>

		</div>
	</div>
</div>
		
<?php
include 'partes/Scripts.php';
?>

	<script type="text/javascript" src="bootstrap-3.3.1/dist/js/bootstrap-switch.js"></script>
	<script type="text/javascript" src="partes/geral.js" ></script>
	<script type="text/javascript" src="partes/dteditavel.js" ></script>
	<script type="text/javascript" >
		function logout()
			{
			Deslogar();
			}

		//	funções genéricas
		function toDate( dt )			//	string YYYYMMDD to date
			{
			var a	=	paresInt(dt.substring(0,4));
			var m = paresInt(dt.substring(4,6));
			var d = paresInt(dt.substring(6));
			return new Date( a, m-1, d, 0, 0, 0 );
			}
		
		function toHora( data )
			{
			var hh = data.getHours();
			if( hh < 10 )
				hh = "0" + hh;
			var mm = data.getMinutes();
			var mm = data.getHours();
			if( mm < 10 )
				mm = "0" + mm;
			return hh + ":" + mm;
			}
			
		//	de hh:mm para minutos
		function hhmmToMin( hhmm )
			{
			if( hhmm.substring(2,3) != ":" )
				return -1;
			if( !$.isNumeric(hhmm.substring(0,2)) )
				return -1;
			if( !$.isNumeric(hhmm.substring(3,5)) )
				return -1;

			var hh = Number(hhmm.substring( 0, 2 ));
			var mm = Number(hhmm.substring( 3, 5 ));
			
			if( hh < 0 || hh > 23 )
				return -1;
			if( mm < 0 || mm > 59 )
				return -1;
			
			return hh*60+mm;
			}
			
		function minToHHMM( minutos )
			{
			var hh = Math.floor(Math.abs(minutos)/60);
			var mm = Math.abs(minutos)%60;
			if( hh < 10 )
				hh = "0" + hh;
			if( mm < 10 )
				mm = "0" + mm;
			if( minutos < 0 )
				return "-"+hh+":"+mm;
			else
				return hh+":"+mm;
			}
			
		//	funções da página
		function escoTiaus( tipo, id )
			{
			//	verifica se o tipo de ausência admite setar horas ou não
			url = "partes/queries.php?query=obttaau&taauid=" + id;
			var resu = remoto( url );
			if( resu == null )
				{
				alert( "Falha na obtenção dos dados. Por Favor, tente mais tarde")
				return;
				}
			if( resu.linhas < 1 )
				{
				alert( "Falha na obtenção dos dados. Por Favor, tente mais tarde")
				return;
				}
			if( resu.dados[0].TAAU_STMARCACAO == '0' )
				{
				document.getElementById('qtaus').setAttribute("readonly", true);
				var mins = resu.dados[0].TAAU_NIVARHORAS * 60;
				document.getElementById('qtaus').value = minToHHMM(mins);
				}
			else
				{
				document.getElementById('qtaus').removeAttribute("readonly");
				document.getElementById('qtaus').value = "";
				}
			//
			taauid = id;
			}
			
		function escoFunc( tipo, id )
			{
			if( id == 0 )
				{
				$("#dados").hide();
				funiid	= 0;
				}
			else
				{
				funiid = id;
				limpaDados();
				//	obtem o SSHD do FUNI_ID
				url = "partes/queries.php?query=sshd&funiid=" + id;
				var resu = remoto( url );
				if( resu == null )
					{
					alert( "Falha na obtenção dos dados do funcionário. Por Favor, tente mais tarde")
					return;
					}
				if( resu.linhas < 1 )
					{
					alert( "Falha na obtenção dos dados do funcionário. Por Favor, tente mais tarde")
					return;
					}
				sshdfunc = resu.dados[0].SSHD;
				if( sshdfunc == sshd )
					{
					alert( "Não é permitido autorizar ausências para si mesmo.")
					return;
					}
				$("#dados").show();
				}
			//	obtem a data do ultimo fechamento do funcionario
			var parms = "&sshd=" + sshd;
			var resu = Select( "dtfecha", parms );
			if( resu == null )
				{
				alert( "Falha acessando dados do funcionário. Por favor, tente mais tarde." );
				return;
				}
			var aux = resu.dados[0].DTFECHA;
			var afecha = Number(aux.substring( 0, 4 ));
			var mfecha = Number(aux.substring( 5, 7 ))-1;
			var dfecha = Number(aux.substring( 8 ));
			dtfecha = new Date( afecha, mfecha, dfecha, 0, 0, 0 );
			}
			
		function escoUor( tipo, id )
			{
			if( id == 0 )
				{
				$("#divfuncs").hide();
				$("#dados").hide();
				return;
				}
			uor = id;
			//	obtem o FUAUID
			parms = "&sshd=" + sshd + "&uorid=" + uor;
			var resu = Select( "fuauid", parms );
			if( resu == null )
				{
				alert( "Falha ao acessar dados do Autorizador. Por favor, tente mais tarde" );
				return;
				}
			if( resu.linhas < 1 )
				{
				alert( "Falha ao acessar dados do Autorizador. Por favor, tente mais tarde" );
				return;
				}
			fuauid = parseInt(resu.dados[0].FUAUID);
			
			//	prepara o Select dos funcionários da UOR
			url =	"selectData.php?query=funcsuor&uor=" + id;
			SelInit( "#funcs", url, 0, "Escolha abaixo", escoFunc );
			funiid	=	0;
			$("#divfuncs").show();
			$("#dados").hide();
			}
			
		function limpaDados()
			{
			//	reinicializa as datas
			dtcor = $.datepicker.formatDate("yymmdd", hoje );
			dtfim = $.datepicker.formatDate("yymmdd", hoje );
			dtini = $.datepicker.formatDate("yymmdd", hoje );
			$("#dtcor").val( $.datepicker.formatDate("dd/mm/yy", hoje ) );
			$("#dtfim").val( $.datepicker.formatDate("dd/mm/yy", hoje ) );
			$("#dtini").val( $.datepicker.formatDate("dd/mm/yy", hoje ));
			//	
			$("#qtaus").val("");
			$("#qtcor").val("");
			$('#ckdbcr').bootstrapSwitch('state', true);
			//	reposiciona tiaus
			url =	"selectData.php?query=tiaus";
			SelInit( "#tiaus", url, 0, "Escolha abaixo", escoTiaus );
			taauid = 0;

			flauto = true;
			$(".sim").show();
			$("#btauto").text("Autorizar")
			}
			
		function corrigir()
			{
			var dt = $("#dtcor").datepicker("getDate");
			if( dt < dtfecha )
				{
				var aux = "Correções permitidas somente após " +
							$.datepicker.formatDate("dd/mm/yy", dtfecha ) + 
							" (data do último fechamento).";
				alert( aux );
				return;
				}
			
			var aux = $("#qtcor").val();
			var qtcor = hhmmToMin( aux );
			if( qtcor < 1 )
				{
				alert( "Por favor, especifique uma quantidade de horas válida no formato HH:MM.")
				return;
				}
			var dbcr = "DB";
			if( fldbcr )
				dbcr = "CR";
			//	insere a correção
			parms = "&funiid=" + funiid + "&fuauid=" + fuauid +
							"&dtref=" + $.datepicker.formatDate("yymmdd", dt ) +
							"&dbcr=" + dbcr + "&mins=" + qtcor;
			if( !Insert( "infuco", parms ))
				{
				alert( "Falha ao inserir correção de horas. Por favor, tente mais tarde." );
				return;
				}
			//	limpa e encerra
			limpaDados();
			}
			
		function autorizar()
			{
			//	verifica e insere novas atualizações
			if( taauid == 0 )
				{
				alert( "Por favor, escolha um tipo de ausência.")
				return;
				}
			//
			var aux = $("#qtaus").val();
			var qtaus = hhmmToMin( aux );
			if( qtaus < 0 || qtaus >= 24*60 )
				{
				alert( "Por favor, especifique uma quantidade de horas válida no formato HH:MM.")
				return;
				}
			//	verifica todas as datas
			var ini = $("#dtini").datepicker("getDate");
			if( ini < dtfecha )
				{
				aux = "Inicio das autorizações deve ser superior a " +
							$.datepicker.formatDate("dd/mm/yy", dtfecha ) + 
							" (data do último fechamento).";
				alert( aux );
				return;
				}
			var fim = $("#dtfim").datepicker("getDate");
			if( ini > fim )
				{
				alert( "Por favor, a data final deve ser maior ou igual à inicial.")
				return;
				}
			var flok = true;
			//	passeia por todas as datas para ver a validade delas
			for( var dt=ini; dt<=fim; dt=new Date(dt.getTime()+(1000*60*60*24))  )
				{
				var iso = $.datepicker.formatDate("yymmdd", dt );
				//
				parms = "&sshd=" + sshdfunc + "&data=" + iso + "&taauid=" + taauid;
				var resu = Select( "ausaut", parms );
				if( resu == null )
					{
					alert( "Falha ao acessar dados do funcionário. Por favor, tente mais tarde" );
					return;
					}
				if( resu.status != "OK" )
					{
					alert( "Falha " + resu.erro + " ao acessar dados do funcionário. Por favor, tente mais tarde" );
					return;
					}
				if( resu.linhas > 0 )
					{
					var ale = "No dia " + $.datepicker.formatDate("dd/mm", dt ) +
										" já há este tipo de ausência autorizada. ";
					alert( ale );
					flok	=	false;
					continue;
					}

				//	verifica a somatória de horas do dia
				parms = "&sshd=" + sshdfunc + "&data=" + iso;
				var resu = Select( "horasdia", parms );
				if( resu == null )
					{
					alert( "Falha ao acessar dados do funcionário. Por favor, tente mais tarde" );
					return;
					}
				if( resu.linhas < 1 )
					continue;							//	dia sem trabalho
				var mxdia = parseInt(resu.dados[0].HORASDIA);
				var jaaut = parseInt(resu.dados[0].TOTAUT);
				var toaut = jaaut+qtaus;
				if( toaut > mxdia )
					{
					var ale = "No dia " + $.datepicker.formatDate("dd/mm", dt ) +
										" o acumulado de ausencias autorizadas excede o total " +
										"de horas a trabalhar no dia. ";
					alert( ale );
					flok	=	false;
					continue;
					}
				}
			if( !flok )
				return;
			//	insere a ausência autorizada
			url = "partes/ausAut.php?funiid=" + funiid + "&taauid=" + taauid + 
						"&fuauid=" + fuauid + "&dtini=" + dtini +
						"&dtfim=" + dtfim + "&mins=" + qtaus;
			var resu = remoto( url );
			if( resu.status != "OK"  )
				{
				var err = resu.erro;
				alert( "Falha <" + err + "> ao cadastrar ausencia autorizada. Por favor, tente mais tarde." );
				return;
				}
			//	limpa e encerra
			limpaDados();
			}

		//	tratamento das datas 		
		$( "#dtcor" ).datepicker(
			{
			dateFormat: "dd/mm/yy",
			altFormat: "yymmdd",
			startView: 2,
			todayBtn: true,
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			todayHighlight: true			
			}).on('change.dp', function(e)
				{ 
				var dt = $("#dtcor").datepicker("getDate");
				dtcor = $.datepicker.formatDate("yymmdd", dt );
				});

		$( "#dtini" ).datepicker(
			{
			dateFormat: "dd/mm/yy",
			altFormat: "yymmdd",
			startView: 2,
			todayBtn: true,
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			todayHighlight: true			
			}).on('change.dp', function(e)
				{ 
				var dt = $("#dtini").datepicker("getDate");
				dtini = $.datepicker.formatDate("yymmdd", dt );
				//	copia a data para a dtfim
				dtfim = $.datepicker.formatDate("yymmdd", dt );
				$("#dtfim").val( $.datepicker.formatDate("dd/mm/yy", dt ) );
				});
				
		$( "#dtfim" ).datepicker(
			{
			dateFormat: "dd/mm/yy",
			altFormat: "yymmdd",
			startView: 2,
			todayBtn: true,
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			todayHighlight: true			
			}).on('change.dp', function(e)
				{ 
				var dt = $("#dtfim").datepicker("getDate");
				dtfim = $.datepicker.formatDate("yymmdd", dt );
				});
		
		$("#ckdbcr").on( 'switchChange.bootstrapSwitch', function( evn, state )
			{
			fldbcr = state;
			if( state )
				{
				$("#btcor").css('background-color','green');
				$("#btcor").text("Acrescentar Horas")
				}
			else
				{
				$("#btcor").css('background-color','red');
				$("#btcor").text("Subtrair Horas")
				}
			});
		
		//	variáveis de uso global
		var fldbcr = true;				//	debita/credita horas
		var parms = "";
		var url = "";
		var uor = 0;
		var funiid	=	0;
		var taauid = 0;
		var fuauid = "";
		var dtfecha;
		var sshdfunc = "";
		var sshd = obterCookie( "user" );
		if( sshd == null )
			{
			Deslogar();
			}

		//	acerta as datas
		var hoje = new Date();
		var dtcor = $.datepicker.formatDate("yymmdd", hoje );
		var dtfim = $.datepicker.formatDate("yymmdd", hoje );
		var dtini = $.datepicker.formatDate("yymmdd", hoje );
		$("#dtcor").val( $.datepicker.formatDate("dd/mm/yy", hoje ) );
		$("#dtfim").val( $.datepicker.formatDate("dd/mm/yy", hoje ) );
		$("#dtini").val( $.datepicker.formatDate("dd/mm/yy", hoje ));
		
		//	visibilidades iniciais
		$("#divfuncs").hide();
		$("#dados").hide();
		
		//	prepara a combo de UORS e demais controles
		url =	"selectData.php?query=uorsaut&sshd=" + sshd;
		SelInit( "#uors", url, 0, "Escolha abaixo", escoUor );
		</script>
	</body>	
</html>
