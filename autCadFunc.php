<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Ponto</title>
		<?php
		include 'partes/Head.php';
		?>
  </head>

  <body onload="javascript:Titulo( '<h4>Alocação de funcionários</h4>' );">
<?php
include 'partes/MenuPri.php';
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
		<!-- modal de troca de REGIME -->
    <div id="infomodal" class="modal fade bs-modal-sm" role="dialog"
				 aria-labelledby="mySmallModalLabel" aria-hidden="true"
				 style="width: 500px; max-width: 500px;" >
			<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"
										onclick="javascript:Cancel()">
							&times;
						</button>
						<h4 class="modal-title">Alteração de Regime de trabalho</h4>
					</div>
					<div class="modal-body" id="bdymodal" ng>
						<h5 id="info">
							A mudança de regime somente será permitida na data seguinte à 
							<b>data do último processamento</b>.<br>
							Necessariamente deverá haver um <b>fechamento do funcionário</b>
							na <b>data do último processamento</b> ou a mudança de regime 
							não será efetuada.<br>
							Caso isto ocorra, efetue o fechamento do funcionário na 
							<b>data do último processamento</b> e volte para modificar 
							o regime.
						</h5>
					</div>
					<div class="modal-footer">
						<center>
							<button type="button" class="btn btn-default" data-dismiss="modal"
											onclick="javascript:Cancel()">OK</button>
						</center>
					</div>
				</div>
			</div>
		</div>
		<!--======================================================================-->
		<!-- modal de troca de UOR -->
    <div id="uormodal" class="modal fade bs-modal-sm" role="dialog"
				 aria-labelledby="mySmallModalLabel" aria-hidden="true"
				 style="width: 500px; max-width: 500px;" >
			<div class="modal-dialog modal-sm" style="width: 500px; max-width: 500px;">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"
										onclick="javascript:Cancel()">
							&times;
						</button>
						<h4 class="modal-title">Alteração de UOR</h4>
					</div>
					<div class="modal-body" id="bdymodal" ng>
						<h5 id="xuornome"></h5>
						<h5 id="xuorfech"></h5>
						<h5 id="xuoratua"></h5>
						<h5 id="xuornova"></h5>
						<h5>
							O início de alocação do funcionário na UOR ocorrerá na 
							<b>data de efetivação</b> abaixo, que é o dia seguinte ao dia 
							do fechamento deste funcionário.<br>
							A partir da data de efetivação para o futuro, não deve haver 
							interferências de autorização, isto é, justificativas 
							<b>aceitas ou negadas</b>, sendo que neste período, pode haver 
							pendências e justificativas em análise que serão resolvidas 
							oportunamente pelo autorizador da nova UOR.<br>
							Caso haja alguma alocação a uma UOR anterior, e cumpridas as 
							exigências acima, esta será encerrada com data anterior 
							à de efetivação.<br>
						</h5>
						<h3 id="efetuor">efetivar em:</h3>
					</div>
					<div class="modal-footer">
						<center>
							<button id="btok" type="button" class="btn btn-primary"
											onclick="javascript:uorOK()">
											OK
							</button>
							<button type="button" class="btn btn-default" data-dismiss="modal"
											onclick="javascript:Cancel()">Cancel</button>
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
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
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
										onclick="javascript:Cancel()">
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
			iduor = -1;
			editRowG( oTable, nRow );
			ixedt = nRow;
			let aData = oTable.fnGetData(nRow);			
			iduorant = aData.IDLOTADO;
			idfunc = aData.IDFUNI;
			idreg = aData.IDREG;
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
				var resu = remoto( url );
				if( resu.status == "OK" )
					{
					atuatab( false );
					}
				else
					{
					alert( "Erro trocando URL " + resu.erro );
					return null;
					}
				}
				
			if( idreg > 0 )
				{
				var url = "partes/trocaRegiFunc.php?funiid="+ aData["IDFUNI"] + 
									"&reginovo="+idreg;
				var resu = remoto( url );
				if( resu.status == "OK" )
					{
					atuatab( false );
					}
				else
					{
					alert( "Erro trocando URL " + resu.erro );
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
		//	rotinas de manipulação dos aparelhos
		//	remove o funcionario de um aparelho (apal) e insere em outro (apalnovo)
		function trocaSSHD( idfltr, sshd, nome, apal, apalnovo )
			{
			//	insere no aparelho novo
			var body =	"[ { \"nome\": \"" + nome +
									"\", \"verifica_biometria\": true, " +
									"\"referencias\": [ " + sshd.substring(1) +  " ]}]";
			let resu = repserviceB( "POST", "usuarios", apalnovo, "SISPONTO", null, body );
			let aux = resu.erro;
			if( aux.indexOf("000") < 0 && aux.indexOf("023") < 0 )
				{
				alert( "Erro inserindo funcionário no novo Local de Trabalho" + resu.erro );
				return;
				}
			//	remove do aparelho atual
			var funcao = "usuarios/" + sshd.substring(1);
			var resu = repserviceB( "DELETE", funcao, apal, "SISPONTO", null, null );
			var aux = resu.erro;
			//	000 -> OK
			//	022 -> não há este usuário no aparelho
			if( aux.indexOf("000") < 0 && aux.indexOf("022") < 0 )
				{
				alert( "Erro removendo funcionário do Local de Trabalho" + resu.erro );
				return false;
				}
			//	troca no banco
			var url = "partes/trocaFLTR.php?fltrid="+ idfltr + 
								"&apalid=" + apal;
			var resu = remoto( url );
			if( resu.status == "OK" )
				{
				alert( "OK: alterado" );
				$("#basemodal").modal('hide');
				return true;
				}
			else
				{
				alert( "Erro removendo funcionário do Local de Trabalho" + resu.erro );
				return false
				}
			}
		//	adiciona um SSHD a um aparelho & atualiza FLTR
		function adicionaSSHD()
			{
			var body =	"[ { \"nome\": \"" + nofunc +
									"\", \"verifica_biometria\": true, " +
									"\"referencias\": [ " + sshd.substring(1) +  " ]}]";
			let resu = repserviceB( "POST", "usuarios", idapal, "SISPONTO", null, body );
			let aux = resu.erro;
			if( aux.indexOf("000") >= 0 || aux.indexOf("023") >= 0 )
				{
				var url = "partes/adicionaFLTR.php?funiid="+ idfunc + 
									"&apalid="+idapal;
				let resu = remoto( url );
				if( resu.status == "OK" )
					{
					alert( "OK: UOR atualizada." );
					}
				else
					{
					alert( "Erro adicionando Local de Trabalho ao funcionário " + resu.erro );
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
			let funcao = "usuarios/" + sshd.substring(1);
			let resu = repserviceB( "DELETE", funcao, apalid, "SISPONTO", null, null );
			let aux = resu.erro;
			//	000 -> OK
			//	022 -> não há este usuário no aparelho
			if( aux.indexOf("000") >= 0 || aux.indexOf("022") >= 0 )
				{
				let url = "partes/removeFLTR.php?funiid="+ idfunc + 
									"&apalid="+idapal;
				let resu = remoto( url );
				if( resu.status == "OK" )
					{
					alert( "OK: Removido" );
					}
				else
					{
					alert( "Erro removendo funcionário do Local de Trabalho" + resu.erro );
					return null;
					}
				return true;
				}
			alert( "Erro: " + aux );
			return false;
			}
			
		//	rotinas de tratamento de escolhas
		function escapar( tipo, id )
			{
			if( id > 0 )
				idapal	=	id;
			}

		function escRegi( tipo, id, text )
			{
			if( id >= 0 )
				{
				idreg	=	id;
				//
				let aData = Table.fnGetData(ixedt);
				let dt = toDate( aData.DTFECHA);
				if( toStDate( dt, 2 ) != toStDate( dtuprc, 2 ) )
					{
					let aux = `
						Funcionário: ${aData.NOFUNC}<br>
						Data fechamento: ${aData.DTFECHA}<br>
						Data do último processamento: ${dtudir}<br><br>
						Não foi possível substituir o regime do funcionário
						acima.<br>
						Por favor, faça um fechamento do funcionário antes.
						`;
					$('#info').html(aux);
					$('#infomodal').modal('show');
					restoreRowG( Table, ixedt );
					ixedt = -1;
					return;
					}
				//	altera o regime do funcionário
				let url = "partes/trocaRegiFunc.php?funiid="+ idfunc + 
									"&retrid=" + idreg;
				let resu = remoto( url );
				if( resu.status != "OK" )
					{
					alert( "Erro trocando Regime: " + resu.erro );
					return null;
					}
				let row = Table.fnGetData( ixedt );
				row["IDREG"] = ""+idreg;
				row["NOREG"] = text;
				Table.api().row(ixedt).data(row);
				}
			}

		function escUor( tipo, id, text )
			{
			if( id >= 0 )
				{
				iduor	=	id;
				iduornov = id;
				nouornov = text;
				let aData = Table.fnGetData(ixedt);
				dtaux = toDate( aData.DTFECHA );
				dtaux.setDate(dtaux.getDate()+1);						//	seguinte ao fechamento
				let aux = `Funcionário: ${aData.NOFUNC}`;
				$('#xuornome').html(aux)
				aux = `Data Fechamento: ${aData.DTFECHA}`;
				$('#xuorfech').html(aux)
				aux = `Uor Atual: ${aData.UNIDADE}`;
				$('#xuoratua').html(aux)
				aux = `Nova Uor: ${text}`;
				$('#xuornova').html(aux);
				aux = `
						Data de efetivação: <b>${toStDate( dtaux, 1 )}</b>
						`;
				$('#efetuor').html(aux);
				$("#uormodal").modal('show');
				}
			}

		//	retornos dos modais
		function Cancel()
			{
			restoreRowG( Table, ixedt );
			ixedt = -1;
			}
			
		function uorOK()
			{
			if( iduorant < 0 || iduornov < 0 )
				return;
			if( iduorant == iduornov )
				return;

			let dtefet = toStDate( dtaux, 2 );
			let url = "partes/trocaUorFunc.php?funiid="+ idfunc + 
								"&uornova=" + iduornov +
								"&dtefet=" + dtefet;
			let resu = remoto( url );
			if( resu.status != "OK" )
				{
				alert( "Erro trocando URL: " + resu.erro );
				return null;
				}
			let row = Table.fnGetData( ixedt );
			row["IDLOTADO"] = ""+iduornov;
			row["UNIDADE"] = nouornov;
			Table.api().row(ixedt).data(row);
			
			$("#uormodal").modal('hide');
			restoreRowG( Table, ixedt );
			ixedt = -1;
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
			
		function setAjax()
			{
			tableDestroy();
			AjaxSource	=	"partes/tableData.php?query=autcadas&sshd=" + sshd;
			inicializa.init();
			}
			
/////////////// PRINCIPAL ////////////////////////
		var idapar = -1;
		var iduornov = -1;
		var iduorant = -1;
		var nouorant = "";
		var nouornov = "";
		
		//	variáveis e rotinas de atualização
		var ixedt = -1;			//	row sendo editada
		var idfunc = -1;		//	funiid do funcionario em edição
		var iduor = -1;			//	última uor escolhida
		var idreg = -1;			//	último regime escolhido
		var dtxuor = "";		//	data do fechamento escolhida
		
		var dtaux;					//	data auxiliar de passagem entre a tela e OK
		
		var sshd, nofunc, idapal;
		var idfltr, idapalant;
		
		var parms = "";
		var resu = Select( "parametros", parms );
		if( resu == null )
			throw new Error("Problemas de acesso ao banco de dados. Por favor, tente mais tarde.");
		var dtuinv = resu.dados[0].DTUPROC;
		var dtudir = toDateDir( dtuinv );
		var dtuprc = toDate( dtudir );
		
		
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
				let dt = $("#xuordata").datepicker("getDate");
				dtxuor = $.datepicker.formatDate("yymmdd", dt );
				$("#btok").focus();
				});

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
			"defaultContent": acshow,
			"render": function( data, type, row, meta )
				{
				return	acshow;
				}

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
