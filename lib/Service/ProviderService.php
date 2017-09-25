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

use OCA\FullNextSearch\Exceptions\NextSearchProviderIsNotCompatibleException;
use OCA\FullNextSearch\INextSearchProvider;

class ProviderService {

	const FILES = 'OCA\FullNextSearch\Provider\FilesProvider';

	/** @var MiscService */
	private $miscService;

	/**
	 * @var INextSearchProvider[]
	 */
	private $providers = [];

	/**
	 * ProviderService constructor.
	 *
	 * @param MiscService $miscService
	 */
	function __construct(MiscService $miscService) {
		$this->miscService = $miscService;
	}


	/**
	 *
	 */
	public function loadAllLocalProviders() {
		$this->loadProvider(self::FILES);
	}


	/**
	 * @param string $name
	 *
	 * @throws NextSearchProviderIsNotCompatibleException
	 */
	public function loadProvider($name) {

		$provider = \OC::$server->query((string)$name);
		if (!($provider instanceof INextSearchProvider)) {
			throw new NextSearchProviderIsNotCompatibleException(
				$name . ' is not a compatible NextSearchProvider'
			);
		}

		$this->providers[] = $provider;
	}


	/**
	 * @param $userId
	 */
	public function indexContentFromUser($userId) {
		foreach($this->providers AS $provider)
		{
			$provider->index($userId);
		}

	}
}