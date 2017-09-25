<?php

namespace OCA\FullNextSearch\AppInfo;

use OCA\FullNextSearch\Controller\SimpleController;
use OCA\FullNextSearch\Controller\NavigationController;
use OCA\FullNextSearch\Controller\SettingsController;
use OCA\FullNextSearch\Service\MiscService;
use OCA\FullNextSearch\Service\SimpleService;
use OCA\FullNextSearch\Service\ConfigService;
use OCP\AppFramework\App;
use OCP\AppFramework\IAppContainer;
use OCP\IUser;

class Application extends App {

	const APP_NAME = 'fullnextsearch';

	/**
	 * @param array $params
	 */
	public function __construct(array $params = array()) {
		parent::__construct(self::APP_NAME, $params);

		$this->registerHooks();
	}


	/**
	 * Register Hooks
	 */
	public function registerHooks() {
	}



	/**
	 * Register Navigation Tab
	 */
	public function registerNavigation() {

		$this->getContainer()
			 ->getServer()
			 ->getNavigationManager()
			 ->add(
				 function() {
					 $urlGen = \OC::$server->getURLGenerator();
					 $navName = \OC::$server->getL10N(self::APP_NAME)
											->t('Full Next Search');

					 return [
						 'id' => self::APP_NAME,
						 'order' => 5,
						 'href' => $urlGen->linkToRoute('fullnextsearch.Navigation.navigate'),
						 'icon' => $urlGen->imagePath(self::APP_NAME, 'ruler.svg'),
						 'name' => $navName
					 ];
				 }
			 );
	}


	public function registerSettingsAdmin() {
		\OCP\App::registerAdmin(self::APP_NAME, 'lib/admin');
	}

	public function registerSettingsPersonal() {
		\OCP\App::registerPersonal(self::APP_NAME, 'lib/personal');
	}
}

