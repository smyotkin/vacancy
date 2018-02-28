<?php

namespace Application\Loader;

require_once (APP_PATH . "/Adapter/Adapter.php");
require_once (APP_PATH . "/Log/Logger.php");

use Application\Log\Logger as Logger;
use Application\Adapter\Adapter;

class Loader {

	public function __construct(){}

	public function load($file) {
		Logger::getInst()->info("Starting to load file $file");
		$handle = fopen($file, "r");
		$fileContent = array();
		while (($data = fgetcsv($handle, "1000", ",")) !== false) {
			$fileContent[] = $data;
		}
		unset($fileContent[0]);
		$this->parse($fileContent);

		Logger::getInst()->info("File load is finished");
	}

	private function parse($content) {
		Logger::getInst()->info("Starting to parse file");
		$needleFields = array(0, 1, 5);
		array_walk($content, function($entry) use ($needleFields){
			$fieldsToInsert = array();
			array_walk($entry, function($entryField, $index) use (&$fieldsToInsert, $needleFields) {
				if (in_array($index, $needleFields) && !empty($entryField)) {
					$fieldsToInsert[] = $entryField;
				}
			});

			$fieldsToInsert[] = date("Y-m-d");
			$query = "INSERT INTO `market_data` (id_value, price, is_noon, update_date) VALUES (?, ?, ?, ?)";
			Adapter::getInst()->exec($query, $fieldsToInsert);
		});

		Logger::getInst()->info("File parsing is finished");
	}

}