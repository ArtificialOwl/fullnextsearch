<?php
/**
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

namespace OCA\FullNextSearch;


use OCA\FullNextSearch\Model\DocumentAccess;

interface INextSearchPlatform {

	/**
	 * Load the search provider
	 */
	public function load();

	/**
	 * must returns a unique Id
	 *
	 * @return string
	 */
	public function getId();


	/** on first use */
	public function create();


	/** on test */
	public function test();


	/** on upgrade */
	public function upgrade();


	/**
	 * @param INextSearchProvider $provider
	 */
	public function initProvider(INextSearchProvider $provider);


	/**
	 * @param INextSearchProvider $provider
	 * @param NextSearchDocument[] $documents
	 */
	public function indexDocuments(INextSearchProvider $provider, $documents);


	/**
	 * @param INextSearchProvider $provider
	 * @param NextSearchDocument $document
	 */
	public function indexDocument(INextSearchProvider $provider, NextSearchDocument $document);

	/**
	 * @param INextSearchProvider $provider
	 * @param DocumentAccess $access
	 * @param string $string
	 *
	 * @return NextSearchDocument[]
	 */
	public function search(INextSearchProvider $provider, DocumentAccess $access, $string);

}