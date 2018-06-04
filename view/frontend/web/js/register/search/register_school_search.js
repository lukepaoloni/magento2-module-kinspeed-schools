define([
	'jquery',
	'register_school_search',
	'jquery/ui'
], function ( $, config ) {
	return function (config) {
		$(document).ready(function (  ) {
			var $schoolSearch = $('#school-finder__search'),
			    $results = $('.results'),
			    cache = {},
			    $schoolNameInput = $('#school_name'),
			    $streetInput = $('#street'),
			    $cityInput = $('#city'),
			    $postcodeInput = $('#postcode');
			
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
				},
				select: function ( event, ui ) {
					console.log(ui.item);
					$schoolNameInput.val(ui.item.name);
					$streetInput.val(ui.item.address);
					$cityInput.val(ui.item.city);
					$postcodeInput.val(ui.item.postcode);
				}
			});
		});
	};
});