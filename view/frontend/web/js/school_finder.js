define([
	'jquery',
	'school_finder',
	'jquery/ui',
], function ( $, config ) {
	return function(config) {
		$(document).ready(function (  ) {
			var $schoolSearch = $('#school-finder__search'),
			    $results = $('.results'),
			    cache = {};
			
			$schoolSearch.autocomplete({
				minLength: 3,
				source: function ( request, response ) {
					var term = request.term;
					var isAjax = true;
					if (term in cache) {
						response(cache[term]);
						return;
					}
					$.getJSON(config.url, request, function ( data, status, xhr, isAjax ) {
						cache[term] = data;
						response(data);
					});
				}
			});
		});
	};
});