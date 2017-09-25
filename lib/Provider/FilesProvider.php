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

namespace OCA\FullNextSearch\Provider;

use OCA\FullNextSearch\AppInfo\Application;
use OCA\FullNextSearch\INextSearchProvider;
use OCA\FullNextSearch\Provider\Files\NextSearch\FilesIndex;
use OCA\FullNextSearch\Provider\Files\Service\FilesService;
use OCA\FullNextSearch\Service\MiscService;

class FilesProvider implements INextSearchProvider {


	/** @var FilesService */
	private $filesService;

	/** @var MiscService */
	private $miscService;


	/**
	 * {@inheritdoc}
	 */
	public function getId() {
		return 'files';
	}


	/**
	 * {@inheritdoc}
	 */
	public function init() {
		$app = new Application();

		$container = $app->getContainer();
		$this->filesService = $container->query(FilesService::class);
		$this->miscService = $container->query(MiscService::class);
	}


	/**
	 * {@inheritdoc}
	 */
	public function end() {
	}

	/**
	 * {@inheritdoc}
	 */
	public function index($userId, $start, $size) {
		$this->filesService->index($userId);

		return [new FilesIndex()];
	}


	/**
	 * {@inheritdoc}
	 */
	public function search($userId, $needle, $start, $size)
	{
		return [new FilesResult()];
	}
}