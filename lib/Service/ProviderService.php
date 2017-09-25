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

use OC\App\AppManager;
use OC_App;
use OCA\FullNextSearch\Exceptions\NextSearchProviderInfoException;
use OCA\FullNextSearch\Exceptions\NextSearchProviderIsNotCompatibleException;
use OCA\FullNextSearch\INextSearchProvider;

class ProviderService {

	/** @var AppManager */
	private $appManager;

	/** @var MiscService */
	private $miscService;

	/**
	 * @var INextSearchProvider[]
	 */
	private $providers = [];

	/**
	 * ProviderService constructor.
	 *
	 * @param AppManager $appManager
	 * @param MiscService $miscService
	 */
	function __construct(AppManager $appManager, MiscService $miscService) {
		$this->appManager = $appManager;
		$this->miscService = $miscService;

		$this->loadProviders();
	}


	/**
	 * Load all NextSearchProviders set in any info.xml file
	 */
	public function loadProviders() {
		$apps = $this->appManager->getInstalledApps();
		foreach ($apps as $appId) {
			$this->loadProviderFromApp($appId);
		}
	}


	/**
	 * @param string $providerId
	 *
	 * @throws NextSearchProviderIsNotCompatibleException
	 */
	public function loadProvider($providerId) {

		$provider = \OC::$server->query((string)$providerId);
		if (!($provider instanceof INextSearchProvider)) {
			throw new NextSearchProviderIsNotCompatibleException(
				$providerId . ' is not a compatible NextSearchProvider'
			);
		}

		$this->providers[] = $provider;
	}


	/**
	 * @param $userId
	 */
	public function indexContentFromUser($userId) {
		foreach ($this->providers AS $provider) {
			$provider->index($userId);
		}

	}


	/**
	 * @param $appId
	 *
	 * @throws NextSearchProviderInfoException
	 */
	private function loadProviderFromApp($appId) {
		$appInfo = OC_App::getAppInfo($appId);
		if (!key_exists('fullnextsearch', $appInfo)) {
			return;
		}

		if (!key_exists('provider', $appInfo['fullnextsearch'])) {
			throw new NextSearchProviderInfoException('wrong syntax in ' . $appId . ' info.xml');
		}

		$providers = $appInfo['fullnextsearch']['provider'];
		$this->loadProvidersFromList($providers);
	}


	/**
	 * @param string|array $providers
	 */
	private function loadProvidersFromList($providers) {
		if (!is_array($providers)) {
			$providers = [$providers];
		}

		foreach ($providers AS $provider) {
			$this->loadProvider($provider);
		}
	}

}