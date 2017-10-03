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

namespace OCA\FullNextSearch\Command;

use Exception;
use OCA\FullNextSearch\Exceptions\InterruptException;
use OCA\FullNextSearch\Model\ExtendedBase;
use OCA\FullNextSearch\Service\IndexService;
use OCA\FullNextSearch\Service\MiscService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Index extends ExtendedBase {

	/** @var IndexService */
	private $indexService;

	/** @var MiscService */
	private $miscService;


	/**
	 * Index constructor.
	 *
	 * @param IndexService $indexService
	 * @param MiscService $miscService
	 */
	public function __construct(IndexService $indexService, MiscService $miscService) {
		parent::__construct();
		$this->indexService = $indexService;

		$this->miscService = $miscService;
	}


	protected function configure() {
		parent::configure();
		$this->setName('fullnextsearch:index')
			 ->setDescription('Index files');
	}


	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('index');

		$this->setOutput($output);
		try {

			$users = \OC::$server->getUserManager()
								 ->search('');

			foreach ($users as $user) {
				if ($user->getUID() === 'test1') {
					continue;
				}

				$output->writeln(' USER: ' . $user->getUID());
				$this->indexService->indexContentFromUser($user->getUID(), $this);
			}

		} catch (Exception $e) {
			throw $e;
		}
	}


}



