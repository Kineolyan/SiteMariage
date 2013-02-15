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

var loader = null;
$(function() {
	loader = new library.Loader(
		['Statut updater', 'Filtre nom', 'Filtre catégories', 'Live editor']);

	$('.btn-group li').click(majStatut);
	loader.load('Statut updater');
	
	new library.Filtre($('#searchBar'), $('#invites tbody tr'), 'itemData');
	loader.load('Filtre nom');

	creerSelectionCategories();
	loader.load('Filtre catégories');

	var modalEditor = new library.Modal("Edition d'un invité");
	$('.editionLink').click(function(e) {
		e.preventDefault();

		var src = this.href + '&display=modal';
		modalEditor.show(src);
	});
	loader.load('Live editor');
});