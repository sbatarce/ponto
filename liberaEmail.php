<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    <title>Ponto - Confirmação de EMAIL</title>
		<!-- Favicon -->
		<link rel="shortcut icon" href="imagens/PMSICO.png">
  </head>

	<body onload="init()">
		<!-- Conteúdo específico da página -->
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-md-4 col-md-offset-4">
					<h1 class="text-center login-title" style='align-content: center; color: blue; '>Confirmação de email</h1>
					<h2 id="texto">original</h2>
					<div class="account-wall">
					</div>
				</div>
			</div>
		</div> <!-- /container -->
		<script src="assets/js/jquery-2.0.3.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/slimscroll/jquery.slimscroll.min.js"></script>
		<script type="text/javascript" >
			var parms = decodeURIComponent(document.location.search);
			$('#texto').text( parms );
			
		</script>
	</body>	
</html>