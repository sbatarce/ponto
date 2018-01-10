<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    <title>Ponto do funcionário</title>
		<?php
		include 'partes/Head.php';
		?>
	</head>
	
	<body onload="javascript:titulo( '<h4>Atribuição de autorizadores</h4>' );">
<?php
include 'partes/MenuPri.php';
include 'partes/Cabec.php';
include 'partes/pageheader.php';
include 'partes/pagebody.php';
?>
		<div class='row form-group' 
				 style='margin-bottom: 5px; ' data-toggle="buttons">
			<div class="col-lg-6" style='width:100%;'>
			UOR
			<input type="text" id="fuors" class="fuors"
								style="width: 25%; margin-right: 30px; " />
			<input type="text" id="dtuprc" class="dtuprc"
					 style="margin-left: 10px; width: 15%;  float: right;
					 font-size: 20px; font-weight: bold; " readonly/>
			<label style="float: right; font-weight: bold; ">última coleta completada em</label>
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

		<!-- modal de criação de período de autorização -->
		<div id="modcriaautor" class="modal fade bs-modal-sm" role="dialog"
				aria-labelledby="mySmallModalLabel" aria-hidden="true"
				style="width: 500px; max-width: 500px;" >
			<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
										&times;
						</button>
						<h4 class="modal-title">Período de autorização</h4>
					</div>
					<div class="modal-body" id="bdymodal" style="width: 100%;">
						UOR do Ponto: 
						<input style='width:40%; ' class='input-small uorpto' readonly="true"
									 id="uorpto" title="UOR que o autorizador irá controlar"/></label>
						<br>
						Autorizador: 
						<input style='width:100%; ' class='input-small autors' 
									 id="criaautors" title="Escolha o autorizador"/>
						<br>						
						<label for="dtinicria" class='lab' style='width: 40%; '>Período de
						<input id="dtinicria" class='input-small inp dtinicria' 
									 style='width: 50%; ' readonly /></label>
						<label for="dtfimcria" class='lab' style='width: 40%; ' >até 
						<input id="dtfimcria" class='input-small inp dtfimcria' 
									 style='width: 50%; ' readonly /></label>
					</div>
					<div class="modal-footer">
					<center>
						<button type="button" class="btn btn-primary"
										onclick="javascript:autorizar()"
										title="Salva o novo período de autorização">
										Autorizar
						</button>
						<button type="button" class="btn btn-warning" data-dismiss="modal"
										title="Encerra alterações sem salvar">
										Cancelar
						</button>
					</center>
					</div>
				</div>
		</div>
	</div>

		<!-- modal de alteração de período de autorização -->
		<div id="modaltautor" class="modal fade bs-modal-sm" role="dialog"
				aria-labelledby="mySmallModalLabel" aria-hidden="true"
				style="width: 500px; max-width: 500px;" >
			<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
										&times;
						</button>
						<h4 class="modal-title">Período de autorização</h4>
					</div>
					<div class="modal-body" id="bdymodal" style="width: 100%;">
						UOR do Ponto: 
						<input style='width:40%; ' class='input-small uorpto' readonly="true"
									 id="uorpto" title="UOR que o autorizador irá controlar"/></label>
						<br>
						Autorizador: 
						<input style='width:100%; ' class='input-small altautors' readonly="true"
									 id="altautors" title="autorizador"/>
						<br>						
						<label for="dtinialt" class='lab' style='width: 40%; '>Período de
						<input id="dtinialt" class='input-small inp dtinialt' 
									 style='width: 50%; ' readonly /></label>
						<label for="dtfimalt" class='lab' style='width: 40%; ' >até 
						<input id="dtfimalt" class='input-small inp dtfimalt' 
									 style='width: 50%; ' readonly /></label>
					</div>
					<div class="modal-footer">
					<center>
						<button type="button" class="btn btn-primary"
										onclick="javascript:alterar()"
										title="Persiste as eventuais modificações">
										Alterar
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
			
		function tabVazia()
			{
			var data = [];
			data.push( 
				{
				"FUAUID": -1,
				"SSHD": "",
				"NOME": "Escolha uma UOR acima",
				"INICIO": "",
				"TERMINO": "",
				"action": ""
				} );
			Table.fnAddData( data, true );
			}
			
		//	verifica jurisdição de autorizador na UOR
		function jurisdUOR( sshdauto, uorauto )
			{
			if( tiuser > 3 )
				return true;
			var url	=	"partes/queries.php?query=fuauid" + 
								"&sshd=" + sshdauto + 
								"&uorid=" + uorauto;
			var	resu	=	remoto( url );
			if( resu.status != "OK" )
				{
				alert( "Erro obtendo dados do autorizador: " + resu.erro );
				return false;
				}
			if( resu.linhas < 1 )
				return false;
			if( resu.dados[0].FIM != "" )
				return false;
			return true;
			}
		
		//	tratamento inicial das datas e inicialização do datatables
		function setAjax(  )
			{
			if( tiuser < 4 )
				{
				if( uorid <	1 )
					{
					tableClear();
					tabVazia();
					return;
					}
				tableDestroy();
				AjaxSource	=	"partes/tableData.php?query=uorautos&uorid=" + uorid;
				}
			else
				{
				tableDestroy();
				if( uorid < 1 )
					AjaxSource	=	"partes/tableData.php?query=uorautos";
				else
					AjaxSource	=	"partes/tableData.php?query=uorautos&uorid=" + uorid;
				}
			inicializa.init();
			}

		function autorizar()
			{
			if( typeof idauto === 'undefined' )
				{
				alert( "Por favor, escolha o autorizador" );
				return;
				}
			var ini = $("#dtinicria").datepicker("getDate");
			if( ini < dtuprc )
				{
				var aux = "o início do período de autorização deve ser \n" +
									"superior a "+$.datepicker.formatDate("dd/mm/yy", dtuprc );
				alert( aux );
				return;
				}
			var fim = $("#dtfimcria").datepicker("getDate");
			if( fim != null && fim <= ini )
				{
				alert( "O término do período de autorização deve ser superior ao inicio." );
				return;
				}
			var url = "partes/adicionaFUAU.php" +
								"?funiid="+ idauto + 
								"&uorid=" + uorid +
								"&dtini=" + $.datepicker.formatDate( "yymmdd", ini );
			if( fim == null )
				url += "&dtfim=NULL";
			else
				url += "&dtfim=" + $.datepicker.formatDate( "yymmdd", fim );
			var resul = remoto( url );
			if( resul.status == "OK" )
				$("#modcriaautor").modal('hide');
			else
				{
				alert( "Erro adicionando período de autorização: " + resul.erro );
				return;
				}
			atuatab(false);
			}

	function encerra( id, ini, fim )
		{
		if( !jurisdUOR( sshd, uorid ) )
			return;
		//
		dtfim = $.datepicker.formatDate("yymmdd", dtuprc )
		var url = "partes/alteraFUAU.php?fuauid="+ id;
		url += "&dtini=" + ini;
		url += "&dtfim=" + dtfim;
		var resul = remoto( url );
		if( resul.status != "OK" )
			{
			alert( "Erro adicionando período de autorização: " + resul.erro );
			return;
			}
		atuatab( false );
		}
		
	function alterar()
		{
		if( dtini == null && dtfim == null )
			{
			$('#modaltautor').modal('hide');
			return;
			}
		//
		dtini = $(".dtinialt").datepicker("getDate");
		//
		var url = "partes/alteraFUAU.php?fuauid="+ fuauid;
		url += "&dtini=" + toStDate( dtini, 2 );
		//if( dtfim != null )
		url += "&dtfim=" + dtfim;
		var resul = remoto( url );
		if( resul.status == "OK" )
			$("#modaltautor").modal('hide');
		else
			{
			alert( "Erro adicionando período de autorização: " + resul.erro );
			return;
			}
		atuatab( false );
		}
		
	function telaAlter( id, nome, iniinv, fiminv )
		{
		if( !jurisdUOR( sshd, uorid ) )
			return;
		//
		fuauid = id;
		var inidir = "";
		var fimdir = "";
		dtini = null;
		dtfim = null;

		inidir = toDateDir(""+iniinv);
		dtini	=	toDate(""+inidir);
		
		if( typeof fiminv != 'undefined' && fiminv != "" )
			{
			fimdir = toDateDir(""+fiminv);
			dtfim = toDate(""+fimdir);
			}
			
		$(".uorpto").val( nouor )
		$("#dtinialt").val( inidir );
		let dt = $(".dtinialt").datepicker("getDate");
		if( dt < hoje )
			$("#dtinialt").datepicker( "option", "minDate", dt );
		else
			$("#dtinialt").datepicker( "option", "minDate", hoje );
		if( fiminv == "" )
			$("#dtfimalt").val( "" );
		else
			$("#dtfimalt").val( fimdir );
		//	acerta o minimo
		if( dt < hoje )
			$("#dtfimalt").datepicker( "option", "minDate", hoje );
		else
			{
			dt.setDate(dt.getDate()+1);
			$("#dtfimalt").datepicker( "option", "minDate", dt );
			}

		$("#altautors").val( nome );
		dtini = null;
		dtfim = null;
		$("#modaltautor").modal('show');
		}
		
	function remove( id )
		{
		if( !jurisdUOR( sshd, uorid ) )
			return;
		var url = "partes/updates.php?query=delfuau&fuauid="+ id;
		var resul = remoto( url );
		if( resul.status == "OK" )
			atuatab( false );
		else
			alert( "Erro adicionando período de autorização: " + resul.erro );
		}
		
	$('#eddt_new').click(function( e )
		{
		e.stopImmediatePropagation();
		if( uorid < 1 )
			{
			alert( "Por favor, escolha uma UOR a qual adicionar autorizador." );
			return;
			}
		if( !jurisdUOR( sshd, uorid ) )
			return;
		//
		$("#uorpto").val( nouor )
		$("#dtinicria").val( $.datepicker.formatDate("dd/mm/yy", hoje ) );
		let amanha = hoje;
		amanha.setDate(hoje.getDate()+1);
		$("#dtfimcria").datepicker( "option", "minDate", amanha );
		$("#dtfimcria").val( "" );
		$(".autors").select2('val', 0 );
		$("#modcriaautor").modal('show');
		} );

		/*
	//	combo de tipo de ausências
	url =	"selectData.php?query=funcfuni";
	SelInit( "#tiaus", url, 0, "Escolha abaixo", escoautor );
	taauid = 0;
		*/
	 
	function escocandid( tipo, id, text )
		{
		if( id > 0 )
			{
			idauto = id;
			noauto = text;
			}
		}
		
	function escofuor( tipo, id, text	 )
		{
		uorid	=	id;
		nouor	= text;
		//	prepara a combo de candidatos
		setBasicAuth( sshd, pass );
		url =	"selectData.php?query=candidauto&fuorid="+id;
		SelInit( ".autors", url, 0, "Escolha abaixo", escocandid );
		//
		setAjax();
		}

	function escoautor( tipo, id, text	 )
			{
			if( id > 0 )
				{
				sshdautor	=	id;
				noautor	= text;
				}
			}

	/////////////// PRINCIPAL ////////////////////////
	//	obtem dados do autorizador
	/////////////////////////////////////////
	var dtini = null;
	var dtfim = null;
	var fuauid = -1;
	var parms, resu;
	var sshd = obterCookie( "user" );
	if( sshd == null )
		{
		Deslogar();
		}

	var pass = obterCookie( "pass" );
	if( pass == null )
		{
		Deslogar();
		}

	var tiuser = obterCookie( "tiuser" );
	
	//	verifica se o logado é um autorizador perene

	//	obtenção de datas 
	var hoje = new Date();
	parms = "";
	resu = Select( "parametros", parms );
	if( resu == null )
		throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
	var dtuinv = resu.dados[0].DTUPROC;
	var dtudir = toDateDir( dtuinv );
	var dtuprc = toDate( dtudir );

	$('#dtuprc').val( dtudir );

	//	datepickers
	$( ".dtinicria" ).datepicker(
		{
		dateFormat: "dd/mm/yy",
		altFormat: "yymmdd",
		startView: 2,
		todayBtn: true,
		daysOfWeekHighlighted: "0,6",
		autoclose: true,
		minDate: dtuprc,
		todayHighlight: true			
		}).on('change.dp', function(e)
			{ 
			var dt = $(".dtinicria").datepicker("getDate");
			dtini = $.datepicker.formatDate("yymmdd", dt );
			$("#dtfimcria").datepicker( "option", "minDate", dt.getDate()+1 );
			$("#dtfimcria").val( "" );
			});

	$( ".dtfimcria" ).datepicker(
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
			var dt = $(".dtfimcria").datepicker("getDate");
			dtfim = $.datepicker.formatDate("yymmdd", dt );
			});

	$( ".dtinialt" ).datepicker(
		{
		dateFormat: "dd/mm/yy",
		altFormat: "yymmdd",
		startView: 2,
		todayBtn: true,
		daysOfWeekHighlighted: "0,6",
		autoclose: true,
		minDate: dtuprc,
		todayHighlight: true			
		}).on('change.dp', function(e)
			{ 
			let dt = $(".dtinialt").datepicker("getDate");
			dtini = $.datepicker.formatDate("yymmdd", dt );
			dt.setDate(dt.getDate()+1);
			$("#dtfimalt").datepicker( "option", "minDate", dt );
			$(".dtfimalt").val( "" );
			});

	$( ".dtfimalt" ).datepicker(
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
			var dt = $(".dtfimalt").datepicker("getDate");
			dtfim = $.datepicker.formatDate("yymmdd", dt );
			});


	//	obtem FUNI_ID do autorizador
	parms = "&sshd=" + sshd;
	resu = Select( "funiid", parms );
	if( resu == null )
		throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
	var autorid = resu.dados[0].FUNI_ID;

	$("#titwidget").html( "" );

	//	tratamento das datas 	
	//	combo de UORS
	if( tiuser < "4" )
		{
		url =	"selectData.php?query=fuors&sshd="+sshd;
		SelInit( "#fuors", url, 0, "Escolha abaixo", escofuor );
		}
	if( tiuser >= 4 )
		{
		url =	"selectData.php?query=fuors&semzero&maisum=todos&numais=0";
		SelInit( "#fuors", url, 0, "todos", escofuor );
		uorid	=	0;
		nouor	= "todos";
		}

	//	formatadores ligados ao datatables
	var liNova			=
		{
		"SSHD": "",
		"NOME": "",
		"INICIO": "",
		"TERMINO": ""
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
		"width": "10%",
		"aTargets": [ ++col ],
		"mData": "SSHD",
		"sTitle":"SSHD",
		"defaultContent": " "
		};
	colDefs.push( aux );

	aux	=
		{
		"tipo": "t",
		"editavel": true,
		"vanovo": "",
		"width": "15%",
		"aTargets": [ ++col ],
		"mData": "SIGLAUOR",
		"sTitle":"UOR",
		"defaultContent": " "
		};
	colDefs.push( aux );

	aux	=
		{
		"tipo": "t",
		"editavel": true,
		"vanovo": "",
		"width": "40%",
		"aTargets": [ ++col ],
		"mData": "NOME",
		"sTitle":"Autorizador",
		"defaultContent": " "
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
			var stnow = toStDate( new Date(), 2 );
			var stini = toDateInv( row.INICIO );
			var stter = toDateInv( row.TERMINO );

			var acalt = "<a href='#' onClick='javascript:telaAlter(" + row.FUAUID + 
									",\"" + row.NOME + "\"," + stini + "," + stter + ");' " +
									"class='btn btn-circle btn-info btn-xs' " +
									"title=\"Modifica as datas do período de autorização\" >" +
									"<i class='glyphicon glyphicon-edit'></i></a>";
			var acrem = "<a href='#' onClick='javascript:remove(" + row.FUAUID + ");' " +
									"class='btn btn-circle btn-info btn-xs' " +
									"title=\"Remove este período de autorização\" >" +
									"<i class='glyphicon glyphicon-remove'></i></a>";
			var acenc = "<a href='#' onClick='javascript:encerra(" + row.FUAUID + 
									"," + stini + "," + stter + ");' " +
									"class='btn btn-circle btn-info btn-xs' " +
									"title=\"Encerra o período de autorização\" >" +
									"<i class='glyphicon glyphicon-log-in'></i></a>";

			if( stini == "" )
				return "";
			if( stnow < stini )
				return acalt+acrem;

			if( stter == "" )
				return acalt+acenc;

			if( stnow == stter )
				return acalt;

			if( stnow < stter )
				return acalt+acenc;
			return "";
			}
		};
	colDefs.push( aux );
	///////////////////////////////////////////////////////////////////////

	if( tiuser < 4 )
		{
		AjaxSource	=	"";
		inicializa.init();
		tabVazia();
		}
	else
		setAjax();
				
		</script>
	</body>	
</html>
