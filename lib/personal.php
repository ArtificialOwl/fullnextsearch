<?php

namespace OCA\FullNextSearch;

use OCA\FullNextSearch\Controller\NavigationController;
use OCP\AppFramework\Http\TemplateResponse;

$app = new AppInfo\Application();

/** @var TemplateResponse $response */
$response = $app->getContainer()
				->query(NavigationController::class)
				->nc12personal();

return $response->render();


