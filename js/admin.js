

/** global: OC */

var elements = {
	test_fullnextsearch: null
};

$(document).ready(function () {

	elements.test_fullnextsearch = $('#test_fullnextsearch');
	elements.test_fullnextsearch.on('change', function () {
		saveChange();
	});


	saveChange = function () {
		$.ajax({
			method: 'POST',
			url: OC.generateUrl('/apps/fullnextsearch/settings/admin'),
			data: {
				data: {
					test_fullnextsearch: (elements.test_fullnextsearch.is(':checked')) ? '1' : '0'
				}
			}

		}).done(function (res) {
			self.refreshSettings(res);
		});
	};

	refreshSettings = function (result) {
		elements.test_fullnextsearch.prop('checked', (result.test_fullnextsearch === '1'));
	};

	$.ajax({
		method: 'GET',
		url: OC.generateUrl('/apps/fullnextsearch/settings/admin'),
		data: {}
	}).done(function (res) {
		self.refreshSettings(res);
	});


});