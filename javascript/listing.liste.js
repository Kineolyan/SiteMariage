function updateStatus() {
	var select = $(this);
	var nameAttribute = select.attr('name');
	var selectElements = /status_(\d+)/.exec(nameAttribute);
	if (null!=selectElements) {
		$.ajax({
			  url: 'listing.php',
			  data: { ajax: 1, id:selectElements[1], status:select.val() },
			  success: function(data) {
				  $('span.' + nameAttribute).text(data);
			  },
			  error: function(xhr) { console.log("error"); console.log(xhr); }
			});
		
//		$.getJSON('listing.php', 
//			{ ajax: 1, id:selectElements[1], status:select.val() }
//		);
	}
	
}

$(function() {
	$('select').change(updateStatus);
});