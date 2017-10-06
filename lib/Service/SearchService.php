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

namespace OCA\FullNextSearch\Service;

use OCA\FullNextSearch\Model\DocumentAccess;
use OCA\FullNextSearch\Model\SearchResult;


class SearchService {

	/** @var string */
	private $userId;

	/** @var ConfigService */
	private $configService;

	/** @var ProviderService */
	private $providerService;

	/** @var PlatformService */
	private $platformService;

	/** @var MiscService */
	private $miscService;


	/**
	 * IndexService constructor.
	 *
	 * @param string $userId
	 * @param ConfigService $configService
	 * @param ProviderService $providerService
	 * @param PlatformService $platformService
	 * @param MiscService $miscService
	 */
	function __construct(
		$userId, ConfigService $configService, ProviderService $providerService,
		PlatformService $platformService, MiscService $miscService
	) {
		$this->userId = $userId;
		$this->configService = $configService;
		$this->providerService = $providerService;
		$this->platformService = $platformService;
		$this->miscService = $miscService;
	}

//				echo memory_get_usage() . "\n";


	/**
	 * @param string $providerId
	 * @param string $userId
	 * @param string $search
	 *
	 * @return SearchResult[]
	 */
	public function search($providerId, $userId, $search) {

		if ($userId === null) {
			$userId = $this->userId;
		}

		$providers = $this->providerService->getFilteredProviders($providerId);
		$platform = $this->platformService->getPlatform();

		$access = $this->getDocumentAccessFromUser($userId);
		$result = [];
		foreach ($providers AS $provider) {
			$searchResult = $platform->search($provider, $access, $search);
			$searchResult->setProvider($provider);

			$provider->parseSearchResult($searchResult);
			if (sizeof($searchResult->getDocuments()) > 0) {
				$result[] = $searchResult;
			}
		}

		return $result;
	}


	/**
	 * @param $userId
	 *
	 * @return DocumentAccess
	 */
	private function getDocumentAccessFromUser($userId) {
		$rights = new DocumentAccess();

		$rights->setViewer($userId);
		$rights->setCircles(['qwerty', '12345']);
		$rights->setGroups(['group1', 'group2']);

		return $rights;
	}


}