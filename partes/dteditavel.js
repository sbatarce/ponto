	//	globais
	var nEdit		= null;										//	linha existente ou nova em edição
	var nNova		=	null;										//	linha nova em edição
	var nChil   = null;	      						//  row.child visível
	var tdChil  = null;	      						//  TDs da row.child visível
	var trChil	=	null;										//	tr da row.child
	var dlen		=	"25";
	var lens    = [[5, 15, 25, 35, -1],
							[5, 15, 25, 35, "todos"]];

	var aiNew = null;

	//	definição do input de entrada nos datatables
	//	colocar o valor e demais atributos logo após
	//	fechar a div com: "></div>
	//		exemplo: dfinput + aData[1] + '"></div>';
	var dfinput	=	"<div class='input-group'>" +
								"<input type='text' class='form-control input-small' value='";
				
	//	inicializações da coluna de ações
	//		acalter	=>	a alterar ou remover linha existente
	//		acnome	=>	a salvar ou cancelar linha nova em edição
	//		acsave	=>	a salvar ou cancelar linha existente em edição
	var acalter = "<a href='#' class='btn btn-circle btn-info btn-xs edit' data-mode='alt' " +
								"title=\"editar a linha\" >" +
								"   <i class='typcn typcn-edit'></i></a>" +
								"<a href='#' class='btn btn-circle btn-danger btn-xs delete' data-mode='alt' " +
								"title=\"remover a linha\" >" +
								"   <i class='typcn typcn-delete-outline'></i></a>";
	var acnova	=	"<a href='#' class='btn btn-success btn-xs save' data-mode='new' " +
								"title=\"salvar\" >" +
								"   <i class='typcn typcn-tick-outline'></i></a>" +
								"<a href='#' class='btn btn-warning btn-xs cancel' data-mode='new' " +
								"title=\"desistir\" >" +
								"   <i class='typcn typcn-times-outline'></i></a>";
	var acsave = 	"<a href='#' class='btn btn-success btn-xs save' " +
								"title=\"salvar\" >" +
								"   <i class='typcn typcn-tick-outline'></i></a>" +
								"<a href='#' class='btn btn-warning btn-xs cancel' " +
								"title=\"desistir\" >" +
								"   <i class='typcn typcn-times-outline'></i></a>";
	var	acdeta	=	"<a href='#' class='btn btn-info btn-xs detalhes'><i class='fa fa-plus'></i></a>";
	var acshow  = "<a href='#' class='btn btn-circle btn-info btn-xs detshow' " +
								"title=\"Mostrar detalhes\" >" +
					      "<i class='typcn typcn-plus-outline'></i></a>";
	var achide  = "<a href='#' class='btn btn-circle btn-info btn-xs dethide' " +
								"title=\"esconder detalhes\" >" +
					      "<i class='typcn typcn-minus-outline'></i></a>";
	var acsim		= "<a href='#' class='chk' value='1' >" +
					      "<i class='typcn typcn-tick' /></a>";
	var acnao		= "<a href='#' class='chk' value='0' >" +
					      "<i class='typcn typcn-times'></i></a>";
	var shsim		= "<i class='typcn typcn-tick' />";
	var shnao		= "<i class='typcn typcn-times' />";
	
	var Table = null;
	
	//	atualizações no banco de dados
	function DBSetUpdt( oTable, nRow )
		{
		var	iinp, isel, ichk, nocmp, ticmp, set, vacmpid, resul, valor;
		var aData = oTable.fnGetData(nRow);
		var jqInputs	= $('input.input-small', nRow);
		var jqSelecs	=	$('span.selec2-chosen', nRow );
		var jqChks = $("a.chk", nRow );
		cmpid	=	aData[nocmpid];
		set		=	"";
		isel	=	0;
		for( ichk=0; ichk<jqChks.length; ichk++ )
			{
			nocmp	=	jqChks[ichk].attributes.cmp.value;
			icol	=	jqChks[ichk].attributes.inx.value;
			valor	= jqChks[ichk].attributes.value.value;
			if( valor == aData[nocmp] )
				continue;
			if( set.length > 0 )
				set	+=	",";
			set	+=	nocmp + "='" + valor + "'";
			}
		for( iinp=0; iinp<jqInputs.length; iinp++ )
			{
			ticmp	=	jqInputs[iinp].attributes.tip.value;
			icol	=	jqInputs[iinp].attributes.inx.value;

			if( jqInputs[iinp].value == null || jqInputs[iinp].value == undefined )
				continue;
			if( jqInputs[iinp].value == "" )
				continue;
			
			if( ticmp == "l" )
				{
				nocmp	=	colDefs[icol].selID;
				valor	=	jqInputs[iinp].value;
				if( valor == aData[nocmp] )
					continue;
				}
			else
				{
				nocmp	=	jqInputs[iinp].attributes.cmp.value;
				valor	=	jqInputs[iinp].value
				if( valor == aData[nocmp] )
					continue;
				}
			if( set.length > 0 )
				set	+=	",";
			if( ticmp == "t" )
				set	+=	nocmp + "='" + valor + "'";
			if( ticmp == "n" )
				set	+=	nocmp + "=" + valor;
			if( ticmp == "l" )
				set	+=	nocmp + "='" + valor + "'";
			}
		if( set == "" )
			return true;
		//	atualiza o banco de dados
		var url =	"partes/updates.php?query=upd&tbl=" + notabel + 
							"&alter=" + set + "&selec=" + nocmpid + "=" + cmpid;
		resul	=	remoto( url )
		if( resul.status == "OK" )
			return true;
		alert( "Erro salvando dados: " + resul.erro );
		return false;
		}
		
	//	retorna o "ID" da nova linha inserida ou null em caso de erro
	function DBSetIsrt( oTable, nRow, seq )
		{
		var	iinp, nocmp, ticmp, cms, vls, icol, valor;
		var aData = oTable.fnGetData(nRow);
		var jqInputs	= $('input.input-small', nRow);
		var jqChks = $("a.chk", nRow );
		vls		=	seq + ".nextval";
		cms		=	nocmpid;
		isel	=	0;
		for( ichk=0; ichk<jqChks.length; ichk++ )
			{
			nocmp	=	jqChks[ichk].attributes.cmp.value;
			icol	=	jqChks[ichk].attributes.inx.value;
			valor	= jqChks[ichk].attributes.value.value;
			cms	+=	", " + nocmp;
			vls	+=	", '" + valor + "'";
			}
		for( iinp=0; iinp<jqInputs.length; iinp++ )
			{
			icol	=	parseInt(jqInputs[iinp].attributes.inx.value);
			ticmp	=	jqInputs[iinp].attributes.tip.value;
			if( ticmp == "l" )
				{
				nocmp	=	colDefs[icol].selID;
				valor	=	jqInputs[iinp].value;
				if( valor == "0" || valor == "" )
					{
					alert( "por favor, preencha os campos corretamente" );
					return null;
					}
				}
			else
				{
				if( jqInputs[iinp].value == null || jqInputs[iinp].value == undefined )
					{
					if( colDefs[icol].editavel == true || colDefs[icol].editavel == undefined )
						{
						alert( "por favor, preencha os campos corretamente" );
						return false;
						}
					}
				if( jqInputs[iinp].value == "" )
					{
					if( colDefs[icol].editavel == true || colDefs[icol].editavel == undefined )
						{
						alert( "por favor, preencha os campos corretamente" );
						return false;
						}
					}
				nocmp	=	jqInputs[iinp].attributes.cmp.value;
				if( jqInputs[iinp].value == null || jqInputs[iinp].value == undefined )
					valor	=	null;
				else
					valor	=	jqInputs[iinp].value;
				}
			if( ticmp == "t" || ticmp == 'l' )
				{
				cms	+=	", " + nocmp;
				vls	+=	", '" + valor + "'";
				}
			if( ticmp == "n" )
				{
				cms	+=	", " + nocmp;
				vls	+=	", " + valor;
				}
			}													//	for
		//	atualiza o banco de dados
		idnovo	=	"";
		var url = "partes/inserts.php?query=ins&sequence=" + seq + 
							"&tbl=" + notabel + "&cmps=" + cms + "&vals=" + vls;
		var resul	=	remoto( url )
		if( resul.status != "OK" )
			{
			alert( "Erro salvando dados: " + resul.erro );
			return null;
			}
		if( parseInt( resul.idnovo ) > 0 )
			return resul.idnovo;
		else
			return null;
		}
		
	//	deleção genérica de uma linha do DT e registro correspondente no DB
	function DBDelete( oTable, nRow )
		{
		var aData = oTable.fnGetData(nRow);
		var url = "partes/updates.php?query=del&tbl=" + notabel + "&cmp=" +
							nocmpid + "&val=" + aData[nocmpid];
		var resul	=	remoto( url );
		if( resul.status != "OK" )
			{
			alert( "Erro removendo do banco:" + resul.erro );
			return false;
			}
		else
			return true;
		}
		
	function MostraRow( oTable, ix )
		{
		var pos = 0;
		var pginf = oTable.api().page.info();
		var todos = oTable.api().rows().indexes();
		for( pos=0; pos<todos.length; pos++ )
			{
			if( todos[pos] == ix )
				break;
			}
		var nupag	=	parseInt(pos/pginf.length);
		oTable.api().page(nupag).draw( false );
		}
		
	//	atualiza, modifica remove e insere linha no datatables
	function deleteRowG( oTable, nRow ) 
		{
		var page = nRow._DT_RowIndex;
		oTable.fnDeleteRow(nRow);
		MostraRow( oTable, page );
		}
	function editRowG( oTable, nRow ) 
		{
		var aData = oTable.fnGetData(nRow);
		var jqTds = $('>td', nRow);
		var itds, ctl, attr;
		
		MostraRow( oTable, nRow._DT_RowIndex );
		for( itds=0; itds<jqTds.length-1; itds++ )
			{
			ctl	=	" ";
			if( colDefs[itds].tipo != null )
				{
				attr	=	"inx=" + itds + " " +
								"cmp='" + colDefs[itds].mData + "' " +
								"tip='" + colDefs[itds].tipo + "' ";

				}
			if( colDefs[itds].tipo == "t" || colDefs[itds].tipo == "n" )
				{
				if( nNova == null )
					ctl	=	dfinput + aData[colDefs[itds].mData] + "' " + attr;
				else
					ctl	=	dfinput + "' " + attr;
					
				if( colDefs[itds].editavel )
					ctl	+=	"></div>";
				else
					ctl	+=	"readonly></div>";
				}
			if( colDefs[itds].tipo == "l" )
				{
				ctl	=	"<input type='hidden' style='width:100%' class='input-small " + 
							colDefs[itds].classe + "' " +
							attr + " />";
				}
			if( colDefs[itds].tipo == "x" )
				{
				if( aData[colDefs[itds].mData] != 0 )
					{
					ctl	=	"<a href='#' class='chk' value='1' " + attr + " >" +
					      "<i class='typcn typcn-tick' /></a>";
					}
				else
					ctl	=	"<a href='#' class='chk' value='0' " + attr + " >" +
					      "<i class='typcn typcn-times' /></a>";
				}
			jqTds[itds].innerHTML = ctl;
			}
		jqTds[itds].innerHTML = acsave;
		for( itds=0; itds<jqTds.length-1; itds++ )
			{
			if( colDefs[itds].tipo == "l" )
				{
				if( nNova == null )
					SelInit( "."+colDefs[itds].classe, colDefs[itds].selURL, 
									aData[colDefs[itds].selID], aData[colDefs[itds].selVal], 
									null, colDefs[itds].selminlen );
				else
					SelInit( "."+colDefs[itds].classe, colDefs[itds].selURL, 
									0, "Escolha abaixo", null, colDefs[itds].selminlen );
				}
			}
		}
	
	function restoreRowG( oTable, nRow )
		{
		var aData = oTable.fnGetData(nRow);
		var jqTds = $('>td', nRow);
		var itds;
		for( itds=0; itds<jqTds.length-1; itds++ )
			{
			if( colDefs[itds].mData )
				{
				oTable.fnUpdate(aData[colDefs[itds].mData], nRow, itds, false);
				}
			}
		oTable.fnUpdate( acalter, nRow, itds, false);
		}
	
	//	idnovo	=>	
	function saveRowG( oTable, nRow, idnovo )
		{
		var jqInputs	= $('input.input-small', nRow);
		var jqSelecs	=	$('span.select2-chosen', nRow);
		var jqChks = $("a.chk", nRow );
		var jqTds = $('>td', nRow);
		var	ichk, iinp, isel, sel, cbval, icol, tipo;
		var aData = oTable.fnGetData(nRow);
		for( var i=0; i<colDefs.length; i++ )
			{
			console.log( colDefs[i] );
			}
		isel	=	0;
		for( ichk=0; ichk<jqChks.length; ichk++ )
			{
			if( jqChks[ichk].attributes.tip == undefined )
				continue;
			if( jqChks[ichk].attributes.tip.value != "x" )
				continue;
			icol	=	jqChks[ichk].attributes.inx.value;
			var val = jqChks[ichk].attributes.value.value;
			if( val == "0" )
				oTable.fnUpdate( "0", nRow, icol, true );
			else
				oTable.fnUpdate( "1", nRow, icol, true );
			}
		for( iinp=0; iinp<jqInputs.length; iinp++ )
			{
			icol	=	parseInt(jqInputs[iinp].attributes.inx.value);
			tipo	=	jqInputs[iinp].attributes.tip.value;
			if( tipo == "l" )
				oTable.fnUpdate( jqSelecs[isel++].innerHTML, nRow, icol, false);
			else
				oTable.fnUpdate( jqInputs[iinp].value, nRow, icol, false);
			}
		//	seta o ID de linhas novas
		if( idnovo != null )
			{
			aData	=	oTable.fnGetData(nRow);
			aData[nocmpid]	=	idnovo;
			oTable.fnUpdate( aData, nRow, undefined, false);
			aData	=	oTable.fnGetData(nRow);
			}
		oTable.fnUpdate( acalter, nRow, jqTds.length-1, false);
		var aData = oTable.fnGetData(nRow);
		}
	
	//	cancela a edição da linha nRow
	function cancelEditRowG( oTable, nRow )
		{
		var jqInputs = $('input', nRow);
		var	itds;
		for( itds=0; itds<jqInputs.length-1; itds++ )
			{
			if( origem[itds] )
				{
				oTable.fnUpdate( jqInputs[itds].value, nRow, origem[itds], false);
				}
			}
		oTable.fnDraw();
		}
		
	function removeChild()
		{
		if( nChil != null )
			{
			nChil.child.hide();
			trChil.removeClass( 'visivel' );
			nChil.child(false);
			tdChil[0].innerHTML = acshow;
			trChil	=	null;
			nChil   = null;
			tdChil  = null;
			}
		}
		
	function atuatab( repag )
		{
		Table.api().ajax.reload( null, repag );
		}
		
	function obterChild()
		{
		return nChil;
		}
		
	function tableDestroy()
		{
		Table.fnDestroy();
		}
	
	var inicializa = function ()
		{
		return {
			init: function () 
				{
				//Datatable Initiating
				var oTable = $('#eddt').dataTable
					(
						{
						"aLengthMenu": lens,
						"iDisplayLength": dlen,
						"sPaginationType": "bootstrap",
						"sDom": "Tflt<'row DTTTFooter'<'col-sm-6'i><'col-sm-6'p>>",
						"aoColumnDefs": colDefs,
						"sAjaxSource": AjaxSource,
						"oTableTools": 
							{
							"aButtons": 
								[
	                {
									"sExtends": "print",
									"sButtonText": "imprimir"
									},
	                {
									"sExtends": "xls",
									"sButtonText": "Excel"
									},
	                {
									"sExtends": "pdf",
									"sButtonText": "PDF"
									}
								],
							"sSwfPath": "assets/swf/copy_csv_xls_pdf.swf"
							},
						"language": 
							{
							"emptyTable":     "Não há dados",
							"info":           "Registros de _START_ até _END_ do total de _TOTAL_",
							"infoEmpty":      "Registros de 0 até 0 do total de 0",
							"infoFiltered":   "(filtrados do total de _MAX_)",
							"infoPostFix":    "",
							"thousands":      ".",
							"sLengthMenu":		"_MENU_",
							"loadingRecords": "Carregando...",
							"processing":     "Processando...",
							"search":         "",
							"zeroRecords":    "Sem correspondẽncia",
							"paginate": 
								{
								"first":      "Primeiro",
								"last":       "Último",
								"next":       "Próximo",
								"previous":   "Anterior"
								},
							"aria": 
								{
								"sortAscending":  ": para ordem ascendente",
								"sortDescending": ": para ordem descendente"
								}
							},																	//	language
						"order": order
						}
					);																			//	datatable
				oTable.$('[ttip="ttip"]').tooltip();
				Table	=	oTable;

				//	cria uma nova linha
        $('#eddt_new').click
					(
					function (e) 
						{
						e.preventDefault();
						
						aiNew = oTable.fnAddData( liNova );
						var nRow = oTable.fnGetNodes(aiNew[0]);
						if( nEdit !== null && nEdit != nRow ) 
							{
							if( nNova != null )
								{
								oTable.fnDeleteRow(nNova);
								}
							else
								{
								restoreRow(oTable, nEdit);
								}
							nEdit = nRow;
							nNova	=	nRow;
							editRow(oTable, nRow);
							} 
						else 
							{
							nEdit = nRow;
							nNova	=	nRow;
							editRow(oTable, nRow);
							}
						}
					);
				
				//Delete an Existing Row
				$('#eddt').on
					(
					"click", 'a.delete', function (e) 
						{
						e.preventDefault();
						var nRow = $(this).parents('tr')[0];

						if( nChil != null )
							{
							tdChil[0].innerHTML	= acshow;
							nChil.child.hide();
							nChil   = null;
							tdChil  = null;
							trChil.removeClass( 'visivel' )
							}
						if( nEdit !== null && nEdit != nRow ) 
							{
							if( nNova != null )
								{
								oTable.fnDeleteRow(nEdit);
								}
							else
								{
								restoreRow(oTable, nEdit);
								}
							if( confirm( "Remover esta linha? Não haverá como desfazer." ) == false ) 
								return;
							} 
						nEdit = nRow;
						nNova	=	null;
						deleteRow( oTable, nRow );
						nEdit	=	null;
						}
					);
					
				//Cancel Editing or Adding a Row
				$('#eddt').on
					(
					"click", 'a.cancel', function (e) 
						{
						e.preventDefault();
						if( nNova != null ) 
							{
							var nRow = $(this).parents('tr')[0];
							oTable.fnDeleteRow(nRow);
							nNova	=	null;
							nEdit	=	null;
							} 
						else 
							{
							restoreRow( oTable, nEdit );
							nEdit = null;
							nNova	=	null;
							}
						}
					);
				
				//	edita uma linha
				$('#eddt').on
					(
					"click", 'a.edit', function (e) 
						{
						e.preventDefault();
						
						var nRow = $(this).parents('tr')[0];

						if( nChil != null )
							{
							tdChil[0].innerHTML	= acshow;
							nChil.child.hide();
							nChil   = null;
							tdChil  = null;
							trChil.removeClass( 'visivel' )
							}
						if( nEdit !== null && nEdit != nRow ) 
							{
							if( nNova != null )
								{
								oTable.fnDeleteRow(nEdit);
								}
							else
								{
								restoreRow(oTable, nEdit);
								}
							nEdit = nRow;
							nNova	=	null;
							editRow(oTable, nRow);
							} 
						else 
							{
							nEdit = nRow;
							nNova	=	null;
							editRow(oTable, nRow);
							}
						}
					);
					
				//	salva a linha em edição
				$('#eddt').on
					(
					"click", 'a.save', function (e) 
						{
						e.preventDefault();
						if( saveRow(oTable, nEdit) )
							{
							nEdit = null;
							nNova	=	null;
							}
						}
					);
				
				$( '#eddt' ).on
					(
					'click', 'a.detshow', function()
						{
						var tr = $( this ).closest( 'tr' );
						var nRow = $( this ).parents( 'tr' )[0];
						var jqTds = $( '>td', nRow );
						var row = oTable.api().row(tr);
						if( nEdit !== null ) 
							{
							if( nNova != null )
								oTable.fnDeleteRow(nEdit);
							else
								restoreRow(oTable, nEdit);
							nEdit = null;
							nNova	=	null;
							} 
						if( nChil != null && nChil != row )
							{
							tdChil[0].innerHTML	= acshow;
							nChil.child.hide();
							nChil   = null;
							tdChil  = null;
							trChil.removeClass( 'visivel' )
							}
						if( !row.child() || row.child() == undefined )
							row.child( FormataChild( row.data() ) );
						row.child.show();
						jqTds[0].innerHTML = achide;
						tr.addClass( 'visivel' )
						nChil	  = row;
						tdChil  = jqTds;
						trChil	=	tr;
						selecChild( row.data() );
						}									//  click a.detshow function
					);

				$( '#eddt' ).on
					(
					'click', 'a.dethide', function()
						{
						if( nChil != null )
							{
							nChil.child.hide();
							trChil.removeClass( 'visivel' )
							tdChil[0].innerHTML = acshow;
							trChil	=	null;
							nChil   = null;
							tdChil  = null;
							}
						}									//  click a.dethide function
					);									//	$( '#eddt' ).on( 'click'

				$('#eddt').on
					(
					"click", 'a.chk', function (e) 
						{
						e.preventDefault();
						if( $(this).attr( "value" ) == "1" )
							{
							$(this).attr( "value", "0" )
							$(this).html( shnao );
							}
						else
							{
							$(this).attr( "value", "1" )
							$(this).html( shsim );
							}
						}
					);
				}											//	init: function()
			};											//	return
		}();
