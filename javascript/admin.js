function refreshPages() {
	$.getJSON('scripts/pagesRegistration.php', { ajax: 1}, function(pageList) {
		var updatedList = '';
		$.each(pageList, function(key, page) {
			updatedList+= '<li>['+ page.id +']'+ page.title +'</li>';
		});
		
		$('#pageList li:last').before(updatedList);
	});
}

$(function() {
	$('#pageList')
		.append("<li><button>Actualiser ...</button></li>")
			.click(refreshPages);
});