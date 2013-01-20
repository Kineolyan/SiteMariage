var library  = {
	extends: function(src, content) {
		if (content && src) {
			for (key in content) {
				src[key] = content[key];
			}
		}
	},

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

	Selector: function(container, elements, values, params) {
		if (undefined == params) {
			params = {};
		}

		var object = this;
		this._elementsAFiltrer = elements;
		this._values = {};
		for (key in values) {
			this._values[values[key]] = false;
		}

		this._attribut = params.attribut;
		var texts = { 
			select: "Sélectionner",
			hide: "Cacher",
			reset: "Reset"
		};
		library.extends(texts, params.texts);

		var selectorForm = $('<div class="selectorForm" style="display: none"></div>');
		var resetBtn = $('<button class="reset btn">' + texts.reset + '</button>');
		var loupeBtn = $('<span class="loupe btn btn-info">' + texts.select + '</span>');
		
		this._selections = [];
		var selectors = $('<ul class="selections"></ul>');
		var selectionCbk = function() { object.selectionner($(this)); };
		for (value in this._values) {
			var identifier = 'field' + value;
			var checkbox = $('<input class="selectorItem" type="checkbox" id="' + identifier 
				+ '" value="' + value + '"/>');
			var label = $('<label class="checkbox" for="' + identifier + '">' + value + '</label>');

			this._selections.push(checkbox);
			checkbox.change(selectionCbk);

			$('<li>').append(checkbox).append(label).appendTo(selectors);
		}
		
		selectorForm
			.append(resetBtn)
			.append(selectors);
		container
			.append(loupeBtn)
			.append(selectorForm);
		
		resetBtn.click(function() { object.resetSelector(); });
		loupeBtn.click(function() {
			var visible = false;
			
			return function () {
				if (visible) {
					selectorForm.hide();
					loupeBtn.text(texts.select);
				}
				else {
					selectorForm.show();
					loupeBtn.text(texts.hide);
				}
				visible = !visible;
			}
		}());

	},

	Modal: function(title) {
		var object = this;
		this.element = $('<div id="modalEditor" class="modal hide fade">');
		
		var header = $('<div class="modal-header">');
		header.append('<h3 id="myModalLabel">').text(title);
		var closeX = $('<button type="button" class="close">×</button>')
			.click(function() { object.close(); })
			.prependTo(header);

		var body = $('<div class="modal-body">');
		this.frame = $('<iframe>').appendTo(body);

		this.element.append(header).append(body)
			.appendTo('body');
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

library.Selector.prototype = {	
	resetSelector: function() {
		this._elementsAFiltrer.show();
		$.each(this._selections, function(index, checkbox) { checkbox.attr('checked', false); } );
		for (value in this._values) {
			this._values[value] = false;
		}
	},

	getData: function(item) {
		return this._attribut ? item.attr(this._attribut) : item.text();
	},
	
	selectionner: function(item) {
		this._values[item.val()] = item.is(':checked');
		console.log(this._values);

		var search = [];
		for (value in this._values) {
			if (this._values[value]) {
				search.push(value);
			}
		}

		var expr = new RegExp(search.join('|'), 'i');
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
	}
};

library.Modal.prototype = {
	show: function(url) {
		this.frame.attr('src', url);
		this.element.removeClass('hide fade');
	},

	close: function() {
		this.frame.attr('src', 'about:blank');
		this.element.addClass('hide fade');
	}
};