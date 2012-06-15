var indiceParticipant, participantHtml;

function removeParticipant() {
	if (1 < indiceParticipant){
		$('#registration .participant:last').remove();
		--indiceParticipant
	}
}

function addParticipant() {
	var part = participantHtml.clone();
	part.html(part.html().replace(
			/\[\d+\]/g, '['+indiceParticipant+']'));
	$('#registration .participant:last').after(part);
	++indiceParticipant;
}

$(function() {
	var form = $('#registration');
	indiceParticipant = form.children('.participant').size();
	participantHtml = $('#registration .participant:last').clone();
	
	// Ajouter un bouton pour plus de participants
	form.append("<span class='plus'>Ajouter</span>&nbsp;");
	form.children('.plus').click(addParticipant);
	form.append('<span class="moins">Supprimer</span>');
	form.children('.moins').click(removeParticipant);
});