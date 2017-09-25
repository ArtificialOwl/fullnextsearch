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

use \Exception;

class IndexService {


	const INDEX_CHUNK_SIZE = 3;

	/** @var ProviderService */
	private $providerService;

	/** @var ElasticService */
	private $elasticService;

	/** @var MiscService */
	private $miscService;


	/**
	 * IndexService constructor.
	 *
	 * @param ProviderService $providerService
	 * @param ElasticService $elasticService
	 * @param MiscService $miscService
	 */
	function __construct(
		ProviderService $providerService, ElasticService $elasticService, MiscService $miscService
	) {
		$this->providerService = $providerService;
		$this->elasticService = $elasticService;
		$this->miscService = $miscService;
	}


	/**
	 * @param $userId
	 */
	public function indexContentFromUser($userId) {
		$this->providerService->loadProviders();

		echo '. userId: ' . $userId . "\n";
		foreach ($this->providerService->getProviders() AS $provider) {

			echo '  . provider:' . $provider->getId() . "\n";
			$provider->initUser($userId);
			for ($i = 0; $i < 1000; $i++) {
				try {
					$items = $provider->generateIndex(self::INDEX_CHUNK_SIZE);
					$this->elasticService->indexItems($items);
				} catch (Exception $e) {
					continue(2);
				}
			}

			$provider->endUser();
		}

	}


}