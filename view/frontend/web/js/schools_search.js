define( [
		'jquery',
		'schools_countries',
		'schools_search',
		'jquery/ui'
	],
	function ( $, country_list ) {
		
		return {
			geocoderObject: function () {
				return new google.maps.Geocoder();
			},
			address: function () {
				return $( "#school-finder__search" ).val()
			},
			getCountryCode: function () {
				name = this.address();
				for ( var i = 0, len = country_list.length; i < len; i++ ) {
					if ( country_list[i].name.toUpperCase() == name.toUpperCase() ) {
						return country_list[i].code
					}
				}
			},
			autocomplete: function ( map, config, markers ) {
				
				$( '#school-finder__search' ).autocomplete( {
					source: function ( request, response ) {
						getSchools( request, response );
					},
					minLength: 3,
					select: function(event, ui) {
						map.setCenter({
								lat: parseFloat(ui.item.latitude),
								lng: parseFloat(ui.item.longitude)
						});
						var id = ui.item.latitude + ui.item.longitude;
						for ( i = 0; i < markers.length; i++ ) {
							if ( markers[i].record_id == id ) {
								google.maps.event.trigger( markers[i], 'click' );
							}
						}
					},
					open: function () {
						$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
					},
					close: function () {
						$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
					}
				} );
				
				function getSchools ( request, response ) {
					var url = window.location.protocol + "//" + window.location.hostname + window.location.pathname;
					url     = (url.substr( -1 ) != '/' ? url + '/' : url);
					$.ajax( {
						dataType: 'json',
						url: url,
						data: {
							term: request.term
						},
						success: function ( data ) {
							response( data );
						}
					} );
				}
			},
			search: function ( map, config ) {
				
				var geocoder = this.geocoderObject();
				
				$( ".map-search__results" ).empty();
				$( ".map-search__results" ).append( "<span class='results-word'>Results for <span class='italic'>" + this.address() + ":</span></span><br />" );
				
				var code_country = this.getCountryCode();
				geocoder.geocode(
					{ 'address': this.address() },
					function ( results, status ) {
						if ( status == google.maps.GeocoderStatus.OK ) {
							if ( results[0] ) {
								if ( results[0]["types"][0] == "country" ) {
									map.setZoom( config.zoom );
									map.setCenter( results[0].geometry.location );
									var marker = new google.maps.Marker( {
										map: map,
										position: results[0].geometry.location
									} );
									for ( i = 0; i < markers.length; i++ ) {
										if ( markers[i].global_country == code_country ) {
											if ( config.unit == "default" ) {
												var school_distance = parseFloat( distance * 0.000621371192 ).toFixed( 2 );
												var unitOfLength   = "miles";
											} else if ( config.unit == "kilometres" ) {
												var school_distance = parseFloat( distance * 0.001 ).toFixed( 2 );
												var unitOfLength   = "kilometres";
											}
											var contentToAppend = "<div class='results-content border' data-miles='" + school_distance + "' data-marker='" + markers[i].record_id + "'><p class='results-title'>" + markers[i].global_name + "</p>";
											if ( markers[i].global_address ) {
												contentToAppend += "<p class='results-address'>" + markers[i].global_address + "</p>";
											}
											if ( markers[i].global_city ) {
												contentToAppend += "<p class='data-phone'>" + markers[i].global_city + " " + markers[i].global_postcode + "</p>";
											}
											contentToAppend += "</div>";
											$( ".map-search__results" ).append( contentToAppend );
										}
									}
								}
								else {
									map.setZoom( config.zoom );
									map.setCenter( results[0].geometry.location );
									var marker = new google.maps.Marker( {
										map: map,
										position: results[0].geometry.location
									} );
									var circle = new google.maps.Circle( {
										map: map,
										radius: config.radius,    // value from admin settings
										fillColor: config.fillColor,
										fillOpacity: config.fillOpacity,
										strokeColor: config.strokeColor,
										strokeOpacity: config.strokeOpacity,
										strokeWeight: config.strokeWeight
									} );
									circle.bindTo( 'center', marker, 'position' );
									for ( i = 0; i < markers.length; i++ ) {
										var distance = google.maps.geometry.spherical.computeDistanceBetween( marker.position, markers[i].position );
										if ( distance < config.radius ) {
											if ( config.unit == "default" ) {
												var school_distance = parseFloat( distance * 0.000621371192 ).toFixed( 2 );
												var unitOfLength   = "miles";
											} else if ( config.unit == "kilometres" ) {
												var school_distance = parseFloat( distance * 0.001 ).toFixed( 2 );
												var unitOfLength   = "kilometres";
											}
											var contentToAppend = "<div class='results-content border' data-miles='" + school_distance + "' data-marker='" + markers[i].record_id + "'><p class='results-title'>" + markers[i].global_name + "</p>";
											if ( markers[i].global_address ) {
												contentToAppend += "<p class='results-address'>" + markers[i].global_address + "</p>";
											}
											if ( markers[i].global_city ) {
												contentToAppend += "<p class='data-phone'>" + markers[i].global_city + " " + markers[i].global_postcode + "</p>";
											}
											contentToAppend += "<p class='data-miles'>" + school_distance + " " + unitOfLength + "</p></div>";
											$( ".map-search__results" ).append( contentToAppend );
										}
									}
									var $wrapper = $( '.map-search__results' );
									
									//sort the result by distance
									$wrapper.find( '.results-content' ).sort( function ( a, b ) {
										return +a.dataset.miles - +b.dataset.miles;
									} )
										.appendTo( $wrapper );
								}
							}
						}
						else {
							alert( "No schools near your location." );
						}
					}
				);
			}
			
		}
		
	}
);
