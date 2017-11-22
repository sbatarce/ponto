<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    <title>Ponto do funcionário</title>
		<style>
		.table thead>tr>th {text-align: center;}
		.direito {text-align: right;}
		.centro  {text-align: center;}
		.esquerdo {text-align: left;}
		.no-close .ui-dialog-titlebar-close {  display: none;	}
		</style>
<?php
include 'partes/Head.php';
?>
		<!-- icone da PMS -->
		<link rel="shortcut icon" href="/imagens/PMSICO.png">
	</head>
	
	<body onload="javascript:titulo( '<h4>Fechamento</h4>' );">
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
									style="width: 15%; margin-right: 30px; " />
				<input type="text" id="dtuprc" class="dtuprc"
						 style="margin-left: 10px; width: 10%; float: right;
						 font-size: 20px; font-weight: bold; " readonly/>
				<label style="float: right; font-weight: bold; ">última coleta completada em</label>
			</div>
			<div class="col-lg-6" style='width:100%; margin-top: 20px; '>
				<input class="btn btn-danger" type="button" value="Executar"
							 onclick="javascript:executar();"
							 style="float: left; font-size: 20px; font-weight: bold; "
							 title="Efetua todos os fechamentos marcados">
				<input class="btn btn-primary" type="button" value="Inverter"
							 onclick="javascript:inverter();"
							 style="margin-left: 10px; float: right; "
							 title="Desmarca os marcados e marca os desmarcado">
				<input class="btn btn-primary" type="button" value="Desmarcar"
							 onclick="javascript:desmarcarTodos();"
							 style="margin-left: 10px; float: right; "
							 title="Desmarca todos os funcionário para fechamento">
				<input class="btn btn-primary" type="button" value="Marcar"
							 onclick="javascript:marcarTodos();"
							 style="margin-left: 10px; float: right; "
							 title="Marca todos os funcionário para fechamento">

				<input type="text" size="10" id="dtfecha" 
						 style="margin-left: 20px; margin-right: 20px; float: right; "/>
				<label style="float: right; font-weight: bold; ">Fechamento</label>
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

