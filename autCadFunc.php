<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Ponto</title>
		<?php
		include 'partes/Head.php';
		?>
  </head>

  <body onload="javascript:Titulo( '<h4>Alocação de funcionários</h4>' );">
  <img src="imagens/carrega.gif" id="carrega" style="display:none" />
    <!-- Conteúdo específico da página -->
<?php
include 'partes/Menu.php';
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
		<!--======================================================================-->
		<!-- modal de troca de UOR -->
    <div id="uormodal" class="modal fade bs-modal-sm" role="dialog"
				 aria-labelledby="mySmallModalLabel" aria-hidden="true"
				 style="width: 500px; max-width: 500px;" >
			<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"
										onclick="javascript:cancelUOR()">
							&times;
						</button>
						<h4 class="modal-title">Alteração de UOR</h4>
					</div>
					<div class="modal-body" id="bdymodal" ng>
						<h5 id="xuornome"></h5>
						<h5 id="xuorfech"></h5>
						<h5 id="xuoratua"></h5>
						<h5 id="xuornova"></h5>
						<div class='row linha' style='margin-top: 10px; margin-left:30px;'>
							<h5>Data em que será efetivada a alocação.</h5>
							<h5>A alocação anterior (se houver) se encerrará.</h5>
							<input id="xuordata" class='input-small inp' style='width: 40%; ' 
										 title="data da transferencia de UOR" readonly />
						</div>
					</div>
					<div class="modal-footer">
						<center>
							<button type="button" class="btn btn-primary"
											onclick="javascript:uorOK()">
											OK
							</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						</center>
					</div>
				</div>
			</div>
		</div>
		<!--======================================================================-->
		<!-- modal de alteração do aparelho BASE -->
    <div id="basemodal" class="modal fade bs-modal-sm" role="dialog"
				 aria-labelledby="mySmallModalLabel" aria-hidden="true"
				 style="width: 500px; max-width: 500px;" >
			<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"
										onclick="javascript:cancel()">
							&times;
						</button>
						<h4 id="titTrocBase" class="modal-title">Troca do aparelho base</h4>
					</div>
					<div class="modal-body" id="bdymodal" ng>
						<div class='row linha' style='margin-top: 10px; margin-left:30px;'>
							<h5>Escolha o novo aparelho BASE do funcionário </h5>
							<h5>O funcionário será removido do aparelho BASE atual</h5>
							<input style='width:90%;' class='input-small lsapar' 
										 id="selbase" title="Escolha um aparelho"/>
						</div>
					</div>
					<div class="modal-footer">
						<center>
							<button type="button" class="btn btn-primary"
											onclick="javascript:baseOK()">
											OK
							</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						</center>
					</div>
				</div>
			</div>
		</div>
		<!--======================================================================-->
		<!-- modal de adição de aparelho -->
    <div id="adicaparmodal" class="modal fade bs-modal-sm" role="dialog"
				 aria-labelledby="mySmallModalLabel" aria-hidden="true"
				 style="width: 500px; max-width: 500px;" >
			<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"
										onclick="javascript:cancel()">
							&times;
						</button>
						<h4 id="titAdicApar" class="modal-title">Troca do aparelho base</h4>
					</div>
					<div class="modal-body" id="bdymodal" ng>
						<div class='row linha' style='margin-top: 10px; margin-left:30px;'>
							<h5>Escolha o aparelho ao qual o funcionário será adicionado</h5>
							<input style='width:90%;' class='input-small lsapar' 
										 id="selapar" title="Escolha um aparelho"/>
						</div>
					</div>
					<div class="modal-footer">
						<center>
							<button type="button" class="btn btn-primary"
											onclick="javascript:adicAparOK()">
											OK
							</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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

		//	rotinas de manipulação dos aparelhos
		//	remove o funcionario de um aparelho (apal) e insere em outro (apalnovo)
		function trocaSSHD( idfltr, sshd, nome, apal, apalnovo )
			{
			//	insere no aparelho novo
			var body =	"[ { \"nome\": \"" + nome +
									"\", \"verifica_biometria\": true, " +
									"\"referencias\": [ " + sshd.substring(1) +  " ]}]";
			var resul = repserviceB( "POST", "usuarios", apalnovo, "SISPONTO", null, body );
			var aux = resul.erro;
			if( aux.indexOf("000") < 0 && aux.indexOf("023") < 0 )
				{
				alert( "Erro inserindo funcionário no novo Local de Trabalho" + resul.erro );
				return;
				}
			//	remove do aparelho atual
			var funcao = "usuarios/" + sshd.substring(1);
			var resul = repserviceB( "DELETE", funcao, apal, "SISPONTO", null, null );
			var aux = resul.erro;
			//	000 -> OK
			//	022 -> não há este usuário no aparelho
			if( aux.indexOf("000") < 0 && aux.indexOf("022") < 0 )
				{
				alert( "Erro removendo funcionário do Local de Trabalho" + resul.erro );
				return false;
				}
			//	troca no banco
			var url = "partes/trocaFLTR.php?fltrid="+ idfltr + 
								"&apalid=" + apal;
			var resul = remoto( url );
			if( resul.status == "OK" )
				{
				alert( "OK: alterado" );
				$("#basemodal").modal('hide');
				return true;
				}
			else
				{
				alert( "Erro removendo funcionário do Local de Trabalho" + resul.erro );
				return false
				}
			}
		//	adiciona um SSHD a um aparelho & atualiza FLTR
		function adicionaSSHD()
			{
			var body =	"[ { \"nome\": \"" + nofunc +
									"\", \"verifica_biometria\": true, " +
									"\"referencias\": [ " + sshd.substring(1) +  " ]}]";
			var resul = repserviceB( "POST", "usuarios", idapal, "SISPONTO", null, body );
			var aux = resul.erro;
			if( aux.indexOf("000") >= 0 || aux.indexOf("023") >= 0 )
				{
				var url = "partes/adicionaFLTR.php?funiid="+ idfunc + 
									"&apalid="+idapal;
				var resul = remoto( url );
				if( resul.status == "OK" )
					{
					alert( "OK: UOR atualizada." );
					}
				else
					{
					alert( "Erro adicionando Local de Trabalho ao funcionário " + resul.erro );
					return null;
					}
				return true;
				}
			alert( "Erro: " + aux );
			return false;
			}
		//	remove um SSHD de um aparelho e o FLTR associado
		function removeSSHD( apalid )
			{
			var funcao = "usuarios/" + sshd.substring(1);
			var resul = repserviceB( "DELETE", funcao, apalid, "SISPONTO", null, null );
			var aux = resul.erro;
			//	000 -> OK
			//	022 -> não há este usuário no aparelho
			if( aux.indexOf("000") >= 0 || aux.indexOf("022") >= 0 )
				{
				var url = "partes/removeFLTR.php?funiid="+ idfunc + 
									"&apalid="+idapal;
				var resul = remoto( url );
				if( resul.status == "OK" )
					{
					alert( "OK: Removido" );
					}
				else
					{
					alert( "Erro removendo funcionário do Local de Trabalho" + resul.erro );
					return null;
					}
				return true;
				}
			alert( "Erro: " + aux );
			return false;
			}
			
		//	funções de chamada e retorno dos modais
		function trocaUOR( idfuni )
			{
			uorant = uoralo;
			idfunc = idfuni;
			$("#uormodal").modal('show');
			}
			
		function uorOK()
			{
			if( uorant == uoralo )
				return;
			var url = "partes/trocaUorFunc.php?funiid="+ idfunc + 
								"&uornova="+uoralo;
			var resul = remoto( url );
			if( resul.status == "OK" )
				{
				alert( "OK: UOR atualizada." );
				}
			else
				{
				alert( "Erro trocando URL " + resul.erro );
				return null;
				}
			$("#uormodal").modal('hide');
			atuatab( false );
			}
										
		var sshd, nofunc, idapal;
		var idfltr, idapalant;
		function trocaBase( idfuni, shd, nome, apal, fltr )
			{
			idfunc = idfuni;
			sshd = shd;
			nofunc =nome;
			idapalant = apal;
			idfltr = fltr;

			idapal = -1
			
			$("#basemodal").modal('show');
			}
			
		function baseOK()
			{
			if( idapal == -1 )
				{
				alert( "Por favor, escolha um aparelho" );
				return;
				}
			if( trocaSSHD( idfltr, sshd, nofunc, idapalant, idapal ) )
				{
				atuatab( false );
				}
			}
			
		function adicApar( idfuni, shd, nome, apal )
			{
			idfunc = idfuni;
			idapar = -1;
			sshd = shd;
			nofunc = nome;
			idapal = -1;
			
			$("#adicaparmodal").modal('show');
			}
			
		function adicAparOK()
			{
			if( idapal == -1 )
				{
				alert( "Por favor, escolha um aparelho" );
				return;
				}
			//
			if( adicionaSSHD( ) )
				{
				$("#adicaparmodal").modal('hide');
				atuatab( false );
				}
			}
			
		function removeApar( idfuni, shd, apal )
			{
			idfunc = idfuni;
			sshd = shd;
			idapal = apal;
			
			if( removeSSHD( idapal ) )
				{
				$("#adicaparmodal").modal('hide');
				atuatab( false );
				}
			}
			
		function escouor( tipo, id )
			{
			if( id > 0 )
				uoralo	=	id;
			}

		function escapar( tipo, id )
			{
			if( id > 0 )
				idapal	=	id;
			}

		function setAjax()
			{
			tableDestroy();
			AjaxSource	=	"partes/tableData.php?query=autcadas&sshd=" + sshd;
			inicializa.init();
			}
			
