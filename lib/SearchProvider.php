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

use OC\Search\SearchResult;
use OCA\FullNextSearch\Service\SearchService;
use OCP\Search\Provider;

class SearchProvider extends Provider {

	/** @var string */
	private $userId;

	/** @var SearchService */
	private $searchService;


	function __construct(array $options = array()) {
		parent::__construct($options);

		$this->searchService = \OC::$server->query(SearchService::class);
		$user = \OC::$server->getUserSession()
							->getUser();

		if ($user !== null) {
			$this->userId = $user->getUID();
		}
	}

	/**
	 * Search for files and folders matching the given query
	 *
	 * @param string $query
	 *
	 * @return SearchResult[]
	 */
	public function search($query) {
		$result = [];

		if ($this->userId === null) {
			return $result;
		}

		$documents = $this->searchService->search($this->getProviderId(), 'cult', $query);

		foreach ($documents as $document) {
			$item = new SearchResult($document->getId());
			$item->setTitle('this is the name');
			//$item->type = 'folder';
			$result[] = $item;
		}

		\OC::$server->getLogger()
					->log(4, json_encode($result));
		return $result;
	}




	private function getProviderId() {

		$providerId = $this->getOption('provider_id');

		if ($providerId === null) {
			return '_all';
		}

		return $providerId;
	}
}