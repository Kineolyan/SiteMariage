$(function() {
	var indiceParticipant, participantHtml;
	
	function getContainer(bouton) {
		return bouton.parent().parent();
	}
	
	function ajouterBoutons(container) {
		var plus = $("<span class='plus'><img src='data/plus.gif'/></span>").click(addParticipant),
			moins = $("<span class='moins'><img src='data/moins.gif'/></span>").click(removeParticipant),
			boutons = $('<div class="plus_moins"></div>')
				.append(plus).append(moins);

		return container.append(boutons);
	}
	
	function removeParticipant() {
		var container = getContainer($(this));
		
		if (1 < indiceParticipant){
			container.remove();
			--indiceParticipant
		}
	}
	
	function addParticipant() {
		var container = getContainer($(this));
		
		var part = $(participantHtml);
		container.after(ajouterBoutons(part));
		++indiceParticipant;
	}
	
	
	participantHtml = $('#registration .participant:last')[0].outerHTML;
	$('#registration .participant:last').after(participantHtml);
	indiceParticipant = $('#registration').children('.participant').size();
	
	$('#registration .participant').each(function() {
		ajouterBoutons($(this));
	});
	
	// Valider la soumission
	var formulaire = $('#registration');
	formulaire.submit(function() {
		var valid = true;
		var erreurs = [];
		if (0 == this.responsable.value && '' == this.nouveauLogin.value) {
			valid = false;
			erreurs.push('Il manque un responsable.');
		}
		
		var compteur = 0;
		formulaire.find(':input').each(function() {
			var element = $(this);
			
			if (/nom/.test(element.attr('name'))) {
				if ('' == element.attr('value')) {
					++compteur;
				}
			}
		});
		
		if (0 < compteur) {
			erreurs.push(compteur + ' champs de nom/prenom sont vides.');
			valid = false;
		}

		if (0 < erreurs.length) {
			for (var index in erreurs) {
				formulaire.before('<div class="alert alert-error">'
					+'<button class="close" data-dismiss="alert">×</button>'
					+'<strong>Erreur!</strong> ' + erreurs[index]
					+'</div>');
			}
		}
		
		return valid;
	})
});