/////////////// PRINCIPAL ////////////////////////
		var idfuni = -1;
		var idapar = -1;
		var uoralo = null;
		var uorant = null;
		
		//	variáveis e rotinas de atualização
		var ixedt = -1;			//	row sendo editada
		var iduor = -1;			//	última uor escolhida
		var idreg = -1;			//	último regime escolhido
		var dtxuor = "";		//	data do fechamento escolhida
		
		
		var sshd, nofunc, idapal;
		var idfltr, idapalant;
		
		$( "#xuordata" ).datepicker(
			{
			dateFormat: "dd/mm/yy",
			altFormat: "yymmdd",
			startView: 2,
			todayBtn: true,
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			todayHighlight: true			
			})
			.on('change.dp', function(e)
				{ 
				var dt = $("#dtcor").datepicker("getDate");
				dtxuor = $.datepicker.formatDate("yymmdd", dt );
				$("#dtcor").datetimepicker('hide');
				});

		var url = "selectData.php?query=uor";
		SelInit( ".uors", url, 0, "Escolha abaixo", escouor, 2 );

		url = "selectData.php?query=aparelhos";
		SelInit( ".lsapar", url, 0, "Escolha abaixo", escapar, 0 );

		var sshd = obterCookie( "user" );
		if( sshd == null )
			{
			Deslogar();
			}
		//	formatadores ligados ao datatables
		//		ticamp:		tipo de campo t/n/l = texto, numÃ©rico ou legenda
		//		inputs:		nomes dos campos no banco relativamente aos inputs
		//		origem:		Ã­ndice da coluna do Datatables que atualiza o campo no banco
		//		editael:	indica se a coluna do datatable Ã© editÃ¡vel
		var acremove = "<a href='#' class='btn btn-circle btn-danger btn-xs delete' " +
										"data-mode='alt' title=\"remover funcionário da UOR\" > " +
										"<i class='typcn typcn-delete-outline'></i></a>";
								
		//$("#eddt_new").hide();																	//	esconde o adicionar
		var notabel			=	"BIOMETRIA.FUNI_FUNCIONARIO";						//	nome da tabela base
		var	nocmpid			=	"FUNI_ID";															//	nome do campo ID da tabela base
		var sequence		= "BIOMETRIA.SQ_FUNI";
		var liNova			=
						{
						"SSHD": "",
						"NOME": "",
						"REGIME": "",
						"UNIDADE": ""
						};
		var	order	=	[];						//	ordem de apresentação
		//	prepara a definição das colunas
		var colDefs	=	[];
		var	col	=	-1;
		var aux	=
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
			"defaultContent": acshow
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "t",
			"editavel": false,
			"vanovo": "",
			"aTargets": [ ++col ],
			"mData": "SSHD",
			"sTitle":"SSHD",
			"width": "15%",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "t",
			"editavel": false,
			"vanovo": "",
			"aTargets": [ ++col ],
			"mData": "NOFUNC",
			"sTitle":"Servidor/Funcionário",
			"width": "25%",
			"defaultContent": " "
			};
		colDefs.push( aux );

		aux	=
			{
			"tipo": "t",
			"editavel": false,
			"vanovo": "",
			"aTargets": [ ++col ],
			"mData": "DTFECHA",
			"sTitle":"Fechamento",
			"width": "10%",
			"defaultContent": " "
			};
		colDefs.push( aux );

		aux	=
			{
			"tipo": "l",
			"editavel": true,
			"vanovo": "",
			"aTargets": [ ++col ],
			"mData": "UNIDADE",
			"sTitle":"Unidade Lotação",
			"selID": "IDLOTADO",
			"classe": "cbuors",
			"selVal": "UNIDADE",
			"selminlen": 0,
			"selURL": "selectData.php?query=uor",
			"funcEscolha": escUor,
			"width": "15%",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "l",
			"editavel": true,
			"vanovo": "",
			"aTargets": [ ++col ],
			"mData": "NOREG",
			"sTitle":"Regime",
			"selID": "IDREG",
			"classe": "cbregi",
			"selVal": "NOREG",
			"selminlen": 0,
			"selURL": "selectData.php?query=regimes",
			"funcEscolha": escRegi,
			"width": "15%",
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
			"mData":null,
			"width": "10%",
			"defaultContent": acsoalt
			};
		colDefs.push( aux );
		
		function escRegi( tipo, id )
			{
			if( id >= 0 )
				idreg	=	id;
			}

		function escUor( tipo, id, text )
			{
			if( id >= 0 )
				{
				iduor	=	id;
				let aData = Table.fnGetData(ixedt);
				let aux = `Funcionário: ${aData.NOFUNC}`;
				$('#xuornome').html(aux)
				aux = `Data Fechamento: ${aData.DTFECHA}`;
				$('#xuorfech').html(aux)
				aux = `Uor Atual: ${aData.UNIDADE}`;
				$('#xuoratua').html(aux)
				aux = `Nova Uor: ${text}`;
				$('#xuornova').html(aux)
				$('#xuordata').val( aData.DTFECHA );
				$("#uormodal").modal('show');
				}
			}

		function deleteRow( oTable, nRow )
			{
			}
		function restoreRow( oTable, nRow )
			{
			restoreRowG( oTable, nRow );
			ixedt = -1;
			}
		function editRow( oTable, nRow )
			{
			ixedt = nRow;
			iduor = -1;
			idreg = -1;
			editRowG( oTable, nRow );
			}
		function saveRow( oTable, nRow )
			{
			var sql = "";
			var aData = oTable.fnGetData(nRow);
			if( iduor <= 0 && idreg <= 0 )
				return;
			
			if( iduor > 0 )
				{
				var url = "partes/trocaUorFunc.php?funiid="+ aData["IDFUNI"] + 
									"&uornova="+iduor;
				var resul = remoto( url );
				if( resul.status == "OK" )
					{
					atuatab( false );
					}
				else
					{
					alert( "Erro trocando URL " + resul.erro );
					return null;
					}
				}
				
			if( idreg > 0 )
				{
				var url = "partes/trocaRegiFunc.php?funiid="+ aData["IDFUNI"] + 
									"&reginovo="+idreg;
				var resul = remoto( url );
				if( resul.status == "OK" )
					{
					atuatab( false );
					}
				else
					{
					alert( "Erro trocando URL " + resul.erro );
					return null;
					}
				}
			//saveRowG( oTable, nRow, idnovo );
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
			var lin = "",
					ret = "";
			var aux, cls;
			
			//	aparelhos do funcionário
			var url	=	"partes/queries.php?query=obtaparelhosfunc&funiid=" + original.IDFUNI;
			var	resu	=	remoto( url );
			if( resu.linhas > 0 )
				{
				cls = "clsapa";
				lin = IniLinha( cls );
				lin += "<span style='margin-left: 10px'>Aparelho(s) do funcionário/Servidor</span>";
				lin	+=	"<a style='margin-left: 10px; ' " +
								"href='javascript:adicApar( " + 
								original.IDFUNI + ", \"" + original.SSHD + "\", \"" + 
								original.NOFUNC + "\", " + resu.dados[0].IDAPAL +  " )' " +
								"class='btn btn-circle btn-info btn-xs ' " +
								"title=\"Adiciona o funcionário a um aparelho que não o base\" >" +
								"<i class='glyphicon glyphicon-plus'></i></a>";
				lin += FimLinha();
				ret += lin;
				for( var ix=0; ix<resu.linhas; ix++ )
					{
					lin = IniLinha( cls );
					aux	=	
						{
						titulo: "",
						nocmp: "EHBASE",
						width: "10%",
						valor: "",
						divclass: "col-xs-2",
						inpclass: cls,
						extra: "readonly"
						};
					if( resu.dados[ix].EHBASE == "1" )
						aux.valor = "Base";
					else
						aux.valor = "Adicional";
					lin	+=	CampoTexto( aux );

					aux	=	
						{
						titulo: "",
						nocmp: "UNIAPAR",
						width: "10%",
						valor: resu.dados[ix].UNIAPAR,
						divclass: "col-xs-2",
						inpclass: cls,
						extra: "readonly"
						};
					lin	+=	CampoTexto( aux );

					aux	=	
						{
						titulo: "",
						nocmp: "LOCAAPAR",
						width: "25%",
						valor: resu.dados[ix].LOCAAPAR,
						divclass: "col-xs-2",
						inpclass: cls,
						extra: "readonly"
						};
					lin	+=	CampoTexto( aux );

					if( resu.dados[ix].EHBASE == "1" )
						{
						lin	+=	"<a style='margin-left: 10px; ' " +
										"href='javascript:trocaBase( " + 
										original.IDFUNI + ", \"" + original.SSHD + "\", \"" + 
										original.NOFUNC + "\", " + 
										resu.dados[ix].IDAPAL + ", " + resu.dados[ix].IDFLTR +  " )' " +
										"class='btn btn-circle btn-info btn-xs ' " +
										"title=\"Troca o aparelho base do funcionário\" >" +
										"<i class='glyphicon glyphicon-random'></i></a>";
						}
					else
						{
						lin	+=	"<a style='margin-left: 10px; ' " +
										"href='javascript:removeApar( " + 
										original.IDFUNI + ", \"" + original.SSHD + "\", " + 
										resu.dados[ix].IDAPAL +  " )' " +
										"class='btn btn-circle btn-info btn-xs ' " +
										"title=\"Troca o regime do funcionário\" >" +
										"<i class='glyphicon glyphicon-minus'></i></a>";
						}

					lin	+=	FimLinha();
					ret += lin;
					}
				}
			return ret;
			}
		///////////////////////////////////////////////////////////////////////

		$("#eddt_new").hide();
		setAjax();
		/*
		var handler = function() 
			{
				alert( "passou aqui" );
			};
		$('#eddt_new').unbind( "click" );
		$('#eddt_new').bind( "click", handler );
		*/
		</script>
	</body>	
</html>
