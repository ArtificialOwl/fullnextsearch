/*
 * FullNextSearch - Full Text Search your Nextcloud.
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@artificial-owl.com>
 * @copyright 2017
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 */

/** global: api */
/** global: search */


var curr = {
	providerResult: []
};


var nav = {

	displayResult: function (res) {

		if (Number(res.meta.size) < 1) {
			OCA.notification.onFail('Search returned no result');
			return;
		}

		var searchResult = res.result;

		for (var i = 0; i < searchResult.length; i++) {
			nav.displayProviderResult(searchResult[i]);
		}

		OCA.notification.onSuccess('Search returned ' + res.meta.size + ' result(s)');
	},


	failedToAjax: function () {
		OCA.notification.onSuccess(
			'Failed to connect to cloud, page will be refresh within the next seconds');
		window.setTimeout(function () {
			window.location.reload(true);
		}, 4000);
	},


	displayProviderResult: function (result) {

		if (settings.resultContainer === null) {
			return;
		}

		var providerId = result.provider.id;

		var current = curr.providerResult[providerId];
		if (!current) {
			current = [];
		}
		console.log('>> ' + JSON.stringify(result));

		var divProvider = nav.getDivProvider(providerId);
		if (divProvider === null) {
			divProvider = nav.generateDivProvider(providerId, result.provider.name);
			settings.resultContainer.append(divProvider);
		}

		nav.managerDivProviderResult(divProvider.children('.provider_result'), result.documents,
			current.documents);

		curr.providerResult[providerId] = result;
	},


	managerDivProviderResult: function (divProvider, newResult, oldResult) {
		nav.divProviderResultAddItems(divProvider, newResult, oldResult);
		nav.divProviderResultRemoteItems(divProvider, newResult, oldResult);

	},


	divProviderResultAddItems: function (divProviderResult, newResult, oldResult) {

		for (var i = 0; i < newResult.length; i++) {
			var entry = newResult[i];
			if (!nav.resultExist(entry.id, oldResult)) {
				var divResult = $('<div>', {class: 'result_entry'});
				divResult.attr('data-id', entry.id);
				divResult.attr('data-result', entry);
				divResult.html(nav.generateTemplateEntry(entry));
				divProviderResult.append(divResult);
			}
		}
	},


	divProviderResultRemoteItems: function (newResult, oldResult) {
	},


	resultExist: function (id, result) {
		if (!result) {
			return false;
		}

		for (var i = 0; i < result.length; i++) {
			if (result[i].id === id) {
				return true;
			}
		}

		return false;
	},


	getDivProvider: function (providerId) {
		var ret = null;
		settings.resultContainer.children('.provider_header').each(function () {
			if ($(this).attr('data-id') === providerId) {
				ret = $(this);
			}
		});

		return ret;
	},


	generateTemplateEntry: function (document) {
		var div = settings.entryTemplate;
		if (div === null) {
			div = settings.entryTemplateDefault;
		}

		var tmpl = div.html();
		tmpl = tmpl.replace(/%%id%%/g, escapeHTML(document.id));

		return tmpl;
	},


	generateDivProvider: function (providerId, providerName) {
		var divProviderName = $('<div>', {class: 'provider_name'});
		divProviderName.text(providerName);

		var divProvider = $('<div>', {class: 'provider_header'});
		divProvider.attr('data-id', providerId);
		divProvider.append(divProviderName);
		divProvider.append($('<div>', {class: 'provider_result'}));

		return divProvider;
	}

};
