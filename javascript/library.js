var library  = {
	parseJSON: function(data) {
		var json = {};
		if ('{}' == data) {
			return json;
		}
		
		var items = data.split(/,/);
		for (var i in items) {
			var matches = /"([^"]+)" *: *"([^"]*)"/.exec(items[i]);
			json[matches[1]] = matches[2];
		}
		return json;
	},
	
	Filtre: function (container, elements) {
		container.append('<span id="searchForm" style="display: none">'
				+ '<input id="searchItem" value=""/>'
				+ '<button id="resetSearch" class="btn">Reset</button>'
			 + '</span>'
			+ '<span id="loupe" class="btn btn btn-info">Rechercher quelqu\'un</span>');
		
		this._filtre = container.find('#searchItem');
		this._elementsAFiltrer = elements;
		
		var object = this;
		
		this._filtre.keyup(function() { object.filtrer(); });
		
		container.find('#resetSearch').click(function() {
			object.resetFiltre(); });
		container.find('#loupe').click(function() {
			var visible = false;
			var form = $('#searchForm');
			var button = $('#loupe');
			
			return function () {
				if (visible) {
					object.resetFiltre();
					form.hide();
					button.text("Rechercher quelqu'un");
				}
				else {
					form.show();
					button.text('Cacher');
					object.focus();
				}
				visible = !visible;
			}
		}());
	},
	
	ajax: function(params) {
		var actions = {
				url: params.url,
				data: params.data };
		// Ajout du param pour identifier la requête comme ajax
		actions.data['__ajax__'] = 1;
		
		// Ajout d'un traitement post-requête
		if ('function' == typeof params.success) {
			actions['success'] = function(dataJSON) {
				var data = library.parseJSON(dataJSON); 
				params.success(data);
			};
		}
		
		if (params.debug) {
			console.log(actions);
		}
		
		// Envoi de la requête
		jQuery.ajax(actions);
	}
}

library.Filtre.prototype = {	
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
	},
	
	focus: function() {
		this._filtre.focus();
	}
};