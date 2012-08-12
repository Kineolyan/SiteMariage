$(function() {
	/* -- Remove the noJS class -- */
	$('*').removeClass('noJS');
	
	/* -- Connection form -- */
	// Ajouter le popover
	var boutonEnregistrer = $('<div class="btn" id="connect">S\'enregistrer</div>');
	var displayForm = false;
	
	boutonEnregistrer
		.popover({
			animation: true,
			placement: 'bottom',
			trigger: 'manual',
			title: 'Accès à la zone privée',
			content: $('<div/>').append($('#connectionContainer form').remove()).html(),
			delay: { show: 100, hide: 10 }
		})
		.click(function() {
			$(this).popover((displayForm = !displayForm)? 'show': 'hide');
		})
		.prependTo($('#connectionContainer'));
});