function initializeMap() {
	  var mapOptions = {
    center: new google.maps.LatLng(49.067798, 1.756323),
    zoom: 8,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };

	maps.initialize('maps_mariage', mapOptions);
}

$(function() {
	$('<script>')
		.attr('type', 'text/javascript')
		.attr('src', maps.generateUrl(initializeMap))
		.appendTo('body');

	var mapsImageVisible = true;
	$('#switchMaps').click(function() {
		mapsImageVisible = !mapsImageVisible;
		$('#mapsImage').css('visibility', mapsImageVisible ? 'visible' : 'hidden');
	});
});