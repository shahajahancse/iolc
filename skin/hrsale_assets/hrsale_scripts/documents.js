$(document).ready(function() {
	var xin_table = $('#doc_table').dataTable({
		"bDestroy": true,
		"ajax": {
			url : site_url+"documents/doc_list_ajax/",
			type : 'GET'
		},
	});

	// Date
	$('.date').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat:'yy-mm-dd',
		yearRange: new Date().getFullYear() + ':' + (new Date().getFullYear() + 10),
	});
});
