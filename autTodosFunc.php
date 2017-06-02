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
	
	<body onload="javascript:titulo( '<h4>Lista de todos os usuários do Autorizador</h4>' );">
<?php
include 'partes/MenuPri.php';
include 'partes/Cabec.php';
include 'partes/pageheader.php';
include 'partes/pagebody.php';
?>
		<div class='row input-append date linha' style='margin-bottom: 10px; margin-left:4px'>
			Período de<input type="text" size="10" id="dtini" 
											 style="margin-left: 20px; margin-right: 20px; "/>
			a <input type="text" size="10" id="dtfim" 
							 style="margin-left: 20px; margin-right: 20px; "/>
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
		function setAjax( del )
			{
			//
			var dt = $("#dtini").datepicker("getDate");
			dtini = $.datepicker.formatDate("yymmdd", dt );
			dt = $("#dtfim").datepicker("getDate");
			dtfim = $.datepicker.formatDate("yymmdd", dt );
			if( del != 0 )
				tableDestroy();
			AjaxSource	=	"partes/tableData.php?query=funaces&dtini="+dtini+"&dtfim="+dtfim;
			inicializa.init();
			}
			
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
		//	tabelas de definição da tabela de presenças
		var dataatual;
		var fdtrid;
		var fdtmid;
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
		var sshd = obterCookie( "user" );
		if( sshd == null )
			{
			Deslogar();
			}

		//	acerta as datas
		var hoje = new Date();
		var dtfim = $.datepicker.formatDate("yymmdd", hoje );
		$("#dtfim").val( $.datepicker.formatDate("dd/mm/yy", hoje ) );
		hoje.setDate(1);
		$("#dtini").val( $.datepicker.formatDate("dd/mm/yy", hoje ));
		var dtini = $.datepicker.formatDate("yymmdd", hoje );

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
			"width": "8%",
			"aTargets": [ ++col ],
			"mData": "TSDT_ID",
			"sTitle":"Situação",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				var res = "";
				if( data == "" )
					{
					res = "<button type='button' class='btn btn-default  btn-md'>OK</button>";
					}
				if( data == "1" )
					{
					res = "<button type='button' class='btn btn-primary  btn-md'>Penden</button>";
					}
				if( data == "2" )
					{
					res = "<button type='button' class='btn btn-success  btn-md'>Aceita</button>";
					}
				if( data == "3" )
					{
					res = "<button type='button' class='btn btn-danger  btn-md'>Negada</button>";
					}
				if( data == "4" )
					{
					res = "<button type='button' class='btn btn-warning  btn-md'>Análise</button>";
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
			"width": "7%",
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
			"width": "17%",
			"aTargets": [ ++col ],
			"mData": "NOME",
			"sTitle":"Funcionário",
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
			"width": "10%",
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
			"width": "25%",
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
			"width": "8%",
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
			"mData": "TOTAL",
			"sTitle":"No dia",
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
