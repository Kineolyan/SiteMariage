var maps = {

	initialize: function(mapId, params) {
	  var map = new google.maps.Map(document.getElementById(mapId), params);
	},

	generateUrl: function (callback) {
	  var googleAPIKey = 'AIzaSyBoH6IXAL-7Uy6aXffwu6WlBxzR8-alo_E',
	  		enableSensor = true;

	  return 'https://maps.googleapis.com/maps/api/js?key=' 
	    + googleAPIKey + '&sensor=' + (enableSensor ? 'true': 'false') + '&callback=' + callback;
	}

}