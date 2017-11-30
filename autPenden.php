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
	</head>
	
	<body onload="javascript:titulo( '<h4>Análise de Pendências</h4>' );">
<?php
include 'partes/MenuPri.php';
include 'partes/Cabec.php';
include 'partes/pageheader.php';
include 'partes/pagebody.php';
?>
		<div class='row form-group' style='margin-bottom: 10px; margin-left:4px' data-toggle="buttons">
			<div class="col-lg-4">
				<span class="input-group-addon" style="height: 55px; ">
					Período de<input type="text" size="10" id="dtini" 
													 style="margin-left: 20px; margin-right: 20px; "/>
					a <input type="text" size="10" id="dtfim" 
									 style="margin-left: 20px; margin-right: 20px; "/>
				</span>
			</div>
			<div class="col-lg-6">
				<span class="input-group-addon" style="height: 55px; ">
					<div class="checkbox">
						<div id="divanali" class="checkbox-inline make-switch has-switch" >
							<input id="ckanali" type="checkbox" 
										 data-size="mini" data-label-text='Em Análise'
										 data-off-color="danger" data-on-color="success"
										 data-off-text="Não" data-on-text="Sim" >
						</div>
						<div id="divpenden" class="checkbox-inline make-switch has-switch" >
							<input id="ckpenden" type="checkbox" 
										 data-size="mini" data-label-text='Pendencias'
										 data-off-color="danger" data-on-color="success"
										 data-off-text="Não" data-on-text="Sim" >
						</div>
						<div id="divok" class="checkbox-inline make-switch has-switch" >
							<input id="ckok" type="checkbox" 
										 data-size="mini" data-label-text='OK'
										 data-off-color="danger" data-on-color="success"
										 data-off-text="Não" data-on-text="Sim" >
						</div>
						<div id="divaceita" class="checkbox-inline make-switch has-switch" >
							<input id="ckaceita" type="checkbox" 
										 data-size="mini" data-label-text='Aceitas'
										 data-off-color="danger" data-on-color="success"
										 data-off-text="Não" data-on-text="Sim" >
						</div>
						<div id="divnegada" class="checkbox-inline make-switch has-switch" >
							<input id="cknegada" type="checkbox" 
										 data-size="mini" data-label-text='Negadas'
										 data-off-color="danger" data-on-color="success"
										 data-off-text="Não" data-on-text="Sim" >
						</div>
					</div>
				</span>
			</div>
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
		
		<!-- diálogo modal de troca de mensagens e mudança de status -->
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
							<h5>
								<a href='javascript:aceitar();' style='margin-left: 10px'
									title="Aceitar a proposição do funcionário"
									class='btn btn-palegreen btn-circle btn-xs'>
									<i class='typcn typcn-thumbs-up'></i>
								</a>
								<a href='javascript:rejeitar();' style='margin-left: 10px'
									title="rejeitar a proposição do funcionário"
									class='btn btn-palegreen btn-circle btn-xs'>
									<i class='typcn typcn-thumbs-down'></i>
								</a>
								<input style='width:50%;' class='input-small syss' id="status" />
							</h5>
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

	<script type="text/javascript" src="bootstrap-3.3.1/dist/js/bootstrap-switch.js"></script>
	<script type="text/javascript" src="partes/geral.js" ></script>
	<script type="text/javascript" src="partes/dteditavel.js" ></script>
	<script type="text/javascript" >
		$("#ckpenden").on( 'switchChange.bootstrapSwitch', function( evn, state )
			{
			flpenden = state;
			setAjax();
			});
		
		$("#ckanali").on( 'switchChange.bootstrapSwitch', function( evn, state )
			{
			flanali = state;
			setAjax();
			});
		
		$("#ckok").on( 'switchChange.bootstrapSwitch', function( evn, state )
			{
			flok = state;
			setAjax();
			});
		
		$("#ckaceita").on( 'switchChange.bootstrapSwitch', function( evn, state )
			{
			flaceita = state;
			setAjax();
			});
		
		$("#cknegada").on( 'switchChange.bootstrapSwitch', function( evn, state )
			{
			flnegada = state;
			setAjax();
			});
		
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
			var cls = "sis" + original.FUNI_ID;

			//	obtem as médias do ponto do funcionário
			var url	=	"partes/queries.php?query=mediofecha&funiid=" + original.FUNI_ID;
			var	resfecha	=	remoto( url );
			if( resfecha.status != "OK" )
				{
				return "<p>Falha ao acessar as médias do funcionário. Por favor Tente mais tarde</p>";
				}
			if( resfecha.linhas < 1 )
				{
				return "<p>Falha ao acessar as médias do funcionário. Por favor Tente mais tarde</p>";
				}
				
			var aux = $.datepicker.formatDate("yymmdd", dtufech )
			url	=	"partes/queries.php?query=medioperio&funiid=" + original.FUNI_ID +
						"&dtini=" + aux;
			aux = $.datepicker.formatDate("yymmdd", hoje )
			url += "&dtfim=" + aux;
			var	resperio	=	remoto( url );
			if( resperio.status != "OK" )
				{
				return "<p>Falha ao acessar as médias do funcionário. Por favor Tente mais tarde</p>";
				}
			if( resperio.linhas < 1 )
				{
				return "<p>Falha ao acessar as médias do funcionário. Por favor Tente mais tarde</p>";
				}
				
			//	linha de médias fechadas
			var cmp;
			var wdcmp = "8%";
			var lin = IniLinha("form-group");
			cmp	=	
				{
				label: "Horários médios do último fechamento em",
				nocmp: "datafch",
				width: "20%",
				valor: resfecha.dados[0].DATA,
				divclass: "row col-xs-2",
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );

			lin += FimLinha();
			
			lin += IniLinha("form-group");
			cmp	=	
				{
				label: "Entrada",
				nocmp: "entra",
				width: wdcmp,
				valor: minToHHMM(resfecha.dados[0].MEDENTRA)+" ("+resfecha.dados[0].QTDENTRA+")" ,
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );
			
			cmp	=	
				{
				label: "Intervalo",
				nocmp: "inter",
				width: wdcmp,
				valor: minToHHMM(resfecha.dados[0].MEDINTER)+" ("+resfecha.dados[0].QTDINTER+")",
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );
			
			cmp	=	
				{
				label: "Volta",
				nocmp: "volta",
				width: wdcmp,
				valor: minToHHMM(resfecha.dados[0].MEDVOLTA)+" ("+resfecha.dados[0].QTDVOLTA+")",
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );
			
			cmp	=	
				{
				label: "Saida",
				nocmp: "saida",
				width: wdcmp,
				valor: minToHHMM(resfecha.dados[0].MEDSAIDA)+" ("+resfecha.dados[0].QTDSAIDA+")",
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );
			
			mins =  (resfecha.dados[0].MEDINTER-resfecha.dados[0].MEDENTRA)+
							(resfecha.dados[0].MEDSAIDA-resfecha.dados[0].MEDVOLTA);
			cmp	=	
				{
				label: "Total Diário",
				nocmp: "todia",
				width: wdcmp,
				valor: minToHHMM(mins),
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );

			mins = resfecha.dados[0].MEDVOLTA - resfecha.dados[0].MEDINTER;
			cmp	=	
				{
				label: "Duração intervalo",
				nocmp: "toint",
				width: wdcmp,
				valor: minToHHMM(mins),
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );
			
			lin	+=	FimLinha();
			
			lin += IniLinha("form-group");

			cmp	=	
				{
				label: "Horários médios do periodo de",
				nocmp: "dtini",
				width: "20%",
				valor: $.datepicker.formatDate("dd/mm/yy", dtufech ),
				divclass: "row col-xs-2",
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );

			cmp	=	
				{
				label: "até",
				nocmp: "dtfim",
				width: "20%",
				valor: $.datepicker.formatDate("dd/mm/yy", hoje ),
				divclass: "row col-xs-2",
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );

			lin += FimLinha();
			
			lin += IniLinha("form-group");

			cmp	=	
				{
				label: "Entrada",
				nocmp: "entra",
				width: wdcmp,
				valor: minToHHMM(resperio.dados[0].MEDENTRA)+" ("+resperio.dados[0].QTDENTRA+")" ,
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );
			
			cmp	=	
				{
				label: "Intervalo",
				nocmp: "inter",
				width: wdcmp,
				valor: minToHHMM(resperio.dados[0].MEDINTER)+" ("+resperio.dados[0].QTDINTER+")" ,
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );
			
			cmp	=	
				{
				label: "Volta",
				nocmp: "volta",
				width: wdcmp,
				valor: minToHHMM(resperio.dados[0].MEDVOLTA)+" ("+resperio.dados[0].QTDVOLTA+")" ,
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );
			
			cmp	=	
				{
				label: "Saida",
				nocmp: "saida",
				width: wdcmp,
				valor: minToHHMM(resperio.dados[0].MEDSAIDA)+" ("+resperio.dados[0].QTDSAIDA+")" ,
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );
			
			mins =  (resperio.dados[0].MEDINTER-resperio.dados[0].MEDENTRA)+
							(resperio.dados[0].MEDSAIDA-resperio.dados[0].MEDVOLTA);
			cmp	=	
				{
				label: "Total Diário",
				nocmp: "todia",
				width: wdcmp,
				valor: minToHHMM(mins),
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );

			mins = resperio.dados[0].MEDVOLTA - resperio.dados[0].MEDINTER;
			cmp	=	
				{
				label: "Duração intervalo",
				nocmp: "toint",
				width: wdcmp,
				valor: minToHHMM(mins),
				inpclass: "input-small",
				extra: "readonly"
				};
			lin	+=	CampoLabel( cmp );
			
			return lin;
			}
			
		//	mostra o diálogo para alteração
		function dshow( idfdtr, idfdtm, dlg )
			{
			$("#novamens").val("");
			flmod = 0;
			flstt = 0;
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
			status = 4;
			$("#status").val("Pendente")
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
			flmod = 0;
			flstt = 0;
			$("#moddialogo").modal("hide");
			}
			
		function aceitar()
			{
			status = 2;
			flstt = 1;
			$("#status").val("Aceita");
			}

		function rejeitar()
			{
			var flrej = true;
			var aux = "&fdtrid=" + fdtrid;
			var resu = Select( "qtdbiom", aux );
			if( resu == null )
				{
				alert( "Falha ao acessar dados. Por favor, tente mais tarde." );
				return;
				}
			if( resu.linhas < 1 )
				{
				aux = "Atenção! Se esta pendencia for rejeitada o funcionário perderá " +
							"8 horas neste dia.Esta operação não poderá ser desfeita e nenhuma " +
							"outra operação será possivel neste dia.";
				if( !confirm( aux ) )
					return;
				}
			else
				{
				var qtbio = 0;
				var qtfun = 0;
				for( var ix=0; ix<resu.linhas; ix++ )
					{
					if( resu.dados[ix].ORIGEM == "1" )
						qtbio = Number( resu.dados[ix].QTD );
					else
						qtfun = Number( resu.dados[ix].QTD );
					}
				if( qtbio == 0 )
					{
					aux = "Atenção! Se esta pendencia for rejeitada o funcionário perderá " +
								"8 horas neste dia.Esta operação não poderá ser desfeita e nenhuma " +
								"outra operação será possivel neste dia.";
					if( !confirm( aux ) )
						return;
					}
				else
					{
					if( qtbio%2 != 0 )
						{
						aux = "Atenção! Não há uma quantidade par de presenças registradas " + 
									"pela BIOMETRIA. Se esta pendencia for rejeitada o funcionário " +
									"perderá 8 horas neste dia.Esta operação não poderá ser " +
									"desfeita e nenhuma outra operação será possivel neste dia.";
						if( !confirm( aux ) )
							return;
						}
					}
				}
			aux = "Atenção! Esta opreração não poderá ser desfeita ou modificada.\n " + 
						"Todas os eventuais registros incluídos pelo funcionário derão removidos.\n" +
						"Prosseguir mesmo assim?";
			if( !confirm( aux ) )
				return;
			status = 3;
			flstt = 1;
			$("#status").val("Negada");
			}

		function dialogoOK()
			{
			var parms;
			if( flstt )
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
				//	insere ou altera o diálogo
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
				//	altera o status do dia
				if( status == 2 )								//	aceita
					{
					parms = "&fdtrid=" + fdtrid + "&tsdt=2";
					if( !Update( "settsdt", parms ) )
						{
						alert( "falha ao modificar para ACEITA. Por favor tente mais tarde" );
						return;
						}
					}
				if( status == 3 )								//	negada
					{
					parms = "&fdtrid=" + fdtrid + "&tsdt=3";
					if( !Update( "settsdt", parms ) )
						{
						alert( "falha ao modificar para ACEITA. Por favor tente mais tarde" );
						return;
						}
						
					var resul = remoto( "partes/Rejeitar.php?fdtrid=" + fdtrid );
					if( resul.status != "OK" )
						{
						alert( "Falha negando: " + resul.erro );
						return;
						}
					}
				if( status == 4 )								//	muda para pendente
					{
					parms = "&fdtrid=" + fdtrid + "&tsdt=1";
					if( !Update( "settsdt", parms ) )
						{
						alert( "falha ao modificar para PENDENTE. Por favor tente mais tarde" );
						return;
						}
					}
				flstt = 0;
				atuatab();
				}
			$("#moddialogo").modal("hide");
			}
			
		function inclMensg()
			{
			var mens = $("#novamens").val();
			if( mens.length < 1 )
				return;
			flmod = 1;
			flstt = 1;
			var agora = new Date();
			var msg = "A " + $.datepicker.formatDate("dd/mm ", agora ) +
								toHora(agora) + " - " + mens;
			var html =	"<tr><th> " + msg +
							"</th></tr>";
			$("#tbmensg tr:last").after(html);
			mensgs.push( msg );
			$("#novamens").val("");
			}

		//	funções genéricas
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

		//	tratamento inicial das datas e inicialização do datatables
		function setAjax(  )
			{
			//
			var dt = $("#dtini").datepicker("getDate");
			dtini = $.datepicker.formatDate("yymmdd", dt );
			dt = $("#dtfim").datepicker("getDate");
			dtfim = $.datepicker.formatDate("yymmdd", dt );
			if( fltable )
				{
				tableDestroy();
				fltable = false;
				}
			var stts = "";
			if( flanali )
				stts += "&ana"
			if( flpenden )
				stts += "&pen";
			if( flaceita )
				stts += "&ace";
			if( flnegada )
				stts += "&neg";
			if( flok )
				stts += "&ok";

			AjaxSource	=	"partes/tableData.php?query=pendencias&sshd="+sshd+
										"&dtini="+dtini+"&dtfim="+dtfim+stts;
			if( sshdfunc != null )
				AjaxSource += "&sshdfunc="+sshdfunc;
			inicializa.init();
			fltable = true;
			}
			
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
				setAjax();
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
				setAjax();
				});
	
		/////////////// PRINCIPAL ////////////////////////
		var flanali = true;
		var flpenden = false;
		var flok = false;
		var flaceita = false;
		var flnegada = false;
		var fltable = false;
		//	tabelas de definição da tabela de presenças
		var funiid = null;
		var flmod = 0;				//	indica se há alterações não salvas
		var flstt = 0;				//	indica se há alterações de status
		var status = 4;
		var dataatual;
		var fdtrid;
		var fdtmid;
		var stts = [];				//	status e-existente, n-novo, d-deletado
		var mensgs = [];			//	lista de mensagens
		var salvos = [];			//	s-salvo

		var sshd = obterCookie( "user" );
		if( sshd == null )
			Deslogar();
			
		var sshdfunc = obterCookie( "sshdfunc" );
		if( sshdfunc == null || sshdfunc == "" )
			window.location = "autTodosFunc.php";

		var nofunc = obterCookie( "nofunc" );
		if( nofunc == null || nofunc == "" )
			window.location = "autTodosFunc.php";

		matarCookie( "sshdfunc" );
		matarCookie( "nofunc" );
		matarCookie( "idfunc" );

		$("#titwidget").html( "Análise de pendencias de " + nofunc );
		
		//	acha a data do último fechamento e acerta as datas iniciais
		var parms = "&sshd=" + sshdfunc;
		var resu = Select( "dtfecha", parms );
		if( resu == null )
			throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
		var dtfecha = resu.dados[0].DTFECHA;
		var hoje = new Date();
		var dtfim = $.datepicker.formatDate("yymmdd", hoje );
		$("#dtfim").val( $.datepicker.formatDate("dd/mm/yy", hoje ) );
		var dtufech = toDate( dtfecha );
		$("#dtini").val( $.datepicker.formatDate("dd/mm/yy", dtufech ));
		var dtini = $.datepicker.formatDate("yymmdd", dtufech );

		//	obtem FUNI_ID do usuário (autorizador)
		var parms = "&sshd=" + sshd;
		var resu = Select( "funiid", parms );
		if( resu == null )
			throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
		funiid = resu.dados[0].FUNI_ID;

		//	formatadores ligados ao datatables
		var notabel			=	"";								//	nome da tabela base
		var	nocmpid			=	"";								//	nome do campo ID da tabela base
		var sequence		= "";
		var liNova			=
					{
					"DATA": "",
					"Registros": "",
					"Mensagens": "",
					"Totais": ""
					};
		var	order	=	[[0,'asc'],[3, 'asc']];			//	classificação 
		//	prepara a definiçao das colunas
		var colDefs	=	[];
		var	col	=	-1;
		var aux	=
			{
			"tipo": null,
			"editavel": false,
			"vanovo": "",
			"bSortable": false,
			"searchable": false,
			"aTargets": [ ++col ],
			"orderable":false,
			"mData":null,
			"width": "5%",
			"defaultContent": acshow
			};
		colDefs.push( aux );
		
		var aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "10%",
			"className": "centro",
			"aTargets": [ ++col ],
			"mData": "TSDT_ID",
			"sTitle":"Situação",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				let txt, cor;
				if( data == "" )
					{
					txt	=	"OK";
					cor = "WhiteSmoke ";
					}
				if( data == "1" )
					{
					txt	=	"PEN";
					cor = "LightBlue ";
					}
				if( data == "2" )
					{
					txt	=	"ACE";
					cor = "LightGreen";
					}
				if( data == "3" )
					{
					txt	=	"NEG";
					cor = "OrangeRed ";
					}
				if( data == "4" )
					{
					txt	=	"ANA";
					cor = "yellow";
					}
				return `
					<span align='center' style='vertical-align: middle; 
								display:inline-block; background-color: ${cor}; border: 1px; 
								border-style: solid; border-radius: 3px; padding:4px; '>
								${txt}</span> `;
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "7%",
			"aTargets": [ ++col ],
			"mData": "DATA",
			"sTitle":"Data",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		var aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "25%",
			"aTargets": [ ++col ],
			"mData": "NOME",
			"sTitle":"Funcionario",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "18%",
			"aTargets": [ ++col ],
			"mData": "HORARIOS",
			"sTitle":"Registros",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				if( data == "" )
					{
					return "<b>sem registros</b>";
					}
				var act = "";
				var lin1 = "";
				var lin2 = "";
				var orgs = full.BIOMET_FUNCION.split(";");
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
				return act;
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "15%",
			"aTargets": [ ++col ],
			"mData": "MENSPADRAO",
			"sTitle":"Ocorrências",
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
			"width": "35%",
			"aTargets": [ ++col ],
			"mData": "FDTM_DLMENS",
			"sTitle":"Diálogo/Aprovar/Rejeitar",
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
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "AUTORIZADAS",
			"sTitle":"Correções",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				var aux = "";
				if( full.AUTORIZADAS == "" )
					aux += "Aut: 0<br>";
				else
					aux += "Aut:" + full.AUTORIZADAS + "<br";
				if( full.CORRECAO == "" )
					aux += "Cor: 0";
				else
					aux += "Cor:" + full.CORRECAO;
				return aux;
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
			"mData": "MINUTOSDIA",
			"sTitle":"No dia",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				return minToHHMM( data )
				}
			};
		colDefs.push( aux );
		
		//	linhas da tabela a mostra por tela
		dlen		=	"22";
		lens    = [[5, 15, 22, 30, -1],
							[5, 15, 22, 30, "todos"]];
		
		///////////////////////////////////////////////////////////////////////

		$('#ckanali').bootstrapSwitch('state', true);
		
		$('#ckpenden').bootstrapSwitch('state', false);
		$('#ckok').bootstrapSwitch('state', false);
		$('#ckaceita').bootstrapSwitch('state', false);
		$('#cknegada').bootstrapSwitch('state', false);
		setAjax();
				
		</script>
	</body>	
</html>
