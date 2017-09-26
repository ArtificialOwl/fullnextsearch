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

use Exception;
use OC\App\AppManager;
use OC_App;
use OCA\FullNextSearch\AppInfo\Application;
use OCA\FullNextSearch\Exceptions\NextSearchPlatformIsNotCompatibleException;
use OCA\FullNextSearch\Exceptions\NextSearchPlatformMustBeSingleException;
use OCA\FullNextSearch\Exceptions\NextSearchPlatformNotDefinedException;
use OCA\FullNextSearch\Exceptions\NextSearchProviderInfoException;
use OCA\FullNextSearch\Exceptions\NextSearchProviderIsNotCompatibleException;
use OCA\FullNextSearch\Exceptions\NextSearchProviderIsNotUniqueException;
use OCA\FullNextSearch\INextSearchPlatform;
use OCA\FullNextSearch\INextSearchProvider;

class PlatformService {

	/** @var MiscService */
	private $miscService;

	/** @var INextSearchPlatform */
	private $platform;


	/**
	 * ProviderService constructor.
	 *
	 * @param MiscService $miscService
	 *
	 * @throws Exception
	 */
	function __construct(MiscService $miscService) {
		$this->miscService = $miscService;
	}


	/**
	 * Load all NextSearchProviders set in any info.xml file
	 *
	 * @throws Exception
	 */
	public function loadPlatform() {
		if ($this->platform !== null) {
			return;
		}

		try {
			$platformId = $this->getPlatformFromInfoXml();
			$platform = \OC::$server->query((string)$platformId);
			if (!($platform instanceof INextSearchPlatform)) {
				throw new NextSearchPlatformIsNotCompatibleException(
					$platformId . ' is not a compatible NextSearchPlatform'
				);
			}

			$this->platform = $platform;
		} catch (Exception $e) {
			$this->miscService->log($e->getMessage());
			throw $e;
		}
	}


	private function getPlatformFromInfoXml() {
		$appInfo = \OC_App::getAppInfo(Application::APP_NAME);
		if (!key_exists('fullnextsearch', $appInfo)
			|| !key_exists(
				'platform', $appInfo['fullnextsearch']
			)) {
			throw new NextSearchPlatformNotDefinedException('no platform defined in info.xml');
		}

		$platformId = $appInfo['fullnextsearch']['platform'];
		if (is_array($platformId)) {
			throw new NextSearchPlatformMustBeSingleException(
				'only one platform can be assigned in info.xml'
			);
		}

		return $platformId;
	}


	/**
	 * @return INextSearchPlatform
	 */
	public function getPlatform() {
		$this->loadPlatform();

		return $this->platform;
	}


}