<?php
include 'partes/Scripts.php';
?>

	<script type="text/javascript" src="partes/geral.js" ></script>
	<script type="text/javascript" src="partes/dteditavel.js" ></script>
	<script type="text/javascript" >
		/*
		function logout()
			{
			Deslogar();
			}
		*/
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
		
		//	tratamento inicial das datas e inicialização do datatables
		function setAjax(  )
			{
			if( uorid <= 0 )
				return;
			tableDestroy();
			AjaxSource	=	"partes/tableData.php?query=funcfuor&uor=" + uorid;
			ixrow=0;
			inicializa.init();
			}

	$('#eddt_new').hide();
	$('#eddt_new').click(function( e )
		{
		e.stopImmediatePropagation();
		} );
			
	function escofuor( tipo, id, text	 )
		{
		if( id <= 0 )
			return;
		uorid	=	id;
		nouor	= text;
		setAjax();
		//$(".fuors").select2('val', 0 );
		}
		
	function marcaFechar( marca )
		{
		var ix = ""+ixrow;
		ixrow++;
		var ret	=	"<a href='#' onClick='javascript:";
		if( marca == "sim" )
			{
			ret +=	"fechar(" + ix + ");' " +
							"class='btn btn-circle btn-info btn-xs' " +
							"title=\"Este funcionário será fechado na data acima\" >" +
							"<i class='glyphicon glyphicon-ok'></i></a>";
			}
		else
			{
			ret +=	"naoFechar(" + ix + ");' " +
							"class='btn btn-circle btn-info btn-xs' " +
							"title=\"Este funcionário não será fechado\" >" +
							"<i class='glyphicon glyphicon-remove'></i></a>";
			}
		return ret;
		}
		
	function fechar( ix )
		{
		var row = Table.fnGetData( ix );
		row.FECHAR = "0";
		ixrow = ix;
		Table.api().row(ix).data(row);
		}
		
	function naoFechar( ix )
		{
		var row = Table.fnGetData( ix );
		row.FECHAR = "1";
		ixrow = ix;
		Table.api().row(ix).data(row);
		}
		
	function marcarTodos()
		{
		var qtlin = tableQtLins();
		if( qtlin < 1 )
			return;
		var row;
		ixrow	=	0;
		for( var ix=0; ix<qtlin; ix++ )
			{
			row = Table.fnGetData( ix );
			if( row.FECHAR == "0" )
				{
				row.FECHAR = "1";
				Table.api().row(ix).data(row);
				}
			}
		}

	function desmarcarTodos()
		{
		var qtlin = tableQtLins();
		if( qtlin < 1 )
			return;
		var row;
		ixrow	=	0;
		for( var ix=0; ix<qtlin; ix++ )
			{
			row = Table.fnGetData( ix );
			if( row.FECHAR == "1" )
				{
				row.FECHAR = "0";
				Table.api().row(ix).data(row);
				}
			}
		}

	function inverter()
		{
		var qtlin = tableQtLins();
		if( qtlin < 1 )
			return;
		var row;
		ixrow	=	0;
		for( var ix=0; ix<qtlin; ix++ )
			{
			row = Table.fnGetData( ix );
			if( row.FECHAR == "1" )
				row.FECHAR = "0";
			else
				row.FECHAR = "1";
			Table.api().row(ix).data(row);
			}
		}
		
	function fechaFunc( sshd, data )
		{
		//	funiid do funcionário
		let parms = `&sshd=${sshd}`;
		let resu = Select( "funiid", parms );
		if( resu == null )
			throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
		let funiid = resu.dados[0].FUNI_ID;
		//	verifica se o funcionário tem pendências anteriores à data de fechamento
		let url = `partes/fechaFuncionario.php?funiid=${funiid}&data=${data}`;
		resu = remoto( url );
		if( resu.status != "OK" )
			return false;
		return true;
		}
		
	function executar()
		{
		var aux = "Fechar todos os funcionários marcados?\n" +
							"Por favor confirme...;"
		if( !confirm( aux ) )
			return;
		bloqueia();
		var qtlin = tableQtLins();
		if( qtlin < 1 )
			return;
		var row;
		ixrow	=	0;
		var qtmarc = 0;						//	quantidade de funcionarios marcados
		var qterro = 0;						//	quantidade de erros
		for( var ix=0; ix<qtlin; ix++ )
			{
			row = Table.fnGetData( ix );
			if( row.FECHAR == "1" )
				{
				qtmarc++;
				if( fechaFunc( row.SSHD, dtfecha ) )
					{
					row.FECHAR = "0";
					row.FECHAMENTO = dtudir;
					Table.api().row(ix).data(row);
					}
				else
					qterro++;
				}
			}
		libera();
		if( qterro > 0 )
			{
			if( qterro == 1 )
				{
				aux = "" + qterro + " funcionário de ";
				if( qtmarc == 1 )
					aux += qtmarc + " marcado ";
				else
					aux += qtmarc + " marcados ";
				aux += "não pode ser fechado.\n" +
							"Provavelmente por ter pendencias anteriores\n" +
							"à data de fechamento estabelecida.";
				}
			else
				{
				aux = "" + qterro + " funcionários de ";
				if( qtmarc == 1 )
					aux += qtmarc + " marcado ";
				else
					aux += qtmarc + " marcados ";
				aux += "não puderam ser fechados.\n" +
							"Provavelmente por terem pendencias anteriores\n" +
							"à data de fechamento estabelecida.";
				}
			alert( aux );
			}
		}

		/////////////// PRINCIPAL ////////////////////////
		//	obtem dados do autorizador
		/////////////////////////////////////////
		var ixrow = 0;
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
		if( tiuser == null || tiuser < 4 )
			{
			Deslogar();
			}
		//	obtenção de datas 
		var hoje = new Date();
		parms = "";
		resu = Select( "parametros", parms );
		if( resu == null )
			throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
		var dtuinv = resu.dados[0].DTUPROC;
		var dtudir = toDateDir( dtuinv );
		var dtuprc = toDate( dtudir );
		var dtfecha = dtuinv;
		
		$('#dtuprc').val( dtudir );
		$('#dtfecha').val( dtudir );

		//	datepickers
		$( "#dtfecha" ).datepicker(
			{
			dateFormat: "dd/mm/yy",
			altFormat: "yymmdd",
			startView: 2,
			todayBtn: true,
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			maxDate: dtuprc,
			todayHighlight: true			
			}).on('change.dp', function(e)
				{ 
				var dt = $("#dtfecha").datepicker("getDate");
				dtfecha = $.datepicker.formatDate("yymmdd", dt );
				dtudir = $.datepicker.formatDate("dd/mm/yy", dt );
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
		url =	"selectData.php?query=uorsaut";
		url += "&sshd="+sshd;
		SelInit( "#fuors", url, 0, "Escolha abaixo", escofuor );
		
		//	formatadores ligados ao datatables
		var liNova			=
						{
						"SSHD": "",
						"NOME": "",
						"FECHAMENTO": "",
						"FECHAR": "0"
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
			"width": "5%",
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
			"width": "25%",
			"aTargets": [ ++col ],
			"mData": "NOME",
			"sTitle":"Funcionário",
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
			"mData": "FECHAMENTO",
			"sTitle":"Fechamento",
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
			"mData": "DTMAX",
			"sTitle":"Maior Fechamento",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=	
			{
			"tipo": null,
			"editavel": false,
			"vanovo": "",
			"sTitle":"Fechar?",
			"bSortable": false,
			"searchable": false,
			"aTargets": [ ++col ],
			"orderable":false,
			"mData": "FECHAR",
			"width": "5%",
			"render": function( data, type, row, meta )
				{
				if( data == "0" )
					return	marcaFechar( "nao" );
				else
					return	marcaFechar( "sim" );
				}
			};
		colDefs.push( aux );
		///////////////////////////////////////////////////////////////////////

		AjaxSource	=	"";
		ixrow=0;
		inicializa.init();
		var data = [];
		data.push( 
						{
						"SSHD": "",
						"NOME": "Escolha uma UOR acima",
						"FECHAMENTO": "",
						"FECHAR": ""
						} );
		Table.fnAddData( data, true );
				
		</script>
	</body>	
</html>
