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
});