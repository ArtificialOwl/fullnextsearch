<?php

namespace OCA\FullNextSearch\Controller;

use OCA\FullNextSearch\AppInfo\Application;
use OCA\FullNextSearch\Service\ConfigService;
use OCA\FullNextSearch\Service\MiscService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

class NavigationController extends Controller {

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
	function __construct(IRequest $request, ConfigService $configService, MiscService $miscService) {
		parent::__construct(Application::APP_NAME, $request);
		$this->configService = $configService;
		$this->miscService = $miscService;
	}


	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 * @NoSubAdminRequired
	 *
	 * @return TemplateResponse
	 */
	public function navigate() {
		$data = [
			ConfigService::APP_TEST => $this->configService->getAppValue(
				ConfigService::APP_TEST
			),
			ConfigService::APP_TEST_PERSONAL => $this->configService->getUserValue(
				ConfigService::APP_TEST_PERSONAL
			)
		];

		return new TemplateResponse(Application::APP_NAME, 'navigate', $data);
	}


	/**
	 * compat NC 12 and lower
	 *
	 * @return TemplateResponse
	 */
	public function nc12personal() {
		return new TemplateResponse(Application::APP_NAME, 'settings.personal', [], '');
	}

}