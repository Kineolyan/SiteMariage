$(function() {
	var indiceParticipant, participantHtml, participantId;
	
	function getContainer(bouton) {
		return bouton.parent().parent();
	}
	
	function ajouterBoutons(container) {
		var plus = $("<span class='plus'><img src='data/plus.gif'/></span>").click(addParticipant),
			moins = $("<span class='moins'><img src='data/moins.gif'/></span>").click(removeParticipant),
			plusUn = $('<i class="icon-avecPlusUn"></i>').click(togglePlus),
			boutons = $('<div class="plus_moins"></div>')
				.append(plus).append(moins).append(plusUn);

		return container.prepend(boutons);
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
		
		var part = $(participantHtml.replace(/\[\d*\]/g, '['+ (participantId++) +']'));
		
		// Fermer le panel plusUn
		part.find('div.plusUn').hide();
		
		// Ajouter les boutons
		container.after(ajouterBoutons(part));
		
		++indiceParticipant;
	}
	
	function togglePlus() {
		var container = getContainer($(this));
		
		var duration = 300;
		container.find('div.otherParams').slideToggle(duration);
		container.find('div.plusUn').slideToggle(duration);
	}
	
	
	participantHtml = $('#registration .participant:last')[0].outerHTML;
	indiceParticipant = $('#registration').children('.participant').size();
	participantId = indiceParticipant;
	
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
			
			if (/plusUn/.test(element.attr('name'))) {
				return;
			}
			
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
	});
	formulaire.find('div.plusUn').hide();
});