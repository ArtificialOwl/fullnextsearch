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

namespace OCA\FullNextSearch\Provider\Files\Service;


use OC\Files\View;
use OCA\FullNextSearch\Model\DocumentAccess;
use OCA\FullNextSearch\Model\SearchDocument;
use OCA\FullNextSearch\Provider\Files\Model\FilesDocument;
use OCA\FullNextSearch\Service\MiscService;
use OCP\Files\File;
use OCP\Files\FileInfo;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Files\Node;

class FilesService {

	/** @var IRootFolder */
	private $rootFolder;

	/** @var MiscService */
	private $miscService;


	/**
	 * ProviderService constructor.
	 *
	 * @param IRootFolder $rootFolder
	 * @param MiscService $miscService
	 */
	function __construct(IRootFolder $rootFolder, MiscService $miscService) {
		$this->rootFolder = $rootFolder;
		$this->miscService = $miscService;
	}


	/**
	 * @param string $userId
	 *
	 * @return FilesDocument[]
	 */
	public function getFilesFromUser($userId) {
		/** @var Folder $root */
		$root = \OC::$server->getUserFolder($userId)
							->get('/');

		$result = $this->getFilesFromDirectory($userId, $root);

		return $result;
	}


	/**
	 * @param string $userId
	 * @param Folder $node
	 *
	 * @return FilesDocument[]
	 */
	public function getFilesFromDirectory($userId, Folder $node) {
		$files = $node->getDirectoryListing();

		$result = [];

		foreach ($files as $file) {
			$result[] = $this->generateFilesIndexFromFile($file);
			if ($file->getType() === FileInfo::TYPE_FOLDER) {
				/** @var $file Folder */
				$result = array_merge($result, $this->getFilesFromDirectory($userId, $file));
			}
		}

		return $result;
	}


	/**
	 * @param Node $file
	 *
	 * @return FilesDocument
	 */
	private function generateFilesIndexFromFile(Node $file) {
		$document = new FilesDocument($file->getId());

		$document->setType($file->getType())
				 ->setOwner(
					 $file->getOwner()
						  ->getUID()
				 )
				 ->setMimetype($file->getMimetype());

		$this->completeFileDocument($document);

		return $document;
	}


	private function completeFileDocument(FilesDocument $document) {

		$ownerFiles = $this->rootFolder->getUserFolder($document->getOwner())
									   ->getById($document->getId());

		if (sizeof($ownerFiles) === 0) {
			return;
		}
		$file = array_shift($ownerFiles);

		// TODO: better way to do this : we remove the 'files/'
		$document->setPath(substr($file->getInternalPath(), 6))
				 ->setFilename($file->getName());
	}


	/**
	 * @param SearchDocument $document
	 */
	public function getViewerPathFromDocument(SearchDocument $document) {

		$viewerId = $document->getAccess()
							 ->getViewer();

		$viewerFiles = $this->rootFolder->getUserFolder($viewerId)
										->getById($document->getId());

		if (sizeof($viewerFiles) === 0) {
			return;
		}

		$file = array_shift($viewerFiles);

		// TODO: better way to do this : we remove the '/userId/files/'
		$document->setTitle(MiscService::noEndSlash(substr($file->getPath(), 7 + strlen($viewerId))));
	}


	/**
	 * @param FilesDocument[] $files
	 *
	 * @return FilesDocument[]
	 */
	public function generateDocuments($files) {

		$index = [];
		foreach ($files as $file) {
			if ($file->getType() === FileInfo::TYPE_FOLDER) {
				continue;
			}

			$this->extractContentFromFile($file);
			$index[] = $file;
		}

		return $index;
	}


	/**
	 * @param FilesDocument $document
	 *
	 * @return FilesDocument
	 */
	private function extractContentFromFile(FilesDocument $document) {
		$userFolder = $this->rootFolder->getUserFolder($document->getOwner());
		$file = $userFolder->get($document->getPath());
		if ($file->getType() === FileInfo::TYPE_FILE) {

			/** @var File $file */
			$access = $this->getDocumentAccessFromFile($file);
			$document->setAccess($access);
			$document->setTitle($document->getPath());

			$this->extractContentFromFileText($document, $file);
			//$this->extractContentFromFilePDF($document, $file);
		}

		return $document;
	}


	/**
	 * @param FilesDocument $document
	 * @param File $file
	 */
	private function extractContentFromFileText(FilesDocument $document, File $file) {
		if (substr($document->getMimetype(), 0, 5) !== 'text/') {
			return;
		}

		$document->setContent($file->getContent());
	}


	/**
	 * @param File $file
	 *
	 * @return DocumentAccess
	 */
	private function getDocumentAccessFromFile(File $file) {
		$access = new DocumentAccess(
			$file->getOwner()
				 ->getUID()
		);

		$access->setUsers(['cul']);
		$access->setGroups(['group1']);
		$access->setCircles(['circle', '12345']);

		return $access;
	}


	/**
	 * @param FilesDocument $document
	 * @param File $file
	 */
//	private function extractContentFromFilePDF(FilesDocument $document, File $file) {
//		if ($document->getMimetype() !== 'application/pdf') {
//			return;
//		}
//
//		$content = $file->getContent();
//		$content = base64_encode($content);
//		$document->setContent($content);
//	}
}