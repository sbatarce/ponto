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
						<div class='row linha' style='margin-top: 10px; margin-left:30px;'>
							<h5>Escolha a nova UOR </h5>
							<input style='width:90%;' class='input-small uors' 
										 id="seluor" title="Escolha a UOR da nova alocação do funcionário"/>
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
    <div id="aparmodal" class="modal fade bs-modal-sm" role="dialog"
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

		function setAjax( del )
			{
			//
			if( del != 0 )
				tableDestroy();
			AjaxSource	=	"partes/tableData.php?query=autcadas&sshd=" + sshd;
			inicializa.init();
			}
			
		//	rotinas de manipulação dos aparelhos
		function adicionaSSHD()
			{
			var body =	"[ { \"nome\": \"" + nofunc +
									"\", \"verifica_biometria\": true, " +
									"\"referencias\": [ " + sshd.substring(1) +  " ]}]";
			var resul = repserviceB( "POST", "usuarios", idapal, "SISPONTO", null, body );
			var aux = resul.erro;
			if( aux.indexOf("023") >= 0 )
				{
				alert( "Atenção: este funcionário já se encontra neste aparelho" );
				return false;
				}
			if( aux.indexOf("000") >= 0 )
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
			
		function removeSSHD( sshd )
			{
			
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
			var chl	=	obterChild();
			chl.child( FormataChild( chl.data() ) );
			atuatab( false );
			}

		function trocaBase( idfuni )
			{
			idfunc = idfuni;
			$("#basemodal").modal('show');
			}
			
		function baseOK()
			{
			$("#basemodal").modal('hide');
			}
			
		function adicApar( idfuni, shd, nome, apal )
			{
			idfunc = idfuni;
			idapar = -1;
			sshd = shd;
			nofunc = nome;
			idapal = -1;
			
			$("#aparmodal").modal('show');
			}
			
		function adicAparOK()
			{
			if( idapal == -1 )
				{
				alert( "Por favor, escolha um aparelho" );
				return;
				}
			//
			if( adicionaSSHD( idapar, sshd ) )
				{
				$("#aparmodal").modal('hide');
//				var chl	=	obterChild();
//				chl.child( FormataChild( chl.data() ) );
				atuatab( false );
				}
			}
			
		function aparOK()
			{
			
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
/////////////// PRINCIPAL ////////////////////////
		var idfuni = -1;
		var idapar = -1;
		var uoralo = null;
		var uorant = null;
		
		var sshd, nofunc, idapal;
		
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
		//$("#eddt_new").hide();																		//	esconde o adicionar
		var notabel			=	"BIOMETRIA.FUNI_FUNCIONARIO";						//	nome da tabela base
		var	nocmpid			=	"FUNI_ID";															//	nome do campo ID da tabela base
		var sequence		= "BIOMETRIA.SQ_FUNI";
		var liNova			=
						{
						"UNIDADE": "",
						"SSHD": "",
						"NOME": ""
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
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
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
			"aTargets": [ ++col ],
			"mData": "SSHD",
			"sTitle":"SSHD",
			"defaultContent": " "
			};
		colDefs.push( aux );
		
		aux	=
			{
			"tipo": "x",
			"editavel": false,
			"vanovo": "",
			"aTargets": [ ++col ],
			"mData": "NOFUNC",
			"sTitle":"Servidor/Funcionário",
			"defaultContent": " "
			};
		colDefs.push( aux );
		/*
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
			"defaultContent": acremove
			};
		colDefs.push( aux );
		*/
		//
		//
	
		function completaChild( original )
			{
			return " ";
			}

		function FormataChild( original )
			{
			var lin = "",
					ret = "";
			var aux, cls;
			
			//	UOR de alocação
			cls = "clsuor";
			lin = IniLinha( cls );
			aux	=	
				{
				titulo: "uor",
				nocmp: "UOR",
				width: "20%",
				valor: original.UNIDADE,
				divclass: "col-xs-2",
				inpclass: cls,
				extra: "readonly"
				};
			lin	+=	CampoTexto( aux );

			lin	+=	"<a style='margin-left: 10px; margin-top: 10px;' " +
							"href='javascript:trocaUOR( " + original.IDFUNI + " )' " +
							"class='btn btn-circle btn-info btn-xs ' " +
							"title=\"Troca a UOR do funcionário\" >" +
							"<i class='glyphicon glyphicon-random'></i></a>";
			lin	+=	FimLinha();

			ret += lin;
			
			//	regime do funcionário
			var url	=	"partes/queries.php?query=obtregimefunc&funiid=" + original.IDFUNI;
			var	resu	=	remoto( url );
			if( resu.linhas > 0 )
				{
				cls = "clsreg";
				lin = IniLinha( cls );
				aux	=	
					{
					titulo: "Regime atual",
					nocmp: "REGIME",
					width: "30%",
					valor: resu.dados[0].NOREG,
					divclass: "col-xs-2",
					inpclass: cls,
					extra: "readonly"
					};
				lin	+=	CampoTexto( aux );
				
				lin	+=	"<a style='margin-left: 10px; margin-top: 10px;' " +
								"href='javascript:trocaReg( " + original.IDFUNI + " )' " +
								"class='btn btn-circle btn-info btn-xs ' " +
								"title=\"Troca o regime do funcionário\" >" +
								"<i class='glyphicon glyphicon-random'></i></a>";

				lin	+=	FimLinha();
				}
			ret += lin;
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
										"href='javascript:trocaBase( " + original.IDFUNI + ", \"" +
										original.SSHD + "\" )' " +
										"class='btn btn-circle btn-info btn-xs ' " +
										"title=\"Troca o aparelho base do funcionário\" >" +
										"<i class='glyphicon glyphicon-random'></i></a>";
						}
					else
						{
						lin	+=	"<a style='margin-left: 10px; ' " +
										"href='javascript:removeApar( " + original.IDFUNI + " )' " +
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

		setAjax( 0 );
		var handler = function() 
			{
				alert( "passou aqui" );
			};
		$('#eddt_new').unbind( "click" );
		$('#eddt_new').bind( "click", handler );
		
		</script>
	</body>	
</html>
