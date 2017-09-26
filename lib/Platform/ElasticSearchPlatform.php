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

namespace OCA\FullNextSearch\Platform;

use OCA\FullNextSearch\AppInfo\Application;
use OCA\FullNextSearch\INextSearchDocument;
use OCA\FullNextSearch\INextSearchPlatform;
use OCA\FullNextSearch\INextSearchProvider;
use OCA\FullNextSearch\Service\MiscService;

class ElasticSearchPlatform implements INextSearchPlatform {

	/** @var MiscService */
	private $miscService;


	/**
	 * {@inheritdoc}
	 */
	public function load() {
		$app = new Application();

		$container = $app->getContainer();
		$this->miscService = $container->query(MiscService::class);
	}


	/**
	 * {@inheritdoc}
	 */
	public function indexDocuments(INextSearchProvider $provider, $documents) {
		foreach ($documents as $item) {
			echo ' < ' . $item->getId() . "\n";
		}
	}


	public function indexDocument(INextSearchProvider $provider, INextSearchDocument $document) {

	}


	/**
	 * {@inheritdoc}
	 */
	public function getId() {
		return 'elastic_search';
	}


	/**
	 * {@inheritdoc}
	 */
	public function create() {
	}


	/**
	 * {@inheritdoc}
	 */
	public function upgrade() {
	}




	/**
	 * {@inheritdoc}
	 */
	public function search($string) {
	}
}