  <script src="../_jquery-ui-1.11.1/jquery-1.11.1.js"></script>
  <script src="../_jquery-ui-1.11.1/jquery-ui.js"></script>		
  <script>
	$(function() {
	$( "#datepicker" ).datepicker( {
		dateFormat : "MM dd yy",
		changeMonth : true,
		changeYear : true,
		yearRange: "-30:-15"
		}
	    );
	});
  </script>
