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
				'Failed to connect to cloud, page will be refresh within few seconds');
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

			var divProvider = nav.getDivProvider(providerId, result.provider.name);

			nav.managerDivProviderResult(divProvider.children('.provider_result'), result.documents,
				current.documents);

			divProvider.slideDown(settings.delay_provider, function () {
				$(this).fadeTo(settings.delay_provider, 1);
			});

			curr.providerResult[providerId] = result;
		},


		managerDivProviderResult: function (divProvider, newResult, oldResult) {
			nav.divProviderResultAddItems(divProvider, newResult, oldResult);
			if (oldResult) {
				nav.divProviderResultRemoveItems(divProvider, newResult, oldResult);
			}

		},


		divProviderResultAddItems: function (divProviderResult, newResult, oldResult) {

			for (var i = 0; i < newResult.length; i++) {
				var entry = newResult[i];
				if (nav.resultExist(entry.id, oldResult)) {
					continue;
				}
				var divResultContent = nav.generateTemplateEntry(entry);
				divResultContent.fadeTo(0);

				var divResult = $('<div>', {class: 'result_entry'});
				divResult.hide();
				divResult.attr('data-id', entry.id);
				divResult.attr('data-result', JSON.stringify(entry));
				divResult.append(divResultContent);

				divProviderResult.append(divResult);
				divResult.slideDown(settings.delay_result, function () {
					$(this).children('.result_template').fadeTo(settings.delay_result, 1);
				});

			}
		},


		divProviderResultRemoveItems: function (divProviderResult, newResult, oldResult) {
			for (var i = 0; i < oldResult.length; i++) {
				var entry = oldResult[i];
				if (!nav.resultExist(entry.id, newResult)) {
					var divResult = nav.getDivResult(entry.id, divProviderResult);
					divResult.fadeTo(settings.delay_result, 0, function () {
						$(this).slideUp(settings.delay_result);
					});
//					divResult.slideDown(settings.delay_result);
				}
			}
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


		getDivProvider: function (providerId, providerName) {
			var ret = null;
			settings.resultContainer.children('.provider_header').each(function () {
				if ($(this).attr('data-id') === providerId) {
					ret = $(this);
				}
			});

			if (ret === null) {
				ret = nav.generateDivProvider(providerId, providerName);
				settings.resultContainer.append(ret);
			}

			return ret;
		},


		getDivResult: function (resultId, divProviderResults) {
			var ret = null;
			divProviderResults.children('.result_entry').each(function () {
				if ($(this).attr('data-id') === resultId) {
					ret = $(this);
				}
			});

			return ret;
		},


		generateTemplateEntry: function (document) {
			var divTemplate = settings.entryTemplate;
			if (divTemplate === null) {
				divTemplate = settings.entryTemplateDefault;
			}

			var tmpl = divTemplate.html();
			tmpl = tmpl.replace(/%%id%%/g, escapeHTML(document.id));
			var div = $('<div>', {class: 'result_template'});
			div.html(tmpl);

			return div;
		},


		generateDivProvider: function (providerId, providerName) {
			var divProviderName = $('<div>', {class: 'provider_name'});
			divProviderName.text(providerName);

			var divProviderResult = $('<div>', {class: 'provider_result'});
			var divProvider = $('<div>', {class: 'provider_header'});
			divProvider.hide();
			divProvider.attr('data-id', providerId);
			divProvider.append(divProviderName);
			divProvider.append(divProviderResult);

			return divProvider;
		}

	}
;
