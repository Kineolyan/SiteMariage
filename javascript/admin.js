function refreshPages() {
	library.ajax({
		url: 'scripts/pagesRegistration.php', 
		data: {}, 
		success: function(pageList) {
			var updatedList = '';
			$.each(pageList, function(key, page) {
				updatedList+= '<li>['+ page.id +']'+ page.title +'</li>';
			});
			
			$('#pageList li:last').before(updatedList);
		}
	});
}

function listerNouvelleCategorie(categorie) {
	$('#listCategories').append('<li>' + categorie + '</li>')
}

$(function() {
	var actualiserBtn = $('<span class="btn">Actualiser les pages</span>')
		.click(refreshPages);
	$('#accessSubmitBtn').before(actualiserBtn);
	
	$('#categoriesForm').submit(function() {
		var form = this;
		
		library.ajax({
			url: 'admin.php',
			data: { categorie: form.categorie.value, action: 'ajouterCategorie' },
			success: function(data) { listerNouvelleCategorie(data.success); }
		});
		
		// Reset le formulaire
		this.reset();
		
		// Only submit via Ajax
		return false;
	});
	
//	$('select[name="editLogin"]').change(function() {
//		var select = this;
//		jQuery.ajax({ url: 'admin.php', method: 'post', 
//			data: {editLogin: select.value},
//			success: function(page) {
//				console.log(page);
//				jQuery('#userProfile').html(
//					jQuery(page).find('#userProfile').html());
//		}});
//	});
});