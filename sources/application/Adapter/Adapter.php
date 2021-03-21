<?php

namespace Application\Adapter;

require_once (APP_PATH . "/Conf/Config.php");
require_once (APP_PATH . "/Log/Logger.php");

use Application\Conf\Config as Conf;
use Application\Log\Logger as Logger;

class Adapter {

	private static $inst;
	private $stmt;

	/**
	 * @var \PDO
	 */
	private $connection;

	private function __construct() {}

	public static function getInst() {
		if (!isset(self::$inst)){
			self::$inst = new self();
		}

		return self::$inst;
	}

	public function getConnection() {
		$conf = Conf::getInst()->getConf();
		$this->connection = new \PDO("mysql:host={$conf->db->host};dbname={$conf->db->name}", $conf->db->user, $conf->db->password);
	}

	public function dropConnection() {
		if (isset($this->connection)) {
			$this->connection = null;
		}
	}

	public function exec($query, $args = array()) {
		try {
			$this->getConnection();
			$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

			$stmt = $this->connection->prepare($query);
			
			if (!is_array($args)) {
				$args = array($args);
			}

			$stmt->execute($args);
			$this->dropConnection();

			$this->stmt = $stmt;

			return self::$inst;
		} catch (\PDOException $e) {
			Logger::getInst()->debug("Error is thrown with message - " . $e->getMessage());
		}
	}

	public function getRow()
	{
		return !empty($this->stmt) ? $this->stmt->fetch() : false;
	}
}