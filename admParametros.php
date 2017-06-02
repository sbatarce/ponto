<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Biometria</title>
		<?php
		include 'partes/Head.php';
		?>
  </head>

  <body onload="javascript:Titulo( '<h4>Administração de Parâmetros do sistema</h4>' );">
		<?php
		include 'partes/Menu.php';
		include 'partes/Cabec.php';
		include 'partes/pageheader.php';
		?>
    <img src="imagens/carrega.gif" id="carrega" style="display:none" />
    <!-- Conteúdo específico da página -->
    <div class='row input-append linha' style='margin-top: 10px; margin-left:30px;'>
			<p><b><font size="20">Em construção</b></p>
		</div>

		<?php
			include 'partes/Scripts.php';
		?>
	<script type="text/javascript" src="partes/geral.js" ></script>
	<script>
		$( function()
			{
			$( "#dtinic" ).datepicker(
				{
				dateFormat: "dd/mm/yy"
				});
			$( "#dtterm" ).datepicker(
				{
				dateFormat: "dd/mm/yy"
				});

			$( "#dtinic" ).change(
			function()
				{
				dtinic = $("#dtinic").datepicker("getDate");
				$( "#presen" ).html( "" );
				Registro();
				});

			$( "#dtterm" ).change(
			function()
				{
				dtterm = $("#dtterm").datepicker("getDate");
				$( "#presen" ).html( "" );
				Registro();
				});
			});
		
		function logout()
			{
			Deslogar();
			}

		var iddepen = 0;

		//	calcula a diferença entre dois horários no formato hhmm
		function hoDif( hoini, hofin )
			{
			var h = hoini.substring( 0, 2 );
			var m = hoini.substring( 2 );
			var mini = parseInt( h ) * 60 + parseInt( m );

			h = hofin.substring( 0, 2 );
			m = hofin.substring( 2 );
			var mfin = parseInt( h ) * 60 + parseInt( m );
			var mdif = mfin - mini;
			h = mdif / 60;
			m = mdif % 60;
			var result;
			if( h < 10 )
				result = "0" + parseInt( "" + h );
			else
				result = "" + parseInt( "" + h );
			if( m < 10 )
				result += ":0" + m;
			else
				result += ":" + m;
			return result;
			}

		function escoPess( tipo, id )
			{
			iddepen = id;
			Registro();
			}

		function Registro()
			{
			if( iddepen < 1 )
				{
				$( "#presen" ).val( "" )
				return;
				}
			$( "#presen" ).html( "" );
			var url = "partes/queries.php?query=reprpmspessoa&pessoa=" + iddepen +
								"&dtinic=" + $.datepicker.formatDate("yymmdd", dtinic ) +
								"&dtterm=" + $.datepicker.formatDate("yymmdd", dtterm );
			var resu = remoto( url );
			if( resu == null )
				{
				$( "#presen" ).val( "Falha na obtenção dos dados" )
				return;
				}
			if( resu.linhas < 1 )
				{
				$( "#presen" ).val( "Não há registros neste período" )
				return;
				}

			var diaant = "-";
			var diaatu = "+";
			var lista = "";
			var iord = 0;
			var pontos = "";
			var difers = "";
			var hoini = 0;
			var hofin = 0;

			for( var ix = 0; ix < resu.linhas; ix++ )
				{
				diaatu = resu.dados[ix].ORDEM.substring( 0, 8 );
				if( diaatu != diaant )
					{
					if( diaant != "-" )
						{
						lista += pontos;
						lista += ".........." + difers;
						lista += "</p>";
						}
					diaant = diaatu;
					lista += "<p>" + resu.dados[ix].PONTO.substring( 0, 10 ) + " => ";
					pontos = "";
					difers = "";
					iord = 0;
					hoini = resu.dados[ix].ORDEM.substring( 8 );
					}
				pontos += "  " + resu.dados[ix].PONTO.substring( 11 );
				if( iord++ > 0 )
					{
					hofin = resu.dados[ix].ORDEM.substring( 8 );
					var dif = hoDif( hoini, hofin );
					difers += " " + dif;
					hoini = hofin;
					}
				}
			lista += pontos;
			lista += ".........." + difers;
			lista += "</p>";
			$( "#presen" ).html( lista );
			}
		///////////////////////////////////////////////////////////////
		//	função principal
		var sshd = obterCookie( "biouser" );
		if( sshd == null )
			logout();

		var dtinic = new Date();
		dtinic.setDate( 1 );
		var dtterm = new Date();

		$( "#dtinic" ).datepicker("setDate", dtinic);
		$( "#dtterm" ).datepicker("setDate", dtterm );
		
		var sel = "selectData.php?query=pessoapebi&sshd=" + sshd.toUpperCase();
		SelInit( ".depen", sel, 0, "Escolha abaixo", escoPess );
		</script>
	</body>	
</html>
