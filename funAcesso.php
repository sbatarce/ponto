<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    <title>Ponto do funcionário</title>
<?php
include 'partes/Head.php';
?>
		<!-- Favicon -->
		<link rel="shortcut icon" href="/imagens/PMSICO.png">
	</head>
	
	<body onload="javascript:titulo( '<h4>Administração de usuários da Biometria</h4>' );">
<?php
include 'partes/MenuPri.php';
include 'partes/Cabec.php';
include 'partes/pageheader.php';
include 'partes/pagebody.php';
?>
		<div class='row input-append date linha' style='margin-bottom: 10px; margin-left:4px'>
			Período de<input type="text" size="10" id="dtini" 
											 style="margin-left: 20px; margin-right: 10px; "/>
			a <input type="text" size="10" id="dtfim" 
							 style="margin-left: 10px; margin-right: 10px; "/>
			Saldo <input type="text" size="10" id="sldant" readonly
									 style="margin-left: 10px; margin-right: 10px; "/>
			<input type="text" size="10" id="dtfecha" readonly
											 style="margin-left: 5px; margin-right: 20px; float: right;"/>
			<label for="dtfecha" style="float: right;">Fechado em</label>
			<input type="text" size="20" id="reghoje" 
														 style="margin-left: 5px; margin-right: 20px; float: right;"/>
			<label for="dtfecha" style="float: right;">Registros do dia</label>
		</div>
										<table class="table table-striped table-hover table-bordered" id="eddt">
											<thead><tr role="row"></tr></thead>
											<tfoot><tr role="row"></tr></tfoot>
											<tbody></tbody>
										</table>
									</div> <!-- class="widget-body" -->
								</div> <!-- class="col-xs-12 col-md-12" -->
							</div> <!-- class="row" -->
						</div> <!-- class="widget-body" -->
					</div> <!-- class="page-body" id="conte" -->
				</div> <!-- /Conteudo -->
			</div> <!-- /Page Container -->
		</div> <!-- Main Container -->
		
		<!-- modal de retificação de horários: adiciona e exclui horários do dia -->
		<div id="modpresen" class="modal fade bs-modal-sm" role="dialog"
				aria-labelledby="mySmallModalLabel" aria-hidden="true"
				style="width: 500px; max-width: 500px;" >
			<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
										&times;
						</button>
						<h4 class="modal-title">Horários do dia</h4>
					</div>
					<div class="modal-body" id="bdymodal" style="width: 80%;">
						<div class='row linha' style='margin-top: 10px; margin-left:30px;'>
							<h5>
								<a href='javascript:addHor();' style='margin-left: 10px'
									title="adicionar um novo horário"
									class='btn btn-palegreen btn-circle btn-xs'>
									<i class='typcn typcn-plus-outline'></i>
								</a>
							</h5>
							<table id="tbhorarios" cellspacing="50" border="2">
							</table>
						</div>
					</div>
					<div class="modal-footer">
					<center>
						<button type="button" class="btn btn-primary"
										onclick="javascript:presenOK()"
										title="Persiste as eventuais modificações">
										OK
						</button>
						<button type="button" class="btn btn-default"
										onclick="javascript:presSairOK()"
										title="Encerra alterações sem salvar eventuais alterações">
										Cancelar
						</button>
					</center>
					</div>
				</div>
		</div>
	</div>
			
	<!-- modal de entrada de novo horário -->
	<div id="novapres" class="modal fade bs-modal-sm" role="dialog"
			aria-labelledby="mySmallModalLabel" aria-hidden="true"
			style="width: 500px; max-width: 500px;" >
		<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
									&times;
					</button>
					<h4 class="modal-title">Entrada de presença pelo funcionário</h4>
				</div>
				<div class="modal-body" id="bdymodal">
					<div class='row linha' style='margin-top: 10px; margin-left:30px;'>
						<input	style='width:90%;' class='input-small' autofocus
										id="novohor" title="introduza um horário a adicionar à data"/>
					</div>
				</div>
				<div class="modal-footer">
				<center>
					<button type="button" class="btn btn-primary"
									onclick="javascript:novapresOK()">
									OK
					</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				</center>
				</div>
			</div>
		</div>
	</div>
			
	<!-- modal de entrada de diálogo: adiciona uma ou mais mensagens de diálogo -->
	<div id="moddialogo" class="modal fade bs-modal-sm" role="dialog"
			aria-labelledby="mySmallModalLabel" aria-hidden="true"
			style="width: 500px; max-width: 500px;" >
		<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
									&times;
					</button>
					<h4 class="modal-title">Diálogo de justificativa</h4>
				</div>
				<div class="modal-body" id="bdymodal">
					<div class='row linha' style='margin-top: 10px; margin-left:30px;'>
						<table id="tbmensg" cellspacing="50" border="2">
						</table>
					</div>
					<div class='row linha' style='margin-top: 10px; margin-left:30px;'>
						<h5>Nova mensagem</h5>
						<input	style='width:90%;' class='input-small syss' 
										id="novamens" title="mensagem a adicionar ao diálogo"/>
						<a href='javascript:inclMensg();' class='btn btn-circle btn-primary btn-xs'>
							<i class='typcn typcn-plus-outline'></i></a>

					</div>
				</div>
				<div class="modal-footer">
				<center>
					<button type="button" class="btn btn-primary"
									onclick="javascript:dialogoOK()"
									title="Persiste as eventuais modificações">
									OK
					</button>
					<button type="button" class="btn btn-default"
									onclick="javascript:dlgSairOK()"
									title="Encerra alterações sem salvar eventuais alterações">
									Cancelar
					</button>
				</center>
				</div>
			</div>
		</div>
	</div>
		
