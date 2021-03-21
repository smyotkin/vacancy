<?php

namespace Application;

defined("APP_PATH")     || define("APP_PATH", dirname(__FILE__));
defined("ROOT_PATH")    || define("ROOT_PATH",  realpath(APP_PATH . '/../..'));
defined("SOURCES_PATH") || define("SOURCES_PATH", ROOT_PATH . "/sources");
defined("CONF_PATH")    || define("CONF_PATH", SOURCES_PATH . "/conf");
defined("LOG_PATH")     || define("LOG_PATH", SOURCES_PATH . "/logs");
defined("APP_ENV")      || define("APP_ENV", getenv("APP_ENV") ? APP_ENV : "dev");

require_once (APP_PATH . "/Log/Logger.php");
require_once (APP_PATH . "/Loader/Loader.php");

use Application\Log\Logger as Logger;
use Application\Loader\Loader as Loader;

class Core {

	private $loader;

	public function __construct() {
		Logger::getInst()->info("Core instance created");
	}

	/**
	 * @return Loader
	 */
	public function getLoader() {
		Logger::getInst()->info("Core::getLoader");

		if (!isset($this->loader)) {
			$this->createLoader();
		}

		return $this->loader;
	}

	private function createLoader() {
		Logger::getInst()->info("Core::createLoader");

		$this->loader = new Loader();
	}
}