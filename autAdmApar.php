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
	
	<body onload="javascript:titulo( '<h4>Pessoas em aparelhos</h4>' );">
<?php
include 'partes/MenuPri.php';
include 'partes/Cabec.php';
include 'partes/pageheader.php';
include 'partes/pagebody.php';
?>
		<div class='row form-group' 
				 style='margin-bottom: 5px; margin-left:4px;' data-toggle="buttons">
			<div class="col-lg-6" style='width:20%;'>
					Aparelho
					<input style='width:100%; ' 
								 class='input-small aparelhos' id="selapar" 
								 title="Escolha o aparelho a adicionar pessoas"/>
			</div>
			<div class="col-lg-6" style='width:40%;'>
					Adicionar todas as pessoas da UOR
					<input style='width:100%; ' 
								 class='input-small uors' id="selfuor" 
								 title="UOR em que as pessoas estão"/>
			</div>
			<div class="col-lg-6" style='width:30%;'>
					Adicionar pessoas
					<input style='width:100%; ' 
								 class='input-small pess' id="selpes" 
								 title="UOR em que as pessoas estão"/>
			</div>
		</div>

		<div class='row form-group' 
				 style='margin-bottom: 5px; margin-left:4px;' data-toggle="buttons">
			<div class="col-lg-6" style='width:50%;'>
				<input class="btn btn-primary" type="button" value="Efetuar modificações"
							 onclick="javascript:executar();"
							 title="Efetua todas as modificações marcadas 
							 no banco de dados, adiciona e remove as pessoas 
							 no aparelho selecionado em Aparelho.">
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

		var acadic =	"<a href='#' onClick='javascript:adiciona(" + "ix" + ")' " +
									"class='btn btn-circle btn-info btn-xs adicionar' " +
									"title=\"adicionar ao aparelho\" >" +
									"   <i class='glyphicon glyphicon-remove'></i></a>";

		$('#cktodos').bootstrapSwitch('state', false);

		$("#cktodos").on( 'switchChange.bootstrapSwitch', function( evn, state )
			{
			fltodos = state;
			});

		function logout()
			{
			Deslogar();
			}
			
		function executar()
			{
			var qtlin = tableQtLins();
			if( qtlin < 1 )
				{
				var txt = "Nada a fazer. \n" +
						"Por favor, escolha um Aparelho,\n" +
						"remova e/ou inclua pessoas nele e\n" +
						"pressione Efetuar Modificações novamente.";
				alert( txt )
				return;
				}
			var row;
			for( var ix=0; ix<qtlin; ix++ )
				{
				row = Table.fnGetData( ix );
				if( row["PRESENTE"] == row["MANTER"] )
					continue;
				if( row["MANTER"] != "sim" )
					continue;
				//	adicionar pessoa ao aparelho
				var url = "partes/adicionaApar.php?funiid=" + row["FUNIID"] +
									"&apalid=" + idapal;
				var resul = remoto( url );
				if( resul.status != "OK" )
					{
					var txt = "Anote o erro: " + resul.erro + 
										"\nAdicionando: " + row["NOME"] + 
										"\nAo Aparelho:\n" + noapal;
					alert( txt );
					}
				idfltr = resul.id;
				//	adiciona o funcionário no aparelho
				var body =	"[ { \"nome\": \"" + row["NOME"] +
						"\", \"verifica_biometria\": true, " +
						"\"referencias\": [ " + row["SSHD"].substring(1) +  " ]}]";
				var resul = repserviceB( "POST", "usuarios", idapal, "SISPONTO", null, body );
				var aux;
				var flerr = false;
				if( resul.hasOwnProperty('status') )
					{
					if( resul.status != "OK" )
						{
						if( !(aux.indexOf("000") >= 0 || aux.indexOf("023") >= 0 ) )
							{
							var url = "partes/updates.php?query=delfltr&idfltr="+idfltr;
							var resul = remoto( url );
							var txt = "Por favor, anote\nO Funcionário: " + row["NOME"] + 
												"\nNão pode ser adicionado ao aparelho";
							aux = "Por favor, anote\nO Funcionário: " + row["NOME"] + 
												"\nNão pode ser adicionado ao aparelho:\n";
							//alert( aux );
							flerr = true;
							}
						}
					if( !flerr )
						{
						Table.fnUpdate( "sim", ix, 4, false );
						Table.fnUpdate( "", ix, 6 );
						}
					}
				}
			}

		function adiciona( ix )
			{
			var act = "<a href='#' onClick='javascript:remove(" + ix + ")' " +
									"class='btn btn-circle btn-info btn-xs adicionar' " +
									"title=\"remover funcionário do aparelho\" >" +
									"   <i class='glyphicon glyphicon-remove'></i></a>"
			Table.fnUpdate( "sim", ix, 5, false );
			Table.fnUpdate( act, ix, 6 );
			flalt = true;
			}
		
			
		function remove( ix )
			{
			var act = "<a href='#' onClick='javascript:adiciona(" + ix + ")' " +
									"class='btn btn-circle btn-info btn-xs adicionar' " +
									"title=\"adicionar funcionário ao aparelho\" >" +
									"   <i class='glyphicon glyphicon-ok'></i></a>"
			Table.fnUpdate( "nao", ix, 5, false );
			Table.fnUpdate( act, ix, 6 );
			flalt = true;
			}
			
		function escapar( tipo, id, text )
			{
			if( flalt )
				{
				var txt = "Foram efetuadas modificações na tela.\n" +
						"Se você prosseguir clicando em OK, \n" +
						"elas serão perdidas. \n" +
						"Para mantê-las pressione CANCELAR e " +
						"em seguida pressione Salvar todas as modificações."
				var res = confirm( txt );
				if( !res )
					{
					$(".aparelhos").select2( 'val', idapalant );
					return;
					}
				}
			if( id <= 0 )
				return;
			//	
			var url	=	"partes/tableData.php?query=funcapar&idapal=" + id;
			var	resu	=	remoto( url );
			if( resu == null )
				{
				alert( "Falha na obtenção dos dados. Por Favor, tente mais tarde");
				$(".aparelhos").select2( 'val', idapalant );
				return;
				}
			idapal	=	id;
			noapal	=	text;
			idapalant = idapal;
			noapalant = noapal;
			flmod = false;
			var qt = resu.data.length;
			tableClear();
			var data = [];
			if( qt > 0 )
				{
				for( var ix=0; ix<qt; ix++ )
					{
					var act = "<a href='#' onClick='javascript:remove(" + ix + ")' " +
									"class='btn btn-circle btn-info btn-xs remover' " +
										"title=\"remover do aparelho\" >" +
										"   <i class='glyphicon glyphicon-remove'></i></a>";
					data.push({	
										"FUNIID": resu.data[ix].FUNI_ID,
										"SSHD": resu.data[ix].SSHD,
										"NOME": resu.data[ix].NOME,
										"BIOS": resu.data[ix].BIOS,
										"UORPONTO": resu.data[ix].SIGLAUORPONTO,
										"PRESENTE": "sim",
										"MANTER": "sim",
										"action": " "
										} );
					}
				Table.fnAddData( data, true );
				}
			else
				{
				data.push(	{	
										"FUNIID": "",
										"SSHD": "",
										"NOME": "Não há dados a exibir",
										"BIOS": "",
										"UORPONTO": "",
										"PRESENTE": "",
										"MANTER": "",
										"action": " "
										} );

				Table.fnAddData( data, true );
				}
			}

		function escuor( tipo, id, text )
			{
			if( idapal < 1 )
				{
				alert( "Por favor, escolha um dos aparelhos." );
				return;
				}
			if( id > 0 )
				{
				iduor	=	id;
				nouor	=	text;
				}
			var url	=	"partes/tableData.php?query=fuornapar&idapal=" + idapal +
									"&fuor=" + iduor;
			var	resu	=	remoto( url );
			if( resu == null )
				{
				alert( "Falha na obtenção dos dados. Por Favor, tente mais tarde")
				return;
				}
			$(".uors").select2( 'val', 0 );
			var qt = resu.data.length;
			var data = [];
			if( qt > 0 )
				{
				for( var ix=0; ix<qt; ix++ )
					{
					var act = "<a href='#' onClick='javascript:remove(" + ix + ")' " +
									"class='btn btn-circle btn-info btn-xs remover' " +
										"title=\"remover do aparelho\" >" +
										"   <i class='glyphicon glyphicon-remove'></i></a>";
					data.push({	
										"FUNIID": resu.data[ix].FUNI_ID,
										"SSHD": resu.data[ix].SSHD,
										"NOME": resu.data[ix].NOME,
										"BIOS": resu.data[ix].QTBIO,
										"UORPONTO": resu.data[ix].SIGLAUORSAU,
										"PRESENTE": "não",
										"MANTER": "sim",
										"action": act
										} );
					}
				var lis = Table.fnAddData( data, false );
				for( var ix=0; ix < lis.length; ix++ )
					{
					var act = "<a href='#' onClick='javascript:remove(" + lis[ix] + ")' " +
									"class='btn btn-circle btn-info btn-xs remover' " +
										"title=\"remover do aparelho\" >" +
										"   <i class='glyphicon glyphicon-remove'></i></a>";
					Table.fnUpdate( act, lis[ix], 6, act, false );
					}
				Table.fnDraw( true );
				}			
			else
				alert( "Nenhum funcionário foi adiciona à lista" );
			}

		function escpess( tipo, id, text )
			{
			if( id > 0 )
				{
				idpess	=	id;
				nopess	= text;
				}
			}

		var url = "selectData.php?query=aparelhos";
		SelInit( ".aparelhos", url, 0, "Escolha abaixo", escapar, 0 );

		url = "selectData.php?query=uor";
		SelInit( ".uors", url, 0, "Escolha abaixo", escuor, 0 );

		var url = "selectData.php?query=funcfuni";
		SelInit( ".pess", url, 0, "Escolha abaixo", escpess, 0 );

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
			
		/////////////// PRINCIPAL ////////////////////////
		var idapalant = 0;
		var noapalant = "Escolha abaixo";
		var idapal = -1;
		var iduor = -1;
		var idpess = -1;
		var noapal = -1;
		var nouor = -1;
		var nopess = -1;
		var flalt = false;				//	indica que houve alterações

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
						"SSHD": "",
						"NOME": "",
						"REGIME": "",
						"UORPONTO": "",
						"UORSAU": "",
						"PRESENTE": "",
						"MANTER": ""
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
			"editavel": false,
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
			"tipo": "t",
			"editavel": false,
			"vanovo": "",
			"width": "10%",
			"aTargets": [ ++col ],
			"mData": "BIOS",
			"sTitle":"Biometrias",
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
			"mData": "UORPONTO",
			"sTitle":"UOR Alocado",
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
			"mData": "PRESENTE",
			"sTitle":"Presente",
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
			"mData": "MANTER",
			"sTitle":"Manter",
			"defaultContent": " "
			};
		colDefs.push( aux );

		aux	=	
			{
			"tipo": null,
			"editavel": false,
			"vanovo": "",
			"bSortable": false,
			"searchable": false,
			"aTargets": [ ++col ],
			"orderable":false,
			"mData": "action",
			"width": "5%",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		
		///////////////////////////////////////////////////////////////////////

		
		AjaxSource	=	"";
		inicializa.init();
		var data = [];
		data.push(	{	
								"FUNIID": "",
								"SSHD": "",
								"NOME": "Escolha um dos aparelhos de ponto",
								"BIOS": "",
								"UORPONTO": "",
								"PRESENTE": "",
								"MANTER": "",
								"action": " "
								} );

		Table.fnAddData( data, true );
		flalt = false;
				
		</script>
	</body>	
</html>
