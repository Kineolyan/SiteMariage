$(function() {
	/* -- Remove the noJS class -- */
	$('*').removeClass('noJS');
	$('.hideIfNoJS').hide();
	
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

	// secure mailto items
	$("a[rel='email']").each(function(){
		// Modify the mailto: value
		var mailAddress = $(this).attr('href');
		if (!mailAddress) {
			mailAddress = $(this).text();
		}

		mailAddress = mailAddress.replace("[at]","@");
		mailAddress = mailAddress.replace("[point]",".");

		// Auto-generate title tags for users
		$(this).attr('title', "Email: " + mailAddress);
		// onClick Event
		$(this).click(function(){
			window.location.href = "mailto:" + mailAddress;
			return false;
		});
	});
});