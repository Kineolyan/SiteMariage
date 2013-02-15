/**
 * Deplace une entrée d'une liste à l'autre
 */
function moveEntry() {
	var element = $(this);

	if (element.hasClass('toLeft')) {
		recuperer(element);
	} else if (element.hasClass('toRight')) {
		envoyer(element);
	}
}

function envoyer(element) {
	element.find('input[name^="send"]').val(1);
	element
		.removeClass('toRight').addClass('toLeft')
		.detach().appendTo('#envoyes');

	return element;
}

function recuperer(element) {
	element.find('input[name^="send"]').val(0);
	element
		.removeClass('toLeft').addClass('toRight')
		.detach().appendTo('#aEnvoyer');

	return element;
}

$(function() {
	var loader = new library.Loader(['Filtre catégories']);

	$('.movingEntry').click(moveEntry);

	new library.Filtre($('#searchBar'), $('.movingEntry'));

	loader.load('Filtre catégories');
})