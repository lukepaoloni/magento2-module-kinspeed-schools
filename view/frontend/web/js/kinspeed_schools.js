define( [
		'jquery',
		'kinspeed_schools',
		'schools_countries',
		'schools_mapstyles',
		'schools_search',
		'schools_geolocation',
		'jquery/ui'
	],
	function ( $, config, country_list, mapstyles, search_widget, currentLocation ) {
		
		return function ( config ) {
			
			$( document ).ready( function () {
				
				$.getScript( "https://maps.googleapis.com/maps/api/js?v=3&sensor=false&key=" + config.apiKey + "&libraries=geometry,places", function () {
					getSchools();
				} );
				
				var map;
				markers = [];
				
				
				$( "body" ).on( "click", ".results-content", function () {
					$( ".results-content" ).not( $( this ) ).removeClass( "active" );
					$( this ).addClass( "active" );
				} );
				
				// get the stores from admin school/ajax/stores
				function getSchools () {
					var url = config.schools_url;
					
					$.ajax( {
						dataType: 'json',
						url: url
					} ).done( function ( response ) {
						initialize( response );
					} );
				}
				
				function initialize ( response ) {
					
					var mapElement      = document.getElementById( 'map' );
					var loadedMapStyles = mapstyles[config.map_styles];
					var mapOptions      = {
						zoom: config.zoom,
						scrollwheel: false,
						center: { lat: config.latitude, lng: config.longitude },
						styles: loadedMapStyles
					};
					
					map                   = new google.maps.Map( mapElement, mapOptions );
					var directionsService = new google.maps.DirectionsService();
					var directionsDisplay = new google.maps.DirectionsRenderer();
					directionsDisplay.setMap( map );
					
					var image      = {
						url: config.map_pin
					};
					var infowindow = new google.maps.InfoWindow( {
						content: ""
					} );
					
					function bindInfoWindow ( marker, map, infowindow, name, address, city, postcode, telephone, link, external_link, email ) {
						google.maps.event.addListener( marker, 'click', function () {
							var contentString = '<div class="school-window" data-latitude="' + marker.getPosition().lat() + '" data-longitude="' + marker.getPosition().lng() + '"><p class="school-title">' + name + '</p>'
							if ( external_link && !external_link.includes( "NULL" ) ) {
								contentString += '<p class="school-telephone"><a href="' + external_link + '" target="_blank">' + external_link + '</a></p>'
							}
							if ( telephone ) {
								contentString += '<p class="school-telephone">' + telephone + '</p>';
							}
							if ( email ) {
								contentString += '<p class="school-address"><a href="mailto:' + email + '" target="_blank">' + email + '</a></p>';
							}
							if ( address ) {
								contentString += '<p class="school-telephone">' + address + '</p>'
							}
							if ( city ) {
								contentString += '<p class="school-telephone">' + city + '</p>'
							}
							if ( postcode ) {
								contentString += '<p class="school-web">' + postcode + '</p>';
							}
							
							if ( !link.includes( "schools/register/interest" ) ) {
								contentString += '<a href="' + link + '" class="btn btn-secondary">View Store</a>';
							} else {
								contentString += '<a href="' + link + '" class="btn btn-primary">Register Interest</a>';
							}
							contentString += '</div>';
							map.setCenter( marker.getPosition() );
							infowindow.setContent( contentString );
							infowindow.open( map, marker );
						} );
					}
					
					var length = response.length;
					
					for ( var i = 0; i < length; i++ ) {
						
						var data = response[i];
						
						var latLng = new google.maps.LatLng( data.latitude,
							data.longitude );
						
						var record_id = "" + data.latitude + data.longitude;
						
						var marker = new google.maps.Marker( {
							record_id: record_id,
							global_name: data.name,
							global_address: data.address,
							global_city: data.city,
							global_postcode: data.postcode,
							global_country: data.country,
							position: latLng,
							map: map,
							icon: image,
							title: data.name
						} );
						markers.push( marker );
						
						bindInfoWindow( marker, map, infowindow, data.name, data.address, data.city, data.postcode, data.phone, data.link, data.external_link, data.email );
						
					}
					
					if ( config.geolocation && navigator.geolocation ) {
						
						getGeoLocation( map );
						
					}
					
					// on click location ask for geolocation and show stores
					if ( navigator.geolocation ) {
						$( document ).on( "click", ".geocode-location", function () {
							if (location.protocol != 'https:') {
								location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
							}
							getGeoLocation( map );
						} )
					}
					
					// attach click events for directions
					if ( navigator.geolocation ) {
						$( document ).on( "click", ".get-directions", function () {
							var storeDirections = {
								latitude: $( ".school-window" ).attr( "data-latitude" ),
								longitude: $( ".school-window" ).attr( "data-longitude" )
							};
							var userTravelMode  = $( this ).attr( "data-directions" );
							
							getGeoLocation( map, storeDirections, userTravelMode, directionsService, directionsDisplay );
							
						} )
					}
					
					
				}
				
				//gets geolocation, if storeDirections is set then it is interpreted as a way to getDirection
				function getGeoLocation ( map, storeDirections, userTravelMode, directionsService, directionsDisplay ) {
					var geoOptions = function () {
						return {
							maximumAge: 5 * 60 * 1000,
							timeout: 10 * 1000
						}
					};
					
					var geoSuccess = function ( position ) {
						
						// if no params then just center it, otherwise call directions
						if ( typeof storeDirections === 'undefined' ) {
							
							centerMap( position.coords, map, markers );
							
						}
						else {

							getDirections( map, storeDirections, position.coords, userTravelMode, directionsService, directionsDisplay );

						}
						
					};
					var geoError   = function ( position ) {
						
						return;
						
					};
					
					navigator.geolocation.getCurrentPosition( geoSuccess, geoError, geoOptions );
				}
				
				$( "body" ).on( "click", ".results-content", function () {
					var id = $( this ).attr( 'data-marker' );
					changeMarker( id );
				} );
				
				function changeMarker ( id ) {
					for ( i = 0; i < markers.length; i++ ) {
						if ( markers[i].record_id == id ) {
							google.maps.event.trigger( markers[i], 'click' );
						}
					}
				}
				
				//after the user has shared his geolocation, center map, insert marker and show stores
				function centerMap ( coords, map, markers ) {
					
					var latLng = new google.maps.LatLng( coords.latitude, coords.longitude );
					
					currentLocation.search( map, coords, latLng, config );
					
				}
				
				//get driving directions from user location to store
				function getDirections ( map, storeDirections, userLocation, userTravelMode, directionsService, directionsDisplay ) {
					
					if ( typeof userTravelMode === 'undefined' ) {
						var directionsTravelMode = "DRIVING";
					} else {
						var directionsTravelMode = userTravelMode;
						
					}
					
					var request = {
						destination: new google.maps.LatLng( storeDirections.latitude, storeDirections.longitude ),
						origin: new google.maps.LatLng( userLocation.latitude, userLocation.longitude ),
						travelMode: google.maps.TravelMode[directionsTravelMode]
					};
					
					directionsService.route( request, function ( response, status ) {
						if ( status == google.maps.DirectionsStatus.OK ) {
							directionsDisplay.setDirections( response );
							directionsDisplay.setPanel( $( '.directions-panel' )[0] );
						}
					} );
					
					$( ".directions-panel" ).show();
					
					//on close reset map and panel and center map to user location
					$( "body" ).on( "click", ".directions-panel .close", function () {
						$( ".directions-panel" ).hide();
						directionsDisplay.setPanel( null );
						directionsDisplay.setMap( null );
						centerMap( userLocation, map, markers );
					} );
				}
				
				// on search show the relevant stores
				$( "#school-finder__submit" ).on( "click", function ( e ) {
					
					search_widget.search( map, config );
					
				} );
				
				$( '#school-finder__search' ).keypress( function ( e ) {
					
					search_widget.autocomplete( map, config, markers );
					
					if ( e.which == 13 ) {//Enter key pressed
						search_widget.search( map, config );
					}
					
				} );
				
			} );
		};
	}
);