<?php
include 'partes/Scripts.php';
?>

	<script type="text/javascript" src="partes/geral.js" ></script>
	<script type="text/javascript" src="partes/dteditavel.js" ></script>
	<script type="text/javascript" >
		function logout()
			{
			Deslogar();
			}

		function deleteRow( oTable, nRow )
			{
			if( DBDelete( oTable, nRow ) )
				deleteRowG( oTable, nRow );
			}
			
		function restoreRow( oTable, nRow )
			{
			restoreRowG( oTable, nRow );
			}
			
		function editRow( oTable, nRow )
			{
			editRowG( oTable, nRow );
			}
			
		function completaChild( original )
			{
			return " ";
			}

		function FormataChild( original )
			{
			}
			
		//	tratamento de presenças

		//	verifica se há uma quantidade adequada de presenças no dialogo
		function verHors()
			{
			var qtval	=	0;
			for( var ix=0; ix<hors.length; ix++ )
				{
				if( opes[ix] == "0" || opes[ix] == "1" )
					qtval++;
				}
			if( qtval%2 != 0 )
				{
				alert( "É necessário ter uma quantidade par de horários." );
				return false;
				}
			return true;
			}
			
		//	saiu do dialogo de presença pelo OK
		function presSairOK()
			{
			if( flmod != 0 )
				{
				var resp = confirm("há alterações não salvas. Quer mesmo sair sem salvá-las?");
				if( !resp )
					return;
				}
			$("#modpresen").modal("hide");
			}

		//	saiu do dialogo de nova presença pelo OK
		function presenOK()
			{
			if( !verHors() )
				return;
			var parms;
			for( var ix=0; ix<hors.length; ix++ )
				{
				if( salvos[ix] != "s" )
					{
					flmod = 1;
					
					switch( stts[ix] )
						{
						case "e":										//	existente
							if( opes[ix] == "0" )
								parms	=	"&fdteid=" + fids[ix] + "&operid=NULL" + 
												"&origid=" + orgs[ix]+ "&hora=" + dataatual + " " + hors[ix];
							else
								parms	=	"&fdteid=" + fids[ix] + "&operid=" + opes[ix] + 
												"&origid=" + orgs[ix]+ "&hora=" + dataatual + " " + hors[ix];
								
							if( !Update( "upfdte", parms ))
								{
								alert( "falha ao atualizar horarios" );
								montaPresTab();
								return;
								}
							break;
							
						case "n":										//	novo
							parms	=	"&fdtrid=" + fdtrid + "&operid=" + opes[ix] + 
											"&origid=" + orgs[ix]+ "&hora=" + dataatual + " " + hors[ix];
							if( !Insert( "infdte", parms ))
								{
								alert( "falha ao atualizar horarios" );
								montaPresTab();
								return;
								}
							break;

					case "d":										//	deletado
							if( fids[ix] != "" )
								{
								if( orgs[ix] == "1" )		//	biometria
									{
									opes[ix] = "2";
									parms	=	"&fdteid=" + fids[ix] + "&operid=2" + 
													"&origid=" + orgs[ix]+ "&hora=" + dataatual + " " + hors[ix];
									if( !Update( "upfdte", parms ) )
										{
										alert( "falha ao atualizar horarios" );
										montaPresTab();
										return;
										}
									}
								if( orgs[ix] == "2" )		//	funcionario
									{
									parms = "&fdteid=" + fids[ix];
									if( !Update( "delfdte", parms ) )
										{
										alert( "falha ao remover horario" );
										montaPresTab();
										return;
										}
									}
								}
							break;
						}
					
					salvos[ix] = "s";
					stts[ix] = "e";
					}
				}
			if( flmod != 0 )
				{
				parms = "&fdtrid=" + fdtrid + "&tsdt=4";
				if( !Update( "settsdt", parms ) )
					{
					alert( "falha ao seta o estado de 'EM ANALISE'." );
					montaPresTab();
					return;
					}
				acum	=	0;
				atuatab( false );
				}
			flmod = 0;
			$("#modpresen").modal("hide");
			}
		
		//	saiu do diálogo de digitação de nova presença pelo OK
		function novapresOK()
			{
			var hora = $("#novohor").val();
			if( hora.length != 5)
				{
				alert( "Horário inválido. Deve ser no formato hh:mm" );
				return;
				}
			if( hora.substring( 2, 3 ) != ":" )
				{
				alert( "Horário inválido. Deve ser no formato hh:mm" );
				return;
				}
			var hh = hora.substring( 0, 2 );
			var mm = hora.substring( 3, 5);
			if( Number(hh) < 0 || Number(hh) > 23 )
				{
				alert( "Horário inválido" );
				return;
				}
			if( Number(mm) < 0 || Number(mm) > 59 )
				{
				alert( "Horário inválido" );
				return;
				}
			
			for( var ix=0; ix<hors.length; ix++ )
				{
				if( hora == hors[ix] )
					{
					if( opes[ix] == "2" )
						{
						if( orgs[ix] == "1" )
							opes[ix] = "0";
						else
							opes[ix] = "1";
						stts[ix] = "n";
						salvos[ix] = "n";
						flmod	=	1;
						$("#novapres").modal("hide");
						montaPresTab();
						return;
						}
					else
						{
						alert( "Ja há este horário nesta data" );
						return;
						}
					}
				}
			fids.push("");
			hors.push(hora);
			opes.push("1");
			orgs.push("2");
			salvos.push("n");
			stts.push("n");
			flmod	=	1;
			$("#novapres").modal("hide");
			montaPresTab();
			}
			
		
		//	exclui uma presença
		function exclPres(ix)
			{
			opes[ix] = "2";
			salvos[ix] = "n";
			stts[ix] = "d";
			montaPresTab();
			}
		//	inclui uma nova presença
		function inclPres(ix)
			{
			if( orgs[ix] == "1" )								//	biometria
				{
				opes[ix] = "0";
				stts[ix] = "e";
				salvos[ix] = "n";
				}
			else
				{
				opes[ix] = "1";
				salvos[ix] = "n";
				if( fids[ix] == "" )
					stts[ix] = "n";
				else
					stts[ix] = "e";
				}
			montaPresTab();
			}
		//	monta a tabela de presenças a partir das tabelas
		function montaPresTab()
			{
			var vhtml =	"<thead><tr>" +
									"<th align='center'><b>  Horário  </b></th>" +
									"<th align='center'><b>  Orígem   </b></th>" +
									"<th align='center'><b>  Tipo     </b></th>" +
									"<th align='center'><b>  Ações    </b></th>" +
									"</tr></thead>";

			$("#idhorarios tbody tr").remove();
			var aux = "<tbody>";
			for( var ix=0; ix<hors.length; ix++ )
				{
				var acao = "";
				aux	+=	"<tr><th>" + hors[ix] + "</th><th>";
				switch( orgs[ix] )
					{
					case "1":
						aux += "biometria</th><th>";
						break;
					case "2":
						aux += "funcionário</th><th>";
						break;
					case "3":
						aux += "automática</th><th>";
						break;
					}
				switch( opes[ix] )
					{
					case "0":
						aux += "</th>";
						acao +=	"<a href='javascript:exclPres("+ix+");' class='btn btn-circle btn-warning btn-xs detalhes'>" +
										"<i class='typcn typcn-minus-outline'></i></a>";
						break;
					case "1":
						aux += "incluida</th>";
						acao +=	"<a href='javascript:exclPres("+ix+");' class='btn btn-circle btn-warning btn-xs detalhes'>" +
										"<i class='typcn typcn-minus-outline'></i></a>";
						break;
					case "2":
						aux += "excluida</th>";
						acao +=	"<a href='javascript:inclPres("+ix+");' class='btn btn-circle btn-primary btn-xs detalhes'>" +
										"<i class='typcn typcn-plus-outline'></i></a>";
						break;
					}
				aux += "<th>" + acao + "</th></tr>";
				}
			aux += "</tbody>";
			vhtml += aux;
			$("#tbhorarios").html(vhtml);
			}

		//	mostra o diálogo para alteração
		function dshow( idfdtr, idfdtm, dlg )
			{
			$("#novamens").val("");
			flmod = 0;
			fdtmid = idfdtm;
			fdtrid = idfdtr;
			var aux = "<tbody><tr></tr>";
			mensgs = [];
			if( dlg != "" )
				{
				mensgs = dlg.split(";");
				for( var ix=0; ix<mensgs.length; ix++ )
					{
					aux +=	"<tr><th>" + mensgs[ix] + "</th></tr>";
					}
				}
			aux += "</tbody>";
			$("#tbmensg").html(aux);
			$("#moddialogo").modal("show");
			}

		function dlgSairOK()
			{
			if( flmod != 0 )
				{
				var resp = confirm("há alterações não salvas. Quer mesmo sair sem salvá-las?");
				if( !resp )
					return;
				}
			$("#moddialogo").modal("hide");
			}

		//	adiciona uma mensagem ao grid de diálogo
		function inclMensg()
			{
			var mens = $("#novamens").val();
			if( mens.length < 1 )
				{
				dialogoOK();
				return;
				}
			flmod = 1;
			var agora = new Date();
			var msg = "F " + $.datepicker.formatDate("dd/mm ", agora ) +
								toHora(agora) + " - " + mens;
			var html =	"<tr><th> " + msg +
							"</th></tr>";
			$("#tbmensg tr:last").after(html);
			mensgs.push( msg );
			$("#novamens").val("");
			}
		//	salva as eventuais mensagens e fecha o diálogo
		function dialogoOK()
			{
			var mens = $("#novamens").val();
			if( mens.length > 0 )
				inclMensg();
			if( flmod )
				{
				var aux = "";
				for( var ix=0; ix<mensgs.length; ix++ )
					{
					if( mensgs[ix] == "" )
						continue;
					if( aux != "" )
						aux += ";";
					aux += mensgs[ix];
					}
				if( fdtmid == "" )
					{
					var parms = "&fdtrid=" + fdtrid + "&msg=" + aux;
					if( !Insert( "indial", parms ))
						{
						alert( "falha ao criar diálogo" );
						return;
						}
					}
				else
					{
					var parms = "&fdtmid=" + fdtmid + "&msg=" + aux;
					if( !Update( "updial", parms ))
						{
						alert( "falha ao atualizar diálogo" );
						return;
						}
					}
				parms = "&fdtrid=" + fdtrid + "&tsdt=4";
				if( !Update( "settsdt", parms ) )
					{
					alert( "falha ao seta o estado de 'EM ANALISE'." );
					montaPresTab();
					return;
					}
				atuatab(false);
				}
			$("#moddialogo").modal("hide");
			}
			
		function toHora( data )
			{
			var hh = data.getHours();
			if( hh < 10 )
				hh = "0" + hh;
			var mm = data.getMinutes();
			if( mm < 10 )
				mm = "0" + mm;
			return hh + ":" + mm;
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

		//	mostra os horários para alteração
		function hshow( data, idfdtr, horarios, operacoes, origens, fdteids )
			{
			flmod	=	0;
			fdtrid = idfdtr;
			dataatual = data;
			if( horarios == "" )
				{
				hors = [];
				opes = [];
				orgs = [];
				fids = [];
				stts = [];
				salvos = [];
				}
			else
				{
				hors = horarios.split( ";" );
				opes = operacoes.split( ";" );
				orgs = origens.split( ";" );
				fids = fdteids.split( ";" );
				stts = [];
				salvos = [];
				for( var ix=0; ix<hors.length; ix++ )
					{
					salvos[ix] = "s";
					stts[ix] = "e";
					}
				}
			montaPresTab();
			$("#modpresen").modal('show');
			}
			
		function addHor()
			{
			$("#novohor").val("");
			$("#novapres").modal("show");
			}

		//	tratamento inicial das datas e inicialização do datatables
		function setAjax( del )
			{
			//
			parms = "&funiid="+funiid+"&dtinic="+dtini;
			var resu = Select( "saldoant", parms );
			if( resu == null )
				throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
			//acum = Number( resu.dados[0].MINUTOS );
			var sldant = minToHHMM( resu.dados[0].MINUTOS );
			$("#sldant").val( sldant );

			//
			var dt = $("#dtini").datepicker("getDate");
			dtini = $.datepicker.formatDate("yymmdd", dt );
			dt = $("#dtfim").datepicker("getDate");
			dtfim = $.datepicker.formatDate("yymmdd", dt );
			
			var pessoa = sshd.substr( 1 );
			parms = "&pessoa="+pessoa+"&dtinic="+dtfim+"&dtterm="+dtfim;
			var resu = Select( "reprpmspessoa", parms );
			if( resu == null )
				throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
			if( resu.linhas > 0 )
				{
				var regs = "";
				for( var i=0; i<resu.linhas; i++ )
					{
					if( regs.length != 0 )
						regs += " ";
					var reg = resu.dados[i].PONTO;
					regs += reg.substr( 11 );
					}
				$("#reghoje").val( regs )
				}
			else
				$("#reghoje").val( "sem registros" )
			

			tableDestroy();
			AjaxSource	=	"partes/tableData.php?query=funaces&sshd=" + sshd.toUpperCase() + 
												"&dtini="+dtini+"&dtfim="+dtfim;
			inicializa.init();
			}
			
		$('#modpresen').on('keyup', function( ev )
			{
			if( ev.key == "Enter" )
				presenOK();
			});
			
		$('#novapres').on('shown.bs.modal', function () 
			{
			$("#novohor").focus();
			});
			
		$('#novapres').on('keyup', function( ev )
			{
			if( ev.key == "Enter" )
				novapresOK();
			});
			
		$('#moddialogo').on('shown.bs.modal', function () 
			{
			$("#novamens").focus();
			});
			
		$('#moddialogo').on('keyup', function( ev )
			{
			if( ev.key == "Enter" )
				inclMensg();
			});
			
		//	tratamento das datas 			
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
				setAjax(1);
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
				setAjax(1);
				});
	
	/////////////// PRINCIPAL ////////////////////////
	$('#eddt_new').hide();
	
	//	tabelas de definição da tabela de presenças
	var funiid = null;
	var flmod = 0;
	var dataatual;
	var fdtrid;
	var fdtmid;
	var hors = [];				//	horários
	var opes = [];				//	códigos de operação
	var orgs = [];				//	código de origens
	var fids = [];				//	id do fdte
	var stts = [];				//	status e-existente, n-novo, d-deletado
	var mensgs = [];			//	lista de mensagens
	var salvos = [];			//	s-salvo
	var acum = 0;

	//	obtem FUNI_ID
	var sshd = obterCookie( "user" );
	if( sshd == null )
		{
		Deslogar();
		}

	var parms = "&sshd=" + sshd;
	var resu = Select( "funiid", parms );
	if( resu == null )
		throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
	funiid = resu.dados[0].FUNI_ID;
	
	//	obtem data do fechamento
	parms = "&sshd=" + sshd;
	resu = Select( "dtfecha", parms );
	if( resu == null )
		throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
	if( resu.linhas < 1 )
		throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
	var dtfecha = toDate( resu.dados[0].DTFECHA );
	var dtproxf = new Date( dtfecha.getYear()+1900, dtfecha.getMonth(), dtfecha.getDate()+1 );
	$("#dtfecha").val( $.datepicker.formatDate("dd/mm/yy", dtfecha ) );

	//	acerta as datas
	var hoje = new Date();
	var dtfim = $.datepicker.formatDate("yymmdd", hoje );
	$("#dtfim").val( $.datepicker.formatDate("dd/mm/yy", hoje ) );
	hoje.setDate(1);
	$("#dtini").val( $.datepicker.formatDate("dd/mm/yy", dtproxf ) );
	var dtini = $.datepicker.formatDate("yymmdd", hoje );

		//	formatadores ligados ao datatables
		//		ticamp:		tipo de campo t/n/l = texto, numérico ou legenda
		//		inputs:		nomes dos campos no banco relativamente aos inputs
		//		origem:		índice da coluna do Datatables que atualiza o campo no banco
		//		editael:	indica se a coluna do datatable é editável
		var notabel			=	"";								//	nome da tabela base
		var	nocmpid			=	"";															//	nome do campo ID da tabela base
		var sequence		= "";
		//var liNova			=	[ '', '', '', acnova ];										//	template de linha nova
		var liNova			=
						{
						"DATA": "",
						"Registros": "",
						"Mensagens": "",
						"Totais": ""
						};
		//	monta o datatables
		var	order	=	[];											//	sem classificação 
		//	prepara a definiçao das colunas
		var colDefs	=	[];
		var	col	=	-1;
		var aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "4%",
			"aTargets": [ ++col ],
			"mData": "TSDT_ID",
			"sTitle":"Situação",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				var res = "";
				if( data == "" )
					{
					res = "<button type='button' title='normal' class='btn btn-default  btn-md'>OK</button>";
					}
				if( data == "1" )
					{
					res = "<button type='button' title='Pendente' class='btn btn-primary  btn-md'>PEN</button>";
					}
				if( data == "2" )
					{
					res = "<button type='button' title='Aceita' class='btn btn-success  btn-md'>ACE</button>";
					}
				if( data == "3" )
					{
					res = "<button type='button' title='Negada' class='btn btn-danger  btn-md'>NEG</button>";
					}
				if( data == "4" )
					{
					res = "<button type='button' title='Em análise' class='btn btn-warning  btn-md'>ANA</button>";
					}
				return res;
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "6%",
			"aTargets": [ ++col ],
			"mData": "DATA",
			"sTitle":"Data",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "15%",
			"aTargets": [ ++col ],
			"mData": "HORARIOS",
			"sTitle":"Registros",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				if( full.TSDT_ID == "" || full.TSDT_ID == "1" || full.TSDT_ID == "4" )
					var act	=	"<a href='javascript:hshow(\"" + full.DATA + "\",\"" + 
									full.FDTR_ID + "\",\"" +
									full.HORARIOS + "\",\"" + full.OPERACOES + "\",\"" + 
									full.ORIGENS +"\",\"" + full.FDTEIDS +"\");' " +
									"title=\"mostrar horarios\" >";
				else
					var act	=	"<a>";
				if( data == "" )
					{
					act += "<b>sem registros</b></a>";
					return act;
					}
				var lin1 = "";
				var lin2 = "";
				var orgs = full.ORIGENS.split(";");
				var opes = full.OPERACOES.split(";");
				var hors = full.HORARIOS.split(";");
				for( var i=0; i<hors.length; i++ )
					{
					var aux = "<font color='";
					if( orgs[i] == "1" )
						aux	+= "green";
					if( orgs[i] == "2" )
						aux	+= "red";
					if( orgs[i] == "3" )
						aux	+= "yellow";

					if( opes[i] == "2" )
						{
						if( lin2 != "" )
							aux += "'>-" + hors[i] + "</font>";
						else
							aux += "'>" + hors[i] + "</font>";
						lin2 += aux;
						}
					else
						{
						if( lin1 != "" )
							aux += "'>-" + hors[i] + "</font>";
						else
							aux += "'>" + hors[i] + "</font>";
						lin1 += aux;
						}
					}
				if( lin1 != "" && lin2 != "" )
					{
					act +=	"<b>Válidos:</b>" + lin1 + "<br>" + 
									"<b>Excluidos:</b>" + lin2;
					}
				if( lin1 != "" && lin2 == "" )
					{
					act += "<b>Válidos:</b>" + lin1;
					}
				if( lin1 == "" && lin2 != "" )
					{
					act += "<b>Excluidos:</b>" + lin2;
					}
				act += "</a>";

				return act;
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "13%",
			"aTargets": [ ++col ],
			"mData": "TIPOMENSAGEM",
			"sTitle":"Mensagens",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				if( data == "" )
					return data;
				var seps = data.split( ";" );
				var resu;
				if( seps.length > 0 )
					{
					resu = seps[0];
					for( var ix=1; ix<seps.length; ix++ )
						{
						resu += "<br>";
						resu += seps[ix];
						}
					}
				return resu;
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "27%",
			"aTargets": [ ++col ],
			"mData": "FDTM_DLMENS",
			"sTitle":"Diálogo",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				var seps = data.split( ";" );
				var resu;
				if( seps.length > 0 )
					{
					resu = seps[0];
					for( var ix=1; ix<seps.length; ix++ )
						{
						resu += "<br>";
						resu += seps[ix];
						}
					}
				if( full.TSDT_ID == "" || full.TSDT_ID == "1" || full.TSDT_ID == "4" )
					var act	=	"<a href='javascript:dshow(\"" + full.FDTR_ID + "\",\"" +  
										full.FDTM_ID + "\",\"" + data + "\");' " +
									"title=\"criar/complementar diálogo\" >";
				else
					var act	=	"<a>";
					
				if( data == "" )
					act += "sem conteúdo</a>";
				else
					act += resu + "</a>";
				return act;
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "25%",
			"aTargets": [ ++col ],
			"mData": "AUTORIZADAS",
			"sTitle":"",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				var resu = "";
				if( full.AUTORIZADAS != "" )
					{
					var seps = data.split(";");
					resu += seps[0];
					for( var ix=1; ix<seps.length; ix++ )
						{
						resu += "<br>" + seps[ix];
						}
					}
				if( full.CORRECAO != "" )
					{
					if( resu.length > 0 )
						resu += "<br>";
					resu += "Correção: " + full.CORRECAO;
					}
				return resu;
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "5%",
			"aTargets": [ ++col ],
			"mData": "TOTAL",
			"sTitle":"Dia",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "5%",
			"aTargets": [ ++col ],
			"mData": "SALDO",
			"sTitle":"Saldo",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				var calc;
				//acum	+=	Number(data) - 480;
				var hh	=	Math.floor(Math.abs(Number(data))/60);
				var mm	=	Math.abs(Number(data))%60;
				if( mm > 9 )
					calc = hh + ":" + mm;
				else
					calc = hh + ":0" + mm;
				if( Number(data) >= 0 )
					return "<font color=blue>"+calc+"</font>";
				else
					return "<font color=red>"+calc+"</font>";
				}
			};
		colDefs.push( aux );
		//	linhas da tabela a mostra por tela
		dlen		=	"22";
		lens    = [[5, 15, 22, 30, -1],
							[5, 15, 22, 30, "todos"]];
		
		///////////////////////////////////////////////////////////////////////

		setAjax( 0 );
				
		</script>
	</body>	
</html>
