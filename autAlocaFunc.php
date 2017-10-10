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
	
	<body onload="javascript:titulo( '<h4>Alocação de funcionários na(s) UOR(s) do autorizador</h4>' );">
<?php
include 'partes/MenuPri.php';
include 'partes/Cabec.php';
include 'partes/pageheader.php';
include 'partes/pagebody.php';
?>
		<div class='row form-group' 
				 style='margin-bottom: 5px; margin-left:4px;' data-toggle="buttons">
			<div class="col-lg-6" style='width:35%;'>
					UOR de PONTO
					<input style='width:80%; margin-left:0px;' 
								 class='input-small uoralo' id="uoralo" 
								 title="UORs sob administração do autorizador"/>
			</div>
			<div class="col-lg-6" style='width:35%;'>
					UOR da qual adicionar pessoas
					<input style='width:80%; margin-left:0px;' 
								 class='input-small uors' id="uors" 
								 title="As pessoas da UOR (do SAU) escolhida e
								 que ainda não constam da tabela,
								 serão incluidas."/>
			</div>
			<div class="col-lg-6" style='width:30%;'>
					Regime a atribuir<input style='width:70%; margin-left:0px;' 
																	class='input-small regimes' id="selreg" 
																	title="Regime a atribuir a todos os 
																	funcionários não lotados na UOR"/>
			</div>
		</div>
		<div class='row form-group' 
				 style='margin-bottom: 5px; margin-left:4px;' data-toggle="buttons">
			<div class="col-lg-6" style='width:50%;'>
				<input class="btn btn-primary" type="button" value="Adicionar por SSHD"
							 onclick="javascript:atribuir();"
							 title="Seleciona uma pessoa a partir do SSHD (do SAU).
							 Adiciona a pessoa na lista abaixo.">
				<input class="btn btn-primary" type="button" value="Alocar pessoas na UOR do PONTO"
							 onclick="javascript:executar();"
							 title="Coloca todas as pessoas da lista
							 abaixo na UOR de PONTO selecionada
							 que ainda não estão na UOR de PONTO.">
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
			
		function escRegime( tipo, id )
			{
			if( id >= 0 )
				idregime	=	id;
			}

	function escualo( tipo, id, text	 )
			{
			if( id > 0 )
				{
				idualo	=	id;
				noualo	= text;
				}
			setAjax();
			}

	function escuors( tipo, id, text	 )
			{
			if( idualo < 0 )
				{
				alert( "Por favor, selecione uma UOR de PONTO ao lado" );
				$(".uors").select2('val', 0 );
				return;
				}
			if( id > 0 )
				{
				iduor	=	id;
				nouor	= text;
				}
			var url	=	"partes/tableData.php?query=funuorbio&iduor=" + iduor;
			var	resu	=	remoto( url );
			if( resu == null )
				{
				alert( "Falha na obtenção dos dados. Por Favor, tente mais tarde")
				return;
				}
			var qt = resu.data.length;
			var data = [];
			if( qt > 0 )
				{
				for( var ix=0; ix<qt; ix++ )
					{
					if( resu.data[ix].IDRETR == "" )
						{
						data.push({	"IUN": resu.data[ix].IUN,
												"NOME": resu.data[ix].NOME,
												"QTBIO": resu.data[ix].QTBIO,
												"REGIME": "Escolha Abaixo",
												"PRESENTE": "não",
												"MANTER": "sim",
												"IDREG": "0",
												"action": "" } );
						}
					else
						{
						data.push({	"IUN": resu.data[ix].IUN,
												"NOME": resu.data[ix].NOME,
												"QTBIO": resu.data[ix].QTBIO,
												"REGIME": resu.data[ix].REGIME,
												"PRESENTE": "não",
												"MANTER": "sim",
												"IDREG": resu.data[ix].IDRETR,
												"action": "" } );
						}
					}
				var lis = Table.fnAddData( data, false );
				for( var ix=0; ix < lis.length; ix++ )
					{
					var fica = "<a href='#' onClick='javascript:remove(" + lis[ix] + ")' " +
										"class='btn btn-circle btn-info btn-xs remover' " +
										"title=\"não colocar este funcionário na UOR de ponto\" >" +
										"<i class='glyphicon glyphicon-ok'></i></a>";
					Table.fnUpdate( fica, lis[ix], 5, fica, false );
					}
				Table.fnDraw( true );
				}
			$(".uors").select2('val', 0 );
			}

		function escregi( tipo, id, text )
			{
			if( idualo < 1 )
				{
				$(".regimes").select2('val', 0 );
				return;
				}
			if( id < 1 )
				{
				idregi	=	-1;
				noregi	= "";
				return;
				}
			idregi	=	id;
			noregi	= text;
			//
			var qtlin = tableQtLins();
			var row;
			for( var ix=0; ix<qtlin; ix++ )
				{
				row = Table.fnGetData( ix );
				if( row["action"] == "" )
					continue;
				if( row["IDREG"] == "0" )
					{
					row["REGIME"] = noregi;
					row["IDREG"] = idregi;
					Table.api().row(ix).data(row);
					}
				}
			}
			
		function adiciona( ix )
			{
			var fica = "<a href='#' onClick='javascript:remove(" + ix + ")' " +
									"class='btn btn-circle btn-info btn-xs adicionar' " +
									"title=\"remover funcionário do aparelho\" >" +
									"   <i class='glyphicon glyphicon-ok'></i></a>"
			var row = Table.fnGetData( ix );
			row["action"] = remo;
			row["MANTER"] = "sim";
			Table.api().row(ix).data(row);
/*
			Table.fnUpdate( "sim", ix, 5, false );
			Table.fnUpdate( act, ix, 6 );
			flalt = true;
	*/
			}
		
			
		function remove( ix )
			{
			var tira =	"<a href='#' onClick='javascript:adiciona(" + ix + ")' " +
									"class='btn btn-circle btn-info btn-xs adicionar' " +
									"title=\"adicionar funcionário ao aparelho\" >" +
									"   <i class='glyphicon glyphicon-remove'></i></a>"
			var row = Table.fnGetData( ix );
			row["action"] = tira;
			row["MANTER"] = "não";
			Table.api().row(ix).data(row);
/*
			Table.fnUpdate( "nao", ix, 5, false );
			Table.fnUpdate( act, ix, 6 );
			flalt = true;
			*/
			}
			
		function executar()
			{
			var qtlin = tableQtLins();
			if( qtlin < 1 )
				{
				var txt = "Nada a fazer. \n" +
						"Por favor, escolha uma UOR de PONTO,\n" +
						"e faça adequações. Somente depois\n" +
						"poderá ser feita a alocação.";
				alert( txt )
				return;
				}
			var row;
			for( var ix=0; ix<qtlin; ix++ )
				{
				row = Table.fnGetData( ix );
				if( row["PRESENTE"] == "1" )
					continue;
				if( row["MANTER"] != "1" )
					continue;
				if( row["IDREG"] <= 0 )
					{
					var txt = "Todos os funcionários a criar devem ter REGIME.\n" +
										"Nenhuma criação efetuada.\n" +
										"Clique no ícone de edição à direita " +
										"e corrija o regime do(s) funcionário(s)";
					alert( txt );
					return;
					}
				}
			for( var ix=0; ix<qtlin; ix++ )
				{
				row = Table.fnGetData( ix );
				if( row["PRESENTE"] == "1" )
					continue;
				if( row["MANTER"] != "1" )
					continue;
				var url = "partes/criaFuni.php?sshd=" + row["IUN"] +
									"&idretr=" + row["IDREG"] +
									"&ualoc=" + idualo;
				var resul = remoto( url );
				if( resul.status != "OK" )
					{
					var txt = "Erro:\n" + resul.erro + "\ncriando o funcionário:\n" +
										row["NOME"];
					alert( txt );
					}
				}
			}

		function deleteRow( oTable, nRow )
			{
			if( DBDelete( oTable, nRow ) )
				deleteRowG( oTable, nRow );
			}
			
		function restoreRow( oTable, nRow )
			{
			restoreRowG( oTable, nRow );
			var row = Table.fnGetData( nRow );
			row["action"] = acsoalt;
			Table.api().row(nRow).data(row);
			}
			
		function saveRow( oTable, nRow, idnovo )
			{
			saveRowG( oTable, nRow, null );
			var row = Table.fnGetData( nRow );
			if( idregime > 0 )
				row["IDREG"] = idregi;
			row["action"] = acsoalt;
			Table.api().row(nRow).data(row);
			}
			
		function editRow( oTable, nRow )
			{
			idregime = 0;
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
			
		//	tratamento inicial das datas e inicialização do datatables
		function setAjax(  )
			{
			if( idualo <= 0 )
				return;
			//
			tableClear();
			
			var url	=	"partes/tableData.php?query=funuorbio&janafuor&iduor=" + idualo;
			var	resu	=	remoto( url );
			if( resu == null )
				{
				alert( "Falha na obtenção dos dados. Por Favor, tente mais tarde")
				return;
				}
			var qt = resu.data.length;
			var data = [];
			if( qt > 0 )
				{
				for( var ix=0; ix<qt; ix++ )
					{
					data.push({	"IUN": resu.data[ix].IUN,
											"NOME": resu.data[ix].NOME,
											"QTBIO": resu.data[ix].QTBIO,
											"REGIME": resu.data[ix].REGIME,
											"PRESENTE": "sim",
											"MANTER": "sim",
											"IDREG": "",
											"action": ""} );
					}
				Table.fnAddData( data, true );
				//atuatab();
				}
			}
		/////////////// PRINCIPAL ////////////////////////
		var idualo = -1;
		var noualo = "";
		var iduor = -1;
		var nouor = "";
		var idregi = -1;
		var idregime = -1;
		var noregi = "";
		var fltodos = false;

		var sshd = obterCookie( "user" );
		if( sshd == null )
			{
			Deslogar();
			}
			
		var url = "selectData.php?query=uorsaut&sshd=" + sshd;
		SelInit( ".uoralo", url, 0, "Escolha abaixo", escualo, 0 );

		url = "selectData.php?query=uor";
		SelInit( ".uors", url, 0, "Escolha abaixo", escuors, 0 );

		url = "selectData.php?query=regimes";
		SelInit( ".regimes", url, 0, "Escolha abaixo", escregi, 0 );
		
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
		var	order	=	[4, 'asc'];											//	sem classificação 
		//	prepara a definiçao das colunas
		var colDefs	=	[];
		var	col	=	-1;

		aux	=
			{
			"tipo": "t",
			"editavel": false,
			"vanovo": "",
			"width": "5%",
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
			"width": "15%",
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
			"mData": "QTBIO",
			"sTitle":"Biometrias",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "l",
			"editavel": false,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "REGIME",
			"sTitle":"Regime",
			"selID": "IDREG",
			"classe": "cbregi",
			"selVal": "REGIME",
			"selminlen": 0,
			"selURL": "selectData.php?query=regimes",
			"funcEscolha": escRegime,
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
			"mData": "PRESENTE",
			"sTitle":"Presente",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=	
			{
			"tipo": null,
			"editavel": false,
			"vanovo": "",
			"sTitle":"Adicionar",
			"bSortable": false,
			"searchable": false,
			"aTargets": [ ++col ],
			"orderable":false,
			"mData": "action",
			"width": "10%",
			"defaultContent": ""
			};
		colDefs.push( aux );
		
		
		///////////////////////////////////////////////////////////////////////

		AjaxSource	=	"";
		inicializa.init();
		var data = [];
		data.push( {	"IUN": "",
									"NOME": "Escolha uma UOR de PONTO acima",
									"QTBIO": "",
									"REGIME": "",
									"PRESENTE": "",
									"MANTER": "",
									"IDREG": "",
									"action": ""} );
		Table.fnAddData( data, true );
				
		</script>
	</body>	
</html>
