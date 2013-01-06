var library  = {
	parseJSONArray: function(data) {
		console.log("parseJSONArray " + data);
		if ('[]' == data) {
			return [];
		}

		var items = data.substr(1, data.length - 2).split(/,/);
		console.log(">> " + items);
		for (var i in items) {
			items[i] = library.parseJSON(items[i]);
		}

		return items;
	},

	parseJSONObject: function(data) {
		console.log("parseJSONObject " + data);
		var json = {};
		if ('{}' == data) {
			return json;
		}

		var items = data.substr(1, data.length - 2).split(/,/);
		console.log(">> " + items);
		for (var i in items) {
			var matches = /"([^"]+)" *: *"([^"]*)*/.exec(items[i]);
			json[matches[1]] = library.parseJSON(matches[2]);
		}

		return json;
	},

	parseJSON: function(data) {
		if ("{" == data[0]) {
			return library.parseJSONObject(data);
		} else if ("[" == data[0]) {
			return library.parseJSONArray(data);
		} else {
			return data;
		}
	},
	
	Filtre: function (container, elements, attribut) {
		var object = this;
		this._filtre = $('<input class="searchItem" value=""/>');
		this._elementsAFiltrer = elements;
		this._attribut = attribut;

		var searchForm = $('<span class="searchForm" style="display: none"></span>');
		var resetBtn = $('<button class="reset btn">Reset</button>');
		var loupeBtn = $('<span class="loupe btn btn-info">Rechercher quelqu\'un</span>');
		
		searchForm
			.append(this._filtre)
			.append(resetBtn);
		container
			.append(searchForm)
			.append(loupeBtn);
		
		this._filtre.keyup(function() { object.filtrer(); });		
		resetBtn.click(function() { object.resetFiltre(); });
		loupeBtn.click(function() {
			var visible = false;
			
			return function () {
				if (visible) {
					object.resetFiltre();
					searchForm.hide();
					loupeBtn.text("Rechercher quelqu'un");
				}
				else {
					searchForm.show();
					loupeBtn.text('Cacher');
					object.focus();
				}
				visible = !visible;
			}
		}());
	},
	
	ajax: function(params) {
		var actions = {
			url: params.url,
			data: params.data,
			error: function() { console.log('error in ajax'); },
			success: params.success
		};
		// Ajout du param pour identifier la requête comme ajax
		actions.data['__ajax__'] = 1;

		if (params.debug) {
			console.log(actions);
		}
		
		// Envoi de la requête
		jQuery.ajax(actions);
	},
	
	json: function(params) {
		var actions = {
			url: params.url,
			data: params.data,
			error: function() { console.log('error in ajax'); }
		};
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
		var filter = this;

		filter._elementsAFiltrer.each(function() {
			var ligne = $(this);
			if (expr.exec(filter.getData(ligne))) {
				ligne.show();
			}
			else {
				ligne.hide();
			}
		});
	},

	getData: function(item) {
		return this._attribut ? item.attr(this._attribut) : item.text();
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