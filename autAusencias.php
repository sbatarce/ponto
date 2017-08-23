<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    <title>Ponto do funcionário</title>
		<style>
		.table thead>tr>th {text-align: center;}
		.direito {text-align: right;}
		.centro  {text-align: center;}
		.esquerdo {text-align: left;}
		</style>
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
			Início<input type="text" size="10" id="dtini" 
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
		
		<!-- modal de retificação de horários: adiciona e exclui horários do dia -->
		<div id="modausen" class="modal fade bs-modal-sm" role="dialog"
				aria-labelledby="mySmallModalLabel" aria-hidden="true"
				style="width: 500px; max-width: 500px;" >
			<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
										&times;
						</button>
						<h4 class="modal-title">Autorização de ausências</h4>
					</div>
					<div class="modal-body" id="bdymodal" style="width: 80%;">
						<h5>Nova mensagem</h5>
						<div class='row linha' style='margin-top: 10px; margin-left:30px;'>
							<input	style='width:90%;' class='input-small' autofocus
											id="novohor" title="introduza um horário a adicionar à data"/>
						</div>
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
										onclick="javascript:ausenOK()"
										title="Persiste as eventuais modificações">
										OK
						</button>
						<button type="button" class="btn btn-default"
										onclick="javascript:ausenSairOK()"
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
			
		//	chama a página de pendências do funcionário
		function pendencias( sshd )
			{
			criarCookie( "sshdfunc", sshd );
			window.location = "autPenden.php";
			}
			
		//	obtem o detalhe de um funcionário escolhido no autTodosFunc
		function detfunc( id, nome, sshd )
			{
			criarCookie( "idfunc", id );
			criarCookie( "nofunc", nome );
			criarCookie( "sshdfunc", sshd );
			window.location = "autFuncio.php";
			}

		//	tratamento inicial das datas e inicialização do datatables
		function setAjax( del )
			{
			//
			if( del != 0 )
				tableDestroy();
			var dt = $("#dtini").datepicker("getDate");
			dtini = $.datepicker.formatDate("yymmdd", dt );
			AjaxSource	=	"partes/tableData.php?query=ausaut" + 
					"&sshd=" + sshdfunc +
					"&inicio=" + dtini;
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
		/////////////// PRINCIPAL ////////////////////////
//		$('#eddt_new').hide();
    $('#eddt_new').click(function( e )
					{
					e.stopImmediatePropagation();
					}
				);
		var dtini = null;
		var sshd = obterCookie( "user" );
		if( sshd == null )
			{
			Deslogar();
			}
		if( sshd == null )
			window.location = "index.php";
		var idfunc = obterCookie( "idfunc" );
		if( idfunc == null )
			{
			console.log("passou 1")
			window.history.back();
			window.location = "index.php";
			}

		var nofunc = obterCookie( "nofunc" );
		if( nofunc == null )
			{
			console.log("passou 2")
			window.history.back();
			window.location = "index.php";
			}

		var sshdfunc = obterCookie( "sshdfunc" );
		if( sshdfunc == null )
			{
			console.log("passou 3")
			window.history.back();
			window.location = "index.php";
			}
		console.log("passou 4")

		//	acha a data do último fechamento e acerta as datas iniciais
		var parms = "&sshd=" + sshd;
		var resu = Select( "dtfecha", parms );
		if( resu == null )
			throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
		var dtfecha = resu.dados[0].DTFECHA;
		var afecha = Number(dtfecha.substring( 0, 4 ));
		var mfecha = Number(dtfecha.substring( 5, 7 ))-1;
		var dfecha = Number(dtfecha.substring( 8 ));
		var dtufech = new Date( afecha, mfecha, dfecha, 0, 0, 0 );
		$("#dtini").val( $.datepicker.formatDate("dd/mm/yy", dtufech ));
		var dtini = $.datepicker.formatDate("yymmdd", dtufech );
		//	formatadores ligados ao datatables
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
			"width": "30%",
			"aTargets": [ ++col ],
			"mData": "TIPO",
			"sTitle":"Tipo",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"className": "centro",
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "15%",
			"aTargets": [ ++col ],
			"mData": "PODE",
			"sTitle":"Marcação",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				if( data == '0' )
					return "Proibida";
				else
					return "Permitida";
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"className": "centro",
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "20%",
			"aTargets": [ ++col ],
			"mData": "INICIO",
			"sTitle":"Início",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"className": "centro",
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "20%",
			"aTargets": [ ++col ],
			"mData": "TERMINO",
			"sTitle":"Término",
			"defaultContent": " ",
			};
		colDefs.push( aux );
		
		aux	=
			{
			"className": "direito",
			"tipo": "x",
			"vanovo": "",
			"width": "20%",
			"aTargets": [ ++col ],
			"mData": "TMPDIARIO",
			"sTitle":"Tempo diário",
			"defaultContent": " ",
			};
		colDefs.push( aux );
		///////////////////////////////////////////////////////////////////////

		setAjax( 0 );
				
		</script>
	</body>	
</html>
