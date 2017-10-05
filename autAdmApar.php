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
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link href="bootstrap-3.3.1/dist/css/bootstrap-switch.css" rel="stylesheet">
	</head>
	
	<body onload="javascript:titulo( '<h4>Lista de todos todos os usuários do Autorizador</h4>' );">
<?php
include 'partes/MenuPri.php';
include 'partes/Cabec.php';
include 'partes/pageheader.php';
include 'partes/pagebody.php';
?>
		<div class='row form-group' 
				 style='margin-bottom: 5px; margin-left:4px;' data-toggle="buttons">
			<div class="col-lg-6" style='width:30%;'>
					Aparelho<input style='width:70%; margin-left:10px;' 
												 class='input-small aparelhos' id="selapar" 
												 title="Escolha o aparelho a adicionar pessoas"/>
			</div>
			<div class="col-lg-6" style='width:40%;'>
					Pessoas da UOR<input style='width:70%; margin-left:10px;' 
															 class='input-small uors' id="seluor" 
															 title="UOR em que as pessoas estão"/>
			</div>
			<div class="col-lg-6" style='width:30%;'>
				<input class="btn btn-primary" type="button" value="Obter"
							 onclick="javascript:setAjax();"
							 title="Obtem todos os funcionários da 
											UOR especificada à esquerda">
				<input class="btn btn-primary" type="button" value="Atribuir"
							 onclick="javascript:atribuir();"
							 title="Atribui Regime e UOR iniciais a todos
											os funcionários não atribuídos.
											Funcionários já com regime e UOR
											atribuídos permanecerão sem alteração.">
				<input class="btn btn-primary" type="button" value="Colocar"
							 onclick="javascript:colocar();"
							 title="Coloca as pessoas marcadas no aparelho.
											Salva Regimes e UOR de alocação.">
			</div>
		</div>
		<div class='row form-group' 
				 style='margin-bottom: 5px; margin-left:4px;' data-toggle="buttons">
			<div class="col-lg-6" style='width:30%;'>
					Regime inicial<input style='width:70%; margin-left:10px;' 
															 class='input-small regimes' id="selreg" 
										 title="Escolha um regime que em princípio será atribuído a todos"/>
			</div>
			<div class="col-lg-6" style='width:40%;'>
					UOR Inicial<input style='width:70%; margin-left:10px;' 
														class='input-small uoralo' id="selreg" 
														title="Escolha uma UOR de alocação inicial"/>
			</div>
			<div class="col-lg-6" style='width:30%;'>
				<div class="checkbox">
					<div id="divanali" class="checkbox-inline make-switch has-switch" >
						<input id="cktodos" type="checkbox" 
									 data-size="mini" data-label-text=''
									 data-off-color="danger" data-on-color="success"
									 data-off-text="Não Alocados" data-on-text="Todos" >
					</div>
				</div>
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

	<script type="text/javascript" src="bootstrap-3.3.1/dist/js/bootstrap-switch.js"></script>
	<script type="text/javascript" src="partes/geral.js" ></script>
	<script type="text/javascript" src="partes/dteditavel.js" ></script>
	<script type="text/javascript" >
		
		$('#cktodos').bootstrapSwitch('state', false);

		$("#cktodos").on( 'switchChange.bootstrapSwitch', function( evn, state )
			{
			fltodos = state;
			});

		function logout()
			{
			Deslogar();
			}
			
		function escapar( tipo, id )
			{
			if( id > 0 )
				idapal	=	id;
			}

		function escuors( tipo, id )
			{
			if( id > 0 )
				iduors	=	id;
			}

		function escregi( tipo, id, text )
			{
			if( id > 0 )
				{
				idregi	=	id;
				noregi	= text;
				}
			}

	function escualo( tipo, id, text	 )
			{
			if( id > 0 )
				{
				idualo	=	id;
				noualo	= text;
				}
			}

		var url = "selectData.php?query=aparelhos";
		SelInit( ".aparelhos", url, 0, "Escolha abaixo", escapar, 0 );

		url = "selectData.php?query=uor";
		SelInit( ".uors", url, 0, "Escolha abaixo", escuors, 0 );

		url = "selectData.php?query=regimes";
		SelInit( ".regimes", url, 0, "Escolha abaixo", escregi, 0 );

		var url = "selectData.php?query=uor";
		SelInit( ".uoralo", url, 0, "Escolha abaixo", escualo, 0 );

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
		function pendencias( sshd )
			{
			criarCookie( "sshdfunc", sshd );
			window.location = "autPenden.php";
			}
			
		function ausencias( sshd, id, nome )
			{
			criarCookie( "idfunc", id );
			criarCookie( "nofunc", nome );
			criarCookie( "sshdfunc", sshd );
			window.location = "autAusencias.php";
			}
			
		function correcoes( sshd )
			{
			criarCookie( "idfunc", id );
			criarCookie( "nofunc", nome );
			criarCookie( "sshdfunc", sshd );
			window.location = "autCorrecao.php";
			}
			
		//	chama o detalhe de um funcionário escolhido
		function detfunc( id, nome, sshd )
			{
			criarCookie( "idfunc", id );
			criarCookie( "nofunc", nome );
			criarCookie( "sshdfunc", sshd );
			window.location = "autFuncio.php";
			}
			
		//	atribui regime e UOR ao funcionários não atribuídos
		function atribuir()
			{
			if( idregi < 0 || idualo < 0 )
				{
				alert( "É necessário escolher Regime e UOR iniciais para atribuir.");
				return;
				}
			atrib = true;
			setAjax();
			}

		//	tratamento inicial das datas e inicialização do datatables
		function setAjax(  )
			{
			if( iduors <= 0 )
				return;
			if( idapal <= 0 )
				return;
			//
			tableDestroy();
			AjaxSource	=	"partes/tableData.php?query=autfuuor&iduor=" + iduors;
			if( fltodos )
				AjaxSource += "&todos";
			inicializa.init();
			}
		/////////////// PRINCIPAL ////////////////////////
		var idapal = -1;
		var iduors = -1;
		var idregi = -1;
		var noregi = "";
		var idualo = -1;
		var noualo = "";
		var fltodos = false;
		var atrib = false;

		var sshd = obterCookie( "user" );
		if( sshd == null )
			{
			Deslogar();
			}
			
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
						"IUN": "",
						"NOME": "",
						"DCSIGLAUORSAU": "",
						"REGIME": ""
						};

		//	monta o datatables
		var	order	=	[];											//	sem classificação 
		//	prepara a definiçao das colunas
		var colDefs	=	[];
		var	col	=	-1;

		aux	=
			{
			"tipo": "t",
			"editavel": false,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "IUN",
			"sTitle":"SSHD",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "t",
			"editavel": false,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "NOME",
			"sTitle":"Funcionário",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "t",
			"editavel": false,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "SIGLAUORSAU",
			"sTitle":"UOR SAU",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "APARELHOS",
			"sTitle":"Presente",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				if( full["APARELHOS"] == "" )
					return "<i class='glyphicon glyphicon-remove'></i></a>";
				else
					{
					var apars = full["APARELHOS"].split(',');
					for( var ix=0; ix<apars.length; ix++ )
						{
						if( Number( apars[ix] ) == idapal )
							return "<i class='glyphicon glyphicon-ok'></i></a>";
						}
					return "<i class='glyphicon glyphicon-remove'></i></a>";
					}
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "t",
			"editavel": false,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "REGIME",
			"sTitle":"Regime",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				if( data != "" )
					return data;
				if( !atrib )
					return "a atribuir";
				return noregi;
				}
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "t",
			"editavel": false,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "SIGALUORPONTO",
			"sTitle":"UOR Ponto",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				if( data != "" )
					return data;
				if( !atrib )
					return "a atribuir";
				return noualo;
				}
			};
		colDefs.push( aux );
		/*
		var aux	=
			{
			"tipo": "l",
			"editavel": false,
			"vanovo": "",
			"width": "15%",
			"aTargets": [ ++col ],
			"sTitle":"Ações",
			"defaultContent": " ",
			"render": function( data, type, full )
				{
				res = "";
				res += "<a href='javascript:pendencias(\"" + full.SSHDFUNC + "\");' ";
				res += "<button type='button' title='Verificar pendências do funcionário' ";
				res += "class='btn btn-warning  btn-md'>ANA</button></a>";
				
				res += "<a href='javascript:ausencias( \"" + full.SSHDFUNC;
				res += "\", \""+ full.IDFUNC + "\", \"" + full.NOFUNC + "\" );' ";
				res += "<button type='button' title='Autorizar ausências' ";
				res += "class='btn btn-success  btn-md'>AUS</button></a>";

				res += "<a href='javascript:correcoes(\"" + full.SSHDFUNC;
				res += "\", \""+ full.IDFUNC + "\", \"" + full.NOFUNC + "\" );' ";
				res += "<button type='button' title='Corrigir saldo para mais ou para menos' ";
				res += "class='btn btn-danger  btn-md'>COR</button></a>";

				return res;
				}
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
		*/
		aux	=	
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
			"defaultContent": acsoalt
			};
		colDefs.push( aux );
		
		
		///////////////////////////////////////////////////////////////////////

		setAjax();
				
		</script>
	</body>	
</html>
