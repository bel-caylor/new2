/*
Datepicker for schedules, exports and graphs
Version: 0.3
Original code: Arnan de Gans
Copyright: See notice in adrotate-pro.php
*/
(function($) {
	$(document).ready(function() {
		$('#startdate_graph_picker').datepicker({dateFormat: 'dd-mm-yy'}); 
		$('#enddate_graph_picker').datepicker({dateFormat: 'dd-mm-yy'}); 
		$('#startdate_picker').datepicker({dateFormat: 'dd-mm-yy'}); 
		$('#enddate_picker').datepicker({dateFormat: 'dd-mm-yy'}); 
	});
}(jQuery));
