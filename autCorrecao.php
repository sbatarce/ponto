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
	
	<body onload="javascript:titulo( '<h4>Correção de Horas</h4>' );">
		
<?php
include 'partes/MenuPri.php';
include 'partes/Cabec.php';
include 'partes/pageheader.php';
include 'partes/pagebody.php';
?>
		<div class='row input-append date linha' style='margin-bottom: 10px; margin-left:4px'>
			Início<input type="text" size="10" id="dtfrom" 
									 style="margin-left: 20px; margin-right: 20px; "/>
			<input type="text" size="10" id="dtfecha" 
						 style="margin-left: 10px; margin-right: 20px; float: right;"/>
			<label style="float: right;">Fechamento em</label>
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
		
	<!-- modal de criação ou alteração de correção de horas -->
	<div id="modcorrec" class="modal fade bs-modal-sm" role="dialog"
			 aria-labelledby="mySmallModalLabel" aria-hidden="true"
			 style="width: 500px; max-width: 500px;" >
		<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
									&times;
					</button>
					<h4 class="modal-title">Correção de horas</h4>
				</div>
				<div class="modal-body" id="bdymodal" style="width: 80%;">
					<label for="dtcor" class='lab' style='width: 40%; '>Data da correção
					<input id="dtcor" class='dtcor input-small inp' 
								 style='width: 50%; ' readonly /></label>
					<br/>
					<label for="dbcr" class='lab' style='width:100%; '>Creditar ou debitar: 
					<input style='width:50%; ' class='input-small inp' 
								 id="dbcr" title="Escolha se crédito ou débito de horas"/></label>
					<br/>
					<br/>
					<label for="obs" class='lab' style='width:100%; '>Observações (opcional): 
						<textarea style='width:100%; height: 50px; ' class='input-small inp' 
											id="obs" title="Observações opcionais do Autorizador">
						</textarea></label>
					<br/>
					<label for="tmp" class='lab sim' style='width:100%; ' >Correção em horas:
					<input style='width:20%; ' class='input-small' 
								 id="tmp" title="quantidade de horas de a corrigir para mais ou para menos"/>
					</label>
				</div>
				<div class="modal-footer">
				<center>
					<button type="button" class="btn btn-primary"
									onclick="javascript:corrigir()"
									title="Persiste as eventuais modificações">
									Corrigir
					</button>
					<button type="button" class="btn btn-warning" data-dismiss="modal"
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
	
	function setAjax(  )
		{
		//
		tableDestroy();
		ixtab = -1;
		AjaxSource	=	"partes/tableData.php?query=correcs" + 
				"&funiid=" + idfunc +
				"&dtini=" + dtfrom +
				"&dtfim=" + dthoje;
		inicializa.init();
		}
		
		function escodbcr( tipo, id, text )
			{
			dbcr = id;
			}
			
		function calcTmp( tmp )
			{
			if( tmp.length < 4 || tmp.length > 6 )
				{
				alert( "O valor da correção deve ser dado em horas XXX:XX, XX:XX ou X:XX");
				return -1;
				}
			if( tmp.length == 4 && tmp.substr( 1,1 ) != ':' )
				{
				alert( "O valor da correção deve ser dado em horas XXX:XX, XX:XX ou X:XX");
				return -1;
				}
			if( tmp.length == 5 && tmp.substr( 2,1 ) != ':' )
				{
				alert( "O valor da correção deve ser dado em horas XXX:XX, XX:XX ou X:XX");
				return -1;
				}
			if( tmp.length == 6 && tmp.substr( 3,1 ) != ':' )
				{
				alert( "O valor da correção deve ser dado em horas XXX:XX, XX:XX ou X:XX");
				return -1;
				}
			let hh, mm;
			if( tmp.length == 4 )
				{
				hh = Number( tmp.substr( 0, 1 ) );
				mm = Number( tmp.substr( 2 ) );
				}
			if( tmp.length == 5 )
				{
				hh = Number( tmp.substr( 0, 2 ) );
				mm = Number( tmp.substr( 3 ) );
				}
			if( tmp.length == 6 )
				{
				hh = Number( tmp.substr( 0, 3 ) );
				mm = Number( tmp.substr( 4 ) );
				}
			if( mm > 59 )
				{
				alert( "A porção de minutos não pode exceder 59");
				return -1;
				}
			return hh*60+mm;
			}
			
		function altera( ix )
			{
			let row = Table.fnGetData( ix );
			fucoid = row.FUCO_ID;
			funcao = "A";		

			let dt = toDate( row.DATA );
			$("#dtcor").val( $.datepicker.formatDate("dd/mm/yy", dt ));
			dtcor = $.datepicker.formatDate("yymmdd", dt );
			
			let url =	"selectData.php?query=dbcr";
			SelInit( "#dbcr", url, 0, "Escolha Abaixo:", escodbcr );
			dbcr = 0;
			$('#obs').val(row.OBS);
			$('#tmp').val(minToHHMM(row.TMP));
		
			$("#modcorrec").modal('show');
			return;
			}
			
		function remove( ix )
			{
			let row = Table.fnGetData( ix );
			fucoid = row.FUCO_ID;
			
			let parms = "&fucoid="+fucoid;
			if( !Delete( "delfuco", parms ))
				{
				alert( "falha ao remover correção" );
				return;
				}
			setAjax();
			return;
			}
			
		function corrigir()
			{
			if( dbcr != 1 && dbcr != 2 )
				{
				alert( "Por favor escolha se as horas serão debitadas ou creditadas.");
				return;
				}
			let mins = calcTmp( $('#tmp').val() );
			if( mins < 0 )
				return;
			let obs = $('#obs').val();

			if( funcao == "I" )
				{
				let parms = "&funiid="+idfunc+"&fuauid="+autorid+
										"&dtref="+dtcor;
				if( dbcr == 1 )
					parms += "&dbcr=DB";
				else
					parms += "&dbcr=CR";
				parms += "&mins="+mins;
				
				if( obs.length > 0 )
					parms += "&obs="+obs;

				if( !Insert( "infuco", parms ))
					{
					alert( "falha ao Inserir nova correção" );
					return;
					}
				}
				
			if( funcao == "A" )
				{
				let parms = "&fucoid="+fucoid+"&fuauid="+autorid+
										"&dtref="+dtcor;
				if( dbcr == 1 )
					parms += "&dbcr=DB";
				else
					parms += "&dbcr=CR";
				parms += "&mins="+mins;
				
				if( obs.length > 0 )
					parms += "&obs="+obs;

				if( !Update( "updfuco", parms ))
					{
					alert( "falha ao atualizar correção" );
					return;
					}
				}

			$("#modcorrec").modal('hide');
			setAjax();
			return;
			}
		
    $('#eddt_new').click(function( e )
			{
			e.stopImmediatePropagation();
			$("#dtcor").val( $.datepicker.formatDate("dd/mm/yy", hoje ));
			dtcor = $.datepicker.formatDate("yymmdd", hoje );
			
			let url =	"selectData.php?query=dbcr";
			SelInit( "#dbcr", url, 0, "Escolha Abaixo:", escodbcr );
			dbcr = 0;
			$('#obs').val('');
			$('#tmp').val('');
			fucoid = -1;
			funcao = "I";
			$("#modcorrec").modal('show');
			} );

	/////////////// PRINCIPAL ////////////////////////
	//	tabelas de definição da tabela de presenças
	var dtcor = null;
	var ixtab = 0;
	var dbcr = "";
	var fucoid = -1;
	var funcao = "-";
	
		//	obtem dados do autorizador
		var sshd = obterCookie( "user" );
		if( sshd == null )
			Deslogar();

		var parms = "&sshd=" + sshd;
		var resu = Select( "funiid", parms );
		if( resu == null )
			throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
		var autorid = resu.dados[0].FUNI_ID;

		//	obtem dados do funcionário escolhido
		var idfunc = obterCookie( "idfunc" );
		if( idfunc == null || idfunc == "" )
			{
			window.location = "autTodosFunc.php";
			}

		var nofunc = obterCookie( "nofunc" );
		if( nofunc == null || nofunc == "" )
			{
			window.location = "autTodosFunc.php";
			}

		var sshdfunc = obterCookie( "sshdfunc" );
		if( sshdfunc == null || sshdfunc == "" )
			{
			window.location = "autTodosFunc.php";
			}
			
		$("#titwidget").html( "Correções de horas de " + nofunc );

		matarCookie( "sshdfunc" );
		matarCookie( "nofunc" );
		matarCookie( "idfunc" );
		matarCookie( "iduorfunc" );

		$( "#dtfrom" ).datepicker(					//	data inicial de pesquisa
			{
			dateFormat: "dd/mm/yy",
			altFormat: "yymmdd",
			startView: 2,
			todayBtn: true,
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			//minDate: dtufech,
			todayHighlight: true			
			}).on('change.dp', function(e)
				{ 
				var dt = $("#dtfrom").datepicker("getDate");
				dtfrom = $.datepicker.formatDate("yymmdd", dt );
				setAjax();
				});

		//	acha a data do último fechamento e acerta as datas iniciais
		var parms = "&sshd=" + sshdfunc;
		var resu = Select( "dtfecha", parms );
		if( resu == null )
			throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
		var dtfecha = resu.dados[0].DTFECHA;
		var dtufech = toDate( dtfecha );
		dtfecha = $.datepicker.formatDate("yymmdd", dtufech );
		$("#dtfrom").val( $.datepicker.formatDate("dd/mm/yy", dtufech ));
		var dtfrom = $.datepicker.formatDate("yymmdd", dtufech );
		$("#dtfecha").val( $.datepicker.formatDate("dd/mm/yy", dtufech ) );
		var dtnext = new Date( dtufech.getYear()+1900, dtufech.getMonth(), dtufech.getDate()+1 );
		$("#dtcor").datepicker( "option", "minDate", dtnext );

		var hoje = new Date();
		var dthoje = toStDate( hoje, 2 );

		//	formatadores ligados ao datatables
		var notabel			=	"BIOMETRIA.FUCO_FUNCCORRECAOHORAS";			//	nome da tabela base
		var	nocmpid			=	"FUCO_ID";															//	nome do campo ID da tabela base
		var sequence		= "";
		var liNova			=
						{
						"DATA": "",
						"OBS": "",
						"DBCR": "",
						"TMP": ""
						};

		//	monta o datatables
		var	order	=	[];											//	sem classificação 
		//	colunas
		var colDefs	=	[];
		var	col	=	-1;
		
		

		aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "DATA",
			"sTitle":"Data",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "50%",
			"aTargets": [ ++col ],
			"mData": "OBS",
			"sTitle":"Observações",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "DBCR",
			"sTitle":"DB/CR",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": true,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "TMP",
			"sTitle":"Horas",
			"defaultContent": " "
			};
		colDefs.push( aux );

		aux	=	
			{
			"tipo": null,
			"editavel": false,
			"vanovo": "",
			"sTitle":"",
			"bSortable": false,
			"searchable": false,
			"aTargets": [ ++col ],
			"orderable":false,
			"mData": "action",
			"width": "10%",
			"render": function( data, type, row )
				{
				let dt = toDateInv( row.DATA );
				ixtab++;

				if( dtfecha >= dt )
					return "";
				
				return	"<a href='#' onClick='javascript:altera(" + ixtab + ");' " +
								"class='btn btn-circle btn-info btn-xs' " +
								"title=\"Clique para alterar este período de autorização\" >" +
								"<i class='glyphicon glyphicon-edit'></i></a>" +
								"<a href='#' onClick='javascript:remove(" + ixtab + ");' " +
								"class='btn btn-circle btn-info btn-xs' " +
								"title=\"Clique para remover este período de autorização\" >" +
								"<i class='glyphicon glyphicon-remove'></i></a>"
				}
			};
		colDefs.push( aux );

		setAjax();
				
		</script>
	</body>	
</html>
