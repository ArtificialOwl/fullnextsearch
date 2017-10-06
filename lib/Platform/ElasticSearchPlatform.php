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

namespace OCA\FullNextSearch\Platform;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\Curl\CouldNotConnectToHost;
use Elasticsearch\Common\Exceptions\MaxRetriesException;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Exception;
use OCA\FullNextSearch\AppInfo\Application;
use OCA\FullNextSearch\Exceptions\InterruptException;
use OCA\FullNextSearch\INextSearchPlatform;
use OCA\FullNextSearch\INextSearchProvider;
use OCA\FullNextSearch\Model\DocumentAccess;
use OCA\FullNextSearch\Model\ExtendedBase;
use OCA\FullNextSearch\Model\SearchDocument;
use OCA\FullNextSearch\Model\SearchResult;
use OCA\FullNextSearch\Service\MiscService;


class ElasticSearchPlatform implements INextSearchPlatform {

	/** @var MiscService */
	private $miscService;

	/** @var Client */
	private $client;

	/**
	 * {@inheritdoc}
	 */
	public function load() {
		$app = new Application();

		$container = $app->getContainer();
		$this->miscService = $container->query(MiscService::class);

		try {

			$hosts = [
				[
					'host'   => '127.0.0.1',
					'port'   => '9200',
					'scheme' => 'http',
					'user'   => 'username',
					'pass'   => 'password'
				]
			];

			$this->client = ClientBuilder::create()
										 ->setHosts($hosts)
										 ->setRetries(2)
										 ->build();


		} catch (CouldNotConnectToHost $e) {
			echo 'CouldNotConnectToHost';
			$previous = $e->getPrevious();
			if ($previous instanceof MaxRetriesException) {
				echo "Max retries!";
			}
		} catch (Exception $e) {
			echo ' ElasticSearchPlatform::load() Exception --- ' . $e->getMessage() . "\n";
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function resetProvider(INextSearchProvider $provider) {
		$map = $this->generateGlobalMap($provider, false);

		try {
			$this->client->indices()
						 ->delete($map);
		} catch (Missing404Exception $e) {
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function initProvider(INextSearchProvider $provider) {
		$map = $this->generateGlobalMap($provider);

		if (method_exists($provider, 'improveMappingForElasticSearch')) {
			$map = $provider->improveMappingForElasticSearch($map);
		}

		if (!$this->client->indices()
						  ->exists($this->generateGlobalMap($provider, false))) {
			$this->client->indices()
						 ->create($map);
		}


	}


	/**
	 * {@inheritdoc}
	 */
	public function indexDocuments(INextSearchProvider $provider, $documents, $command) {
		foreach ($documents as $document) {

			if ($command !== null) {
				if ($command->hasBeenInterrupted()) {
					throw new InterruptException();
				}

				$this->interactWithCommandDuringIndex($command);
			}

			$this->indexDocument($provider, $document);
		}
	}


	/**
	 * @param INextSearchProvider $provider
	 * @param SearchDocument $document
	 */
	public function indexDocument(INextSearchProvider $provider, SearchDocument $document) {

		$article = array();
		$article['index'] = $provider->getId();
		$article['id'] = $document->getId();
		$article['type'] = 'notype';
		$article['body'] = [
			'title'   => $document->getTitle(),
			'content' => $document->getContent()
		];

		$result = $this->client->index($article);
		echo 'Indexing: ' . json_encode($result) . "\n";
	}


	/**
	 * @param ExtendedBase $command
	 */
	private function interactWithCommandDuringIndex(ExtendedBase $command) {

	}


	/**
	 * {@inheritdoc}
	 */
	public function getId() {
		return 'elastic_search';
	}


	/**
	 * {@inheritdoc}
	 */
	public function create() {
	}


	/**
	 * {@inheritdoc}
	 */
	public function test() {
	}


	/**
	 * {@inheritdoc}
	 */
	public function upgrade() {
	}


	/**
	 * {@inheritdoc}
	 */
	public function search(INextSearchProvider $provider, DocumentAccess $access, $string) {

		$params = [
			'index' => $provider->getId(),
			'type'  => 'notype'
		];
		$params['body']['query']['bool']['should'] =
			[
				['match' => ['title' => $string]],
				['match' => ['content' => $string]]
			];

		$result = $this->client->search($params);
		$searchResult = $this->generateSearchResultFromResult($result);
		$searchResult->setProvider($provider);

		foreach ($result['hits']['hits'] as $entry) {
			$searchResult->addDocument($this->parseSearchEntry($entry, $access->getViewer()));
		}

		return $searchResult;
	}


	/**
	 * @param array $result
	 *
	 * @return SearchResult
	 */
	private function generateSearchResultFromResult($result) {
		$searchResult = new SearchResult();
		$searchResult->setRawResult(json_encode($result));

		return $searchResult;
	}


	/**
	 * @param array $entry
	 * @param string $viewerId
	 *
	 * @return SearchDocument
	 */
	private function parseSearchEntry($entry, $viewerId) {
		$access = new DocumentAccess();
		$access->setViewer($viewerId);

		$document = new SearchDocument($entry['_id']);
		$document->setAccess($access);
		$document->setContent($entry['_source']['content']);
		$document->setScore($entry['_score']);
		$document->setTitle($entry['_source']['title']);

		return $document;
	}


	private function generateGlobalMap(INextSearchProvider $provider, $complete = true) {
		$params = [
			'index' => $provider->getId()
		];

		if ($complete === false) {
			return $params;
		}

		$params['body'] = [
			'settings' => [
				'analysis' => [
					'filter'      => [
						'shingle' => [
							'type' => 'shingle'
						]
					],
					'char_filter' => [
						'pre_negs'  => [
							'type'        => 'pattern_replace',
							'pattern'     => '(\\w+)\\s+((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\b',
							'replacement' => '~$1 $2'
						],
						'post_negs' => [
							'type'        => 'pattern_replace',
							'pattern'     => '\\b((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\s+(\\w+)',
							'replacement' => '$1 ~$2'
						]
					],
					'analyzer'    => [
						$provider->getId() => [
							'type'      => 'custom',
							'tokenizer' => 'standard',
							'filter'    => ['lowercase', 'stop', 'kstem']
						]
					]
				]
			],
			'mappings' => [
				'_default_' => [
					'properties' => [
						'title'    => [
							'type'        => 'text',
							'analyzer'    => 'files',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'content'  => [
							'type'        => 'text',
							'analyzer'    => 'files',
							'term_vector' => 'yes',
							'copy_to'     => 'combined'
						],
						'combined' => [
							'type'        => 'text',
							'analyzer'    => 'files',
							'term_vector' => 'yes'
						],
						'topics'   => [
							'type'  => 'text',
							'index' => 'not_analyzed'
						],
						'places'   => [
							'type'  => 'text',
							'index' => 'not_analyzed'
						]
					]
				]
			]
		];

		return $params;
	}
}