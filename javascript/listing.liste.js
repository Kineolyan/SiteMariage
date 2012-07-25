function parseJSON(data) {
	var json = {};
	var items = data.split(/,/);
	for (var i in items) {
		var matches = /\"([\w-_éèà]+)\":\"([\w-_éèà]+)\"/.exec(items[i]);
		json[matches[1]] = matches[2];
	}
	return json;
}

function majStatut() {
	var liElt = $(this);
	var statut = liElt.parent().parent().find('span');
	var btn = liElt.parent().parent().find('.btn');

	if (statut.text() != liElt.text()) {
		$.ajax({
			url: 'listing.php',
			data: { ajax: 1, id:statut.attr('statusId'), 
				oldStatus: statut.text(), newStatus:liElt.text() },
			success: function(dataJSON) {
				var data = parseJSON(dataJSON); 
				statut.text(data.updatedStatus);
				btn.removeClass(data.oldClass).addClass(data.newClass);
			}});
	}
}

function updateStatus() {
	var select = $(this);
	var nameAttribute = select.attr('name');
	var selectElements = /status_(\d+)/.exec(nameAttribute);
	if (null!=selectElements) {
//		$.ajax({
//			  url: 'listing.php',
//			  data: { ajax: 1, id:selectElements[1], status:select.val() },
//			  success: function(data) {
//				  $('span.' + nameAttribute).text(data);
//			  },
//			  error: function(xhr) { console.log("error"); console.log(xhr); }
//			});
		
		$.getJSON('listing.php', 
		  { ajax: 1, id:selectElements[1], status:select.val() },
		  function(data) {
			  $('span.' + nameAttribute).text(data);
		  }
		);
	}
}

function Filtre(filtre, elements) {
	this._filtre = filtre;
	this._elementsAFiltrer = elements;
	var object = this;
	
	this._filtre.keyup(function() { object.filtrer(); });
}
Filtre.prototype = {	
	resetFiltre: function() {
		this._elementsAFiltrer.show();
		this._filtre.val("");
	},
	
	rechercher: function(item) {
		var expr = new RegExp(item, 'i');
			
		this._elementsAFiltrer.each(function() {
			var ligne = $(this);
			if (expr.exec(ligne.text())) {
				ligne.show();
			}
			else {
				ligne.hide();
			}
		});
	},
	
	filtrer: function() {
		var recherche = this._filtre.val();
		
		if (2 < recherche.length) {
			this.rechercher(recherche);
		}
		else if (0 == recherche.length) {
			this.resetFiltre();
		}
	}
};

$(function() {
	$('.btn-group li').click(majStatut);
	$("#invites").tablesorter({sortList: [[0,0], [1,0]], headers: { 2:{sorter: false}, }});
	var filtre = new Filtre($('#searchItem'), $('#invites tbody tr'));
	$('#resetSearch').click(function() { filtre.resetFiltre(); });
	$('#loupe').click(function() {
		var visible = false;
		var form = $('#searchForm');
		var button = $(this);
		
		return function () {
			if (visible) {
				filtre.resetFiltre();
				form.hide();
				button.text('Cacher');
			}
			else {
				form.show();
				button.text("Rechercher quelqu'un");
			}
			visible = !visible;
		}
	}());
});