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

namespace OCA\FullNextSearch\Provider\Files\NextSearch;

use OCA\FullNextSearch\INextSearchDocument;

class FilesDocument implements INextSearchDocument {

	/** @var string|int */
	private $id;

	/** @var string */
	private $type;

	/** @var string */
	private $path;

	/** @var string */
	private $filename;

	function __construct($id) {
		$this->id = $id;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setType($type) {
		$this->type = $type;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param string $path
	 *
	 * @return $this
	 */
	public function setPath($path) {
		$this->path = $path;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}


	/**
	 * @param $name
	 *
	 * @internal param string $path
	 * @return $this
	 */
	public function setFilename($name) {
		$this->filename = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}


}