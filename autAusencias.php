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
						<label for="lib4b" class='lab sim' style='width:100%; '>Libera exigência de 4 batidas diárias: 
						<input style='width:20%; ' class='input-small inp' 
									 id="lib4b" title="Indica haverá liberação da obrigação de 4 batidas
																			nos dias em que esta ausência estiver em vigor"/>
						</label>
						<br/>
						<label for="qtaus" class='lab sim' style='width:100%; ' >Horas de ausência:
						<input style='width:20%; ' class='input-small inp' 
									 id="qtaus" title="quantidade de horas de ausência a autorizar no formato HH:MM"/>
						</label>
					</div>
					<div class="modal-footer">
					<center>
						<button type="button" class="btn btn-primary"
										onclick="javascript:autorizar()"
										title="Persiste as eventuais modificações">
										Autorizar
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
			
		function saveRow( oTable, nRow )
			{
			var idnovo	=	null;
			if( nNova == null )
				{
				if( !DBSetUpdt( oTable, nRow ) )
					return false;
				}
			else
				{
				idnovo	=	DBSetIsrt( oTable, nRow, sequence );
				if( idnovo < 1 )
					return false;
				}
			saveRowG( oTable, nRow, idnovo );
			return true;
			}
		function cancelEditRow( oTable, nRow )
			{
			cancelEditRowG( oTable, nRow );
			}
			
		function completaChild( original )
			{
			return " ";
			}

		function FormataChild( original )
			{
			}
		
		function limpaDados()
			{
			$("#dtini").val( $.datepicker.formatDate("dd/mm/yy", hoje ));
			$("#dtfim").val( $.datepicker.formatDate("dd/mm/yy", hoje ) );
			$('#tiaus').select2('val', '0');
			$('#lib4b').select2('val', '1');
			$('#qtaus').val('00:00');
			$("#modausen").modal('hide');
			}

		//	cria a nova ausência autorizada
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
			dtini = toStDate( ini, 2 );
			dtfim = toStDate( fim, 2 );
			//	insere a ausência autorizada
			url = "partes/ausAut.php?funiid=" + idfunc +
						"&autid=" + autorid +
						"&taauid=" + taauid +
						"&iduor=" + iduorfunc + 
						"&dtini=" + dtini +
						"&dtfim=" + dtfim +
						"&mins=" + qtaus;
			if( faauid >= 0 )
				url += "&faauid=" + faauid;
			if( idlib4b == 1 )
				url += '&lib4b';
				
			var resu = remoto( url );
			if( resu.status != "OK" )
				{
				var err = resu.erro;
				alert( "Falha <" + err + "> ao cadastrar ausencia autorizada." );
				return;
				}
			//	limpa e encerra
			limpaDados()
			setAjax();
			}
			
		function altera( ix )
			{
			let row = Table.fnGetData( ix );
			$('#dtini').datepicker( "option", "ninDate", dtproxf );
			$('#dtini').val(row.INICIO);
			$('#dtfim').val(row.TERMINO);
			let url =	"selectData.php?query=naosim";
			if( row.LIB4B == 0 )
				SelInit( "#lib4b", url, 0, "nao", escolib4b );
			else
				SelInit( "#lib4b", url, 1, "sim", escolib4b );
			
			SelInit( "#lib4b", url, 1, "sim", escolib4b );
			url =	"selectData.php?query=tiaus";
			SelInit( "#tiaus", url, row.IDTIPO, row.TIPO, escoTiaus );
			taauid = row.IDTIPO;
			escoTiaus( 'change', row.IDTIPO );
			
			$('#qtaus').val(minToHHMM(row.TMPDIARIO));
			
			faauid = row.FAAU_ID;
			$("#modausen").modal('show');
			return;
			}
			
		function remove( ix )
			{
			if( confirm( "Remover a ausência autorizada selecionada?" )  )
				{
				//	remove
				let row = Table.fnGetData( ix );
				let url = "partes/ausAutRem.php?faauid=" + row.FAAU_ID;
				var resu = remoto( url );
				if( resu.status != "OK" )
					{
					var err = resu.erro;
					alert( "Falha <" + err + "> ao remover ausencia autorizada." );
					return;
					}
				setAjax();
				}
			}

		//	tratamento inicial das datas e inicialização do datatables
		function setAjax(  )
			{
			//
			tableDestroy();
			ixtab = -1;
			AjaxSource	=	"partes/tableData.php?query=ausaut" + 
					"&sshd=" + sshdfunc +
					"&inicio=" + dtfrom;
			inicializa.init();
			}
			
		function escolib4b( tipo, id, text )
			{
			idlib4b = id;
			}
			
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
				document.getElementById('lib4b').setAttribute("readonly", true);
				}
			else
				{
				document.getElementById('qtaus').removeAttribute("readonly");
				document.getElementById('qtaus').value = "";
				document.getElementById('lib4b').removeAttribute("readonly");
				}
			//
			idlib4b = 1;
			taauid = id;
			$('#lib4b').val('sim').trigger('change');
			}

    $('#eddt_new').click(function( e )
			{
			e.stopImmediatePropagation();
			$("#dtini").val( $.datepicker.formatDate("dd/mm/yy", hoje ));
			$("#dtfim").val( $.datepicker.formatDate("dd/mm/yy", hoje ) );
			
			
			let url =	"selectData.php?query=naosim";
			SelInit( "#lib4b", url, 1, "sim", escolib4b );
			url =	"selectData.php?query=tiaus";
			SelInit( "#tiaus", url, 0, "Escolha abaixo", escoTiaus );
			taauid = 0;




			faauid = -1;
			$("#modausen").modal('show');
			} );
		/////////////// PRINCIPAL ////////////////////////
		var ixtab = -1;
		var hoje = new Date();
		var dtini = $.datepicker.formatDate("yymmdd", hoje );
		var dtfim = $.datepicker.formatDate("yymmdd", hoje );
		
		var idaus = -1;
		var noaus = "";
		var taauid = -1;				//	id do tipo de ausência autorizada
		var faauid = -1;				//	id de uma ausencia preexistente
		
		var idlib4b = 0;
		
		//	obtem dados do autorizador
		var sshd = obterCookie( "user" );
		if( sshd == null )
			Deslogar();

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

		var iduorfunc = obterCookie( "iduorfunc" );
		if( iduorfunc == null )
			{
			window.location = "autTodosFunc.php";
			}

		matarCookie( "sshdfunc" );
		matarCookie( "nofunc" );
		matarCookie( "idfunc" );
		matarCookie( "iduorfunc" );

		//	obtem FUNI_ID do autorizador
		var parms = "&sshd=" + sshd;
		var resu = Select( "funiid", parms );
		if( resu == null )
			throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
		var autorid = resu.dados[0].FUNI_ID;

			
		$("#titwidget").html( "Ausências autorizadas de " + nofunc );
		matarCookie( "sshdfunc" );

		//	acha a data do último fechamento e acerta as datas iniciais
		var parms = "&sshd=" + sshdfunc;
		var resu = Select( "dtfecha", parms );
		if( resu == null )
			throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
		var dtfecha = resu.dados[0].DTFECHA;
		var afecha = Number(dtfecha.substring( 0, 4 ));
		var mfecha = Number(dtfecha.substring( 5, 7 ))-1;
		var dfecha = Number(dtfecha.substring( 8 ));
		var dtufech = toDate( dtfecha );
		dtfecha = $.datepicker.formatDate("yymmdd", dtufech );
		var dtproxf = new Date( dtufech.getYear()+1900, dtufech.getMonth(), dtufech.getDate()+1 );
		$("#dtfrom").val( $.datepicker.formatDate("dd/mm/yy", dtproxf ));
		var dtfrom = $.datepicker.formatDate("yymmdd", dtufech );
		$("#dtfecha").val( $.datepicker.formatDate("dd/mm/yy", dtufech ) );

		//	tratamento das datas 	
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
				
		$( "#dtini" ).datepicker(						//	data inicial de ausencias
			{
			dateFormat: "dd/mm/yy",
			altFormat: "yymmdd",
			startView: 2,
			todayBtn: true,
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			minDate: dtufech,
			todayHighlight: true			
			}).on('change.dp', function(e)
				{ 
				var dt = $("#dtini").datepicker("getDate");
				dtini = $.datepicker.formatDate("yymmdd", dt );
				$("#dtfim").datepicker('setDate', $.datepicker.formatDate("dd/mm/yy", dt ))
				});
				
		$( "#dtfim" ).datepicker(						//	data final de ausencias
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

		//	combo de tipo de ausências
		url =	"selectData.php?query=tiaus";
		SelInit( "#tiaus", url, 0, "Escolha abaixo", escoTiaus );
		taauid = 0;
		
		//	combo de tipo de ausências
		url =	"selectData.php?query=naosim";
		SelInit( "#lib4b", url, 1, "sim", escolib4b );
		
		//	formatadores ligados ao datatables
		var liNova			=
						{
						"IDTIPO": "0",
						"TIPO": "Escolha abaixo",
						"PODE": "",
						"INICIO": "",
						"TERMINO": "",
						"TMPDIARIO": ""
						};

		//	monta o datatables
		var	order	=	[];											//	sem classificação 
		//	prepara a definiçao das colunas
		var colDefs	=	[];
		var	col	=	-1;

		var aux	=
			{
			"tipo": "t",
			"editavel": true,
			"vanovo": "",
			"width": "20%",
			"aTargets": [ ++col ],
			"mData": "TIPO",
			"sTitle":"Tipo",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"className": "centro",
			"tipo": "t",
			"editavel": true,
			"vanovo": "",
			"width": "10%",
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
			"tipo": "t",
			"editavel": true,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "LIB4B",
			"sTitle":"Libera 4 Batidas",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				if( full.PODE == '0' )
					return "sim";
				if( data == '0' )
					return "nao";
				else
					return "sim";
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"className": "centro",
			"tipo": "l",
			"editavel": true,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "INICIO",
			"sTitle":"Início",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"className": "centro",
			"tipo": "t",
			"editavel": true,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "TERMINO",
			"sTitle":"Término",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"className": "direito",
			"tipo": "t",
			"editavel": true,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "TMPDIARIO",
			"sTitle":"Tempo diário",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				return minToHHMM( data );
				}
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
				let stini = toDateInv( row.INICIO );
				let stter = toDateInv( row.TERMINO );
				
				ixtab++;
				if( dtfecha >= stter )
					return "";
				if( dtfecha >= stini )
					return	"<a href='#' onClick='javascript:altera(" + ixtab + ");' " +
									"class='btn btn-circle btn-info btn-xs' " +
									"title=\"Clique para alterar este período de autorização\" >" +
									"<i class='glyphicon glyphicon-edit'></i></a>"
				
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
		///////////////////////////////////////////////////////////////////////
		setAjax();
		</script>
	</body>	
</html>
