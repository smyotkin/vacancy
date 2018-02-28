<?php

namespace Application;

defined("APP_PATH")  || define("APP_PATH", realpath(dirname(__FILE__)));
defined("ROOT_PATH") || define("ROOT_PATH",  APP_PATH . "/..");
defined("CONF_PATH") || define("CONF_PATH", ROOT_PATH . "/conf");
defined("LOG_PATH")  || define("LOG_PATH", ROOT_PATH . "/logs");
defined("APP_ENV")   || define("APP_ENV", getenv("APP_ENV") ?? "dev");

require_once (APP_PATH . "/Log/Logger.php");
require_once (APP_PATH . "/Loader/Loader.php");

use Application\Log\Logger as Logger;
use Application\Loader\Loader as Loader;

class Core {

	private $loader;

	public function __construct(){
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