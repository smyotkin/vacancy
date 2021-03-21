<?php

namespace Application\Loader;

require_once (APP_PATH . "/Adapter/Adapter.php");
require_once (APP_PATH . "/Log/Logger.php");

use Application\Log\Logger as Logger;
use Application\Adapter\Adapter;

class Loader {

	public function __construct() {}

	public function load($file) {
		Logger::getInst()->info("Starting to load file $file");

		$handle = fopen(ROOT_PATH . '/' . $file, 'r');
		$fileContent = [];

		while (($data = fgetcsv($handle, 1000, ',')) !== false) {
			$fileContent[] = $data;
		}

		fclose($handle);
		unset($fileContent[0]);
		$this->parse($fileContent);

		Logger::getInst()->info("File load is finished");
	}

	private function parse($content) {
		Logger::getInst()->info("Starting to parse file");

		$needleFields = [0, 1, 5];

		foreach ($content as $entry) {
			$fieldsToInsert = [];
	
			foreach ($needleFields as $index) {
				if (!empty($entry[$index])) {
					$fieldsToInsert[] = $entry[$index];
				} else {
					unset($fieldsToInsert);
					break;
				}
			}

			if (!empty($fieldsToInsert)) {
				$fieldsToInsert[] = date("Y-m-d");
				$query            = 'INSERT INTO `market_data` (id_value, price, is_noon, update_date) VALUES (?, ?, ?, ?)';

				Adapter::getInst()->exec($query, $fieldsToInsert);
			}
		}

		Logger::getInst()->info("File parsing is finished");
	}
}
