<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>jQuery UI Datepicker - Default functionality</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() 
		{
		var odat = new Date(2017, 01, 21, 0, 0, 0 );
		var adic = 0;
		var tx = $.datepicker.formatDate("dd/mm/yy", odat );
		$("#odat").val(tx);

		$( "#datepicker" ).datepicker(
			{
			dateFormat: "dd/mm/yy",
			altFormat: "yymmdd"
			});
		
		$("#datepicker").change( 
		function()
			{
			var dt = $("#datepicker").datepicker("getDate");
			adic += 1;
			var odt = new Date(dt.getTime()+(1000*60*60*24*adic));			
			dt.setDate(1);
			var dtl = $.datepicker.formatDate("dd/mm/yy", dt );
			$("#texto").val( dtl );
			var dtl = $.datepicker.formatDate("dd/mm/yy", odt );
			$("#odat").val(dtl);
			//$("#texto").val("passou");
			} );
		var data = new Date();
		$('#datepicker').val("10/02/2014");
		} );

  </script>
</head>
<body>
 
	<p>Text: <input type="text" id="texto"></p>
	<p>Date: <input type="text" id="datepicker" ></p>
	<p>Outra data: <input type="text" id="odat"></p>
	
 
 
</body>
</html>