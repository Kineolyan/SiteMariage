function majStatut() {
	var liElt = $(this);
	var statut = liElt.parent().parent().find('span');
	var btn = liElt.parent().parent().find('.btn');

	if (statut.text() != liElt.text()) {
		library.json({
			url: 'listing.php',
			data: {
				action:'updateStatus',
				id:statut.attr('statusId'), 
				oldStatus: statut.text(), 
				newStatus:liElt.text()
			},
			success: function(data) {
				statut.text(data.updatedStatus);
				btn.removeClass(data.oldClass).addClass(data.newClass);
			}
		});
	}
}

function creerSelectionCategories() {
	library.ajax({
		url: 'listing.php',
		data: { action:'getCategories' },
		success: function(data) {
			var categories = data.split('|');
			new library.Selector($('#selectBar'), $('#invites tbody tr'), categories, 
				{ attribut: "categories", texts: { select: "Sélection par catégorie" }});
		}
	});
}

$(function() {
	$('.btn-group li').click(majStatut);
	$("#invites").tablesorter({sortList: [[0,0], [1,0]], headers: { 2:{sorter: false}, }});
	
	new library.Filtre($('#searchBar'), $('#invites tbody tr'), 'itemData');
	creerSelectionCategories();
});