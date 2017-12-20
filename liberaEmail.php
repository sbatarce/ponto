<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    <title>Ponto</title>
		<?php
		include 'partes/Head.php';
		?>
		<!-- Favicon -->
		<link rel="shortcut icon" href="imagens/PMSICO.png">
			<style>
				.form-signin
				{
					max-width: 330px;
					padding: 15px;
					margin: 0 auto;
				}
				.form-signin .form-signin-heading, .form-signin .checkbox
				{
					margin-bottom: 10px;
				}
				.form-signin .checkbox
				{
					font-weight: normal;
				}
				.form-signin .form-control
				{
					position: relative;
					font-size: 16px;
					height: auto;
					padding: 10px;
					-webkit-box-sizing: border-box;
					-moz-box-sizing: border-box;
					box-sizing: border-box;
				}
				.form-signin .form-control:focus
				{
					z-index: 2;
				}
				.form-signin input[type="text"]
				{
					margin-bottom: -1px;
					border-bottom-left-radius: 0;
					border-bottom-right-radius: 0;
				}
				.form-signin input[type="password"]
				{
					margin-bottom: 10px;
					border-top-left-radius: 0;
					border-top-right-radius: 0;
				}
				.account-wall
				{
					margin-top: 20px;
					padding: 40px 0px 20px 0px;
					background-color: #f7f7f7;
					-moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
					-webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
					box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
				}
				.login-title
				{
					color: #555;
					font-size: 18px;
					font-weight: 400;
					display: block;
				}
				.profile-img
				{
					width: 96px;
					height: 96px;
					margin: 0 auto 10px;
					display: block;
					-moz-border-radius: 50%;
					-webkit-border-radius: 50%;
					border-radius: 50%;
				}
				.need-help
				{
					margin-top: 10px;
				}
				.new-account
				{
					display: block;
					margin-top: 10px;
				}				
			</style>
  </head>

	<body onload="init()">
		<?php
		include 'partes/MenuPri.php';
		include 'partes/Cabec.php';
		?>
		<!-- Conteúdo específico da página -->
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-md-4 col-md-offset-4">
					<h1 class="text-center login-title">Por favor, identifique-se</h1>
					<div class="account-wall">
						<img class="profile-img" src="imagens/user.png"
								 alt="">
							<form class="form-signin" action="javascript:login()">
								<input id="user" type="text" class="form-control" placeholder="usuário" required autofocus>
								<input id="pass" type="password" class="form-control" placeholder="senha" required>
								<button class="btn btn-lg btn-primary btn-block" type="submit">
									Acessar
								</button>
								<input type="checkbox" value="remember-me">
									lembrar-me
							</form>
							<input id="resul" style="width: 98%;" class='input-small' readonly />
					</div>
				</div>
			</div>
		</div> <!-- /container -->

		<?php
		include 'partes/Scripts.php';
		?>
		<script type="text/javascript" src="partes/geral.js" ></script>
		<script type="text/javascript" >

			function VerUser(user, loguser, logpass)
				{
				$("#resul").val("");
				var aux = "partes/verLogin.php?loguser=" + loguser.toUpperCase() + 
											"&user=" +  user +
											"&logpass=" +  encodeURIComponent( logpass );
				var resul = remoto( aux );
				if( resul.status == "OK" )
					{
					matarCookie("uoraut");
					matarCookie("super");
					criarCookie("tiuser", resul.PERF_ID, 1);
					
						
					if( resul.PERF_ID == "4" || resul.PERF_ID == "5" )
						{
						if( resul.UOR_ID != "" )
							criarCookie("uoraut", resul.UOR_ID, 0, 10 );
						criarCookie("super", "1", 0, 10 );
						$(".fun").show();
						$(".aut").show();
						$(".adm").show();
						}
					else
						{
						if( resul.UOR_ID != "" )
							{
							criarCookie("uoraut", resul.UOR_ID, 0, 10 );
							$(".fun").show();
							$(".aut").show();
							$(".adm").hide();
							}
						else
							{
							$(".fun").show();
							$(".aut").hide();
							$(".adm").hide();
							}
						}
					$("#menupri").collapse("show");
					$("#menu").show();
					$("#menu").click();
					return true;
					}
				else
					{
					if( typeof resul.dbmens == "undefined" )
						$("#resul").val(resul.erro);
					else
						$("#resul").val(resul.erro + "-" + resul.dbmens);
					matarCookie("user");
					matarCookie("pass");
					matarCookie("uoraut");
					matarCookie("idfunc");
					$(".usu").hide();
					$(".aut").hide();
					$(".adm").hide();
					$("#menu").hide();
					return false;
					}
				}

			function init()
				{
				var loguser = obterCookie("loguser");
				var logpass = obterCookie("logpass");
				var user = obterCookie("user");
				var pass = obterCookie("pass");
				if (user != null)
					$("#user").val(user.toUpperCase());
				if (pass != null)
					$("#pass").val(pass);
				$("#menu").hide();
				}

			function login()
				{
				var user = $("#user").val();
				var pass = $("#pass").val();
				if (user == "" || pass == "")
					return false;
				if( VerUser(user.toUpperCase(), user.toUpperCase(), pass ) )
					{
					criarCookie("user", user.toUpperCase(), 0, 10 );
					criarCookie("pass", pass, 0, 10 );
					}
				}
				
			function logout()
				{
				$("#menupri").collapse('hide');
				$("#menu").hide();
				matarCookie("user");
				matarCookie("pass");
				matarCookie("loguser");
				matarCookie("logpass");
				matarCookie("uoraut");
				}
		</script>
	</body>	
</html>