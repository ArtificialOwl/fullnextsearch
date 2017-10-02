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


const nextsearch = OCA.NextSearch.api;


var elements = {
	search_input: null,
	search_result: null,
	search_submit: null
};

const Example = function () {
	this.init();
};

Example.prototype = {

	init: function () {
		var self = this;

		elements.search_input = $('#search_input');
		elements.search_submit = $('#search_submit');
		elements.search_result = $('#search_result');

		elements.search_submit.on('click', function () {
			nextsearch.search('files', elements.search_input.val(), self.searchResult);
		});
	},

	searchResult: function (result) {
		elements.search_result.text(JSON.stringify(result));
	}

};


OCA.NextSearch.Example = Example;

$(document).ready(function () {
	OCA.NextSearch.example = new Example();
});



