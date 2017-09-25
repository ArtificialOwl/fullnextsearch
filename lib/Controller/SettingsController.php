<?php

namespace OCA\FullNextSearch\Controller;

use OCA\FullNextSearch\AppInfo\Application;
use OCA\FullNextSearch\Service\ConfigService;
use OCA\FullNextSearch\Service\MiscService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;use OCP\IRequest;

class SettingsController extends Controller {

	/** @var ConfigService */
	private $configService;

	/** @var MiscService */
	private $miscService;


	/**
	 * NavigationController constructor.
	 *
	 * @param IRequest $request
	 * @param ConfigService $configService
	 * @param MiscService $miscService
	 */
	function __construct( IRequest $request, ConfigService $configService, MiscService $miscService) {
		parent::__construct(Application::APP_NAME, $request);
		$this->configService = $configService;
		$this->miscService = $miscService;
	}


	/**
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function getSettingsPersonal() {
		$data = [
			ConfigService::APP_TEST_PERSONAL => $this->configService->getUserValue(
				ConfigService::APP_TEST_PERSONAL
			)
		];

		return new DataResponse($data, Http::STATUS_OK);
	}

	/**
	 * @param $data
	 *
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function setSettingsPersonal($data) {
		$this->configService->setUserValue(
			ConfigService::APP_TEST_PERSONAL, $data[ConfigService::APP_TEST_PERSONAL]
		);

		return $this->getSettingsAdmin();
	}


	/**
	 * @return DataResponse
	 */
	public function getSettingsAdmin() {
		$data = [
			ConfigService::APP_TEST => $this->configService->getAppValue(
				ConfigService::APP_TEST
			)
		];

		return new DataResponse($data, Http::STATUS_OK);
	}

	/**
	 * @param $data
	 *
	 * @return DataResponse
	 */
	public function setSettingsAdmin($data) {
		$this->configService->setAppValue(
			ConfigService::APP_TEST, $data[ConfigService::APP_TEST]
		);

		return $this->getSettingsAdmin();
	}

}