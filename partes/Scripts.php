<!--Basic Scripts-->
<script src="assets/js/jquery-2.0.3.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/slimscroll/jquery.slimscroll.min.js"></script>
<!--Beyond Scripts-->
<script src="assets/js/beyond.min.js"></script>
<!-- Datatables  e select2 -->
<script src="assets/js/datatable/jquery.dataTables.min.js"></script>
<script src="assets/js/datatable/ZeroClipboard.js"></script>
<script src="assets/js/datatable/dataTables.tableTools.min.js"></script>
<script src="assets/js/datatable/dataTables.bootstrap.min.js"></script>
<script src="assets/js/select2/select2.min.js" ></script>
<!-- scripts do tema -->		
<script type="text/javascript" src="bootstrap-3.3.1/dist/js/jquery.themepunch.plugins.min.js"></script>
<script type="text/javascript" src="bootstrap-3.3.1/dist/js/jquery.themepunch.revolution.min.js"></script>
<script type="text/javascript" src="bootstrap-3.3.1/dist/js/waypoints.min.js"></script>
<script type="text/javascript" src="bootstrap-3.3.1/dist/js/jquery.countdown.min.js"></script>
<script type="text/javascript" src="bootstrap-3.3.1/dist/js/filter.js"></script>
<script type="text/javascript" src="bootstrap-3.3.1/dist/js/custom.js"></script>
<!-- jquery 
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
-->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- /Basic Scripts-->
<script type="text/javascript">
	$( ".menu-btn" ).click( function( e )
		{
		$(".fun").show();
		var uorAut = obterCookie( "uoraut" );
		let tiuser = obterCookie( "tiuser" );
		if( uorAut != "" || tiuser > 3 )
			$(".aut").show();
		else
			$(".aut").hide();
		var adm = obterCookie( "admin" );
		if( adm != "" )
			$(".adm").show();
		else
			$(".adm").hide();
		});
	
  $(document).ready(function () 
    {
		function attip()
			{
			$('[ttip="ttip"]').tooltip();
			}
		function titulo( tit )
			{
			document.getElementById("titu").innerHTML = tit;
			}
		attip();
    });
</script>

