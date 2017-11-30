<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    <title>Ponto do funcionário</title>
		<style>
			.table thead>tr>th {text-align: center;}
		</style>
<?php
include 'partes/Head.php';
?>
		<!-- Favicon -->
		<link rel="shortcut icon" href="/imagens/PMSICO.png">
	</head>
	
	<body onload="javascript:titulo( '<h4>Lista de todos todos os usuários do Autorizador</h4>' );">
<?php
include 'partes/MenuPri.php';
include 'partes/Cabec.php';
include 'partes/pageheader.php';
include 'partes/pagebody.php';
?>
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
			
		//	chama a página de pendências do funcionário
		function pendencias( sshd, id, nome, iduorfunc )
			{
			criarCookie( "sshdfunc", sshd );
			criarCookie( "idfunc", id );
			criarCookie( "nofunc", nome );
			criarCookie( "iduorfunc", iduorfunc );
			window.location = "autPenden.php";
			}
			
		function ausencias( sshd, id, nome, iduorfunc )
			{
			criarCookie( "sshdfunc", sshd );
			criarCookie( "idfunc", id );
			criarCookie( "nofunc", nome );
			criarCookie( "iduorfunc", iduorfunc );
			window.location = "autAusencias.php";
			}
			
		function correcoes( sshd, id, nome, iduorfunc )
			{
			criarCookie( "sshdfunc", sshd );
			criarCookie( "idfunc", id );
			criarCookie( "nofunc", nome );
			criarCookie( "iduorfunc", iduorfunc );
			window.location = "autCorrecao.php";
			}
			
		//	chama o detalhe de um funcionário escolhido
		function detfunc( id, nome, sshd )
			{
			criarCookie( "sshdfunc", sshd );
			criarCookie( "idfunc", id );
			criarCookie( "nofunc", nome );
			window.location = "autFuncio.php";
			}

		//	tratamento inicial das datas e inicialização do datatables
		function setAjax(  )
			{
			//
			tableDestroy();
			AjaxSource	=	"partes/tableData.php?query=funcindex&autsshd=" + sshd;
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
		var sshd = obterCookie( "user" );
		if( sshd == null )
			Deslogar();
		$("#eddt_new").hide();
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

		//	monta o datatables
		var	order	=	[];											//	sem classificação 
		//	prepara a definiçao das colunas
		var colDefs	=	[];
		var	col	=	-1;

		aux	=
			{
			"tipo": "x",
			"bVisible": false,
			"editavel": false,
			"vanovo": "",
			"width": "0%",
			"aTargets": [ ++col ],
			"mData": "IDFUNC",
			"sTitle":"id",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		var aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "15%",
			"aTargets": [ ++col ],
			"sTitle":"Ações",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				res = "";
				res +=	"<a href='javascript:pendencias(\"" + full.SSHDFUNC +
								"\", \""+ full.IDFUNC + "\", \"" + full.NOFUNC + 
								"\", \""+ full.IDUORFUNC + "\" );' " +
								"<button type='button' title='Verificar pendências do funcionário' " +
								"class='btn btn-warning  btn-md'>ANA</button></a>";
				
				res +=	"<a href='javascript:ausencias( \"" + full.SSHDFUNC +
								"\", \""+ full.IDFUNC + "\", \"" + full.NOFUNC + 
								"\", \""+ full.IDUORFUNC + "\" );' " +
								"<button type='button' title='Autorizar ausências' " +
								"class='btn btn-success  btn-md'>AUS</button></a>";

				res +=	"<a href='javascript:correcoes(\"" + full.SSHDFUNC+
								"\", \""+ full.IDFUNC + "\", \"" + full.NOFUNC + 
								"\", \""+ full.IDUORFUNC + "\" );' " +
								"<button type='button' title='Corrigir saldo para mais ou para menos' " +
								"class='btn btn-danger  btn-md'>COR</button></a>";

				return res;
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "UNIDADE",
			"sTitle":"Unidade",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "25%",
			"aTargets": [ ++col ],
			"mData": "NOFUNC",
			"sTitle":"Funcionário",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				var act	=	"<a href='javascript:detfunc( \"" + full.IDFUNC + 
								"\", \""+ data + "\", \"" + full.SSHDFUNC + 
								"\" )' title=\"detalhes do funcionário\" >" +
								data + "</a>";
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
			"mData": "DTUFECHAMENTO",
			"sTitle":"Fechamento",
			"defaultContent": " ",
			};
		colDefs.push( aux );
		
		aux	=
			{
			"className": "dt-center",
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "QTOK",
			"sTitle":"OK",
			"defaultContent": " ",
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "5%",
			"aTargets": [ ++col ],
			"mData": "QTPENDENTE",
			"sTitle":"Pendentes",
			"defaultContent": " ",
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "5%",
			"aTargets": [ ++col ],
			"mData": "QTACEITO",
			"sTitle":"Aceitas",
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
			"mData": "QTNEGADO",
			"sTitle":"Negadas",
			"defaultContent": " ",
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "5%",
			"aTargets": [ ++col ],
			"mData": "QTANALISE",
			"sTitle":"Análise",
			"defaultContent": " ",
			};
		colDefs.push( aux );
		
		///////////////////////////////////////////////////////////////////////

		setAjax();
				
		</script>
	</body>	
</html>
