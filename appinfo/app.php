<?php


namespace OCA\FullNextSearch\AppInfo;

require_once __DIR__ . '/autoload.php';

$app = new Application();

$app->registerNavigation();
$app->registerSettingsAdmin();
$app->registerSettingsPersonal();
