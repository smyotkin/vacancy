<?php

namespace Application\Loader;

require_once (APP_PATH . '/Adapter/Adapter.php');
require_once (APP_PATH . '/Log/Logger.php');

use Application\Log\Logger as Logger;
use Application\Adapter\Adapter;

class Loader
{

	private $counter    = 0;
	private $typeFields = [
		'eu' => [0, 1, 5],
		'us' => [6, 1, 5]
	];
	private $filenameData;
	private $query;
	private $queryParams;
	private $errors = [];

	public function __construct() {}

	public function load($file) {
		Logger::getInst()->info('Starting to load file ' . $file);

		$handle      = fopen(ROOT_PATH . '/' . $file, 'r');
		$fileContent = [];

		while (($data = fgetcsv($handle, 1000, ',')) !== false) {
			$fileContent[] = $data;
		}

		fclose($handle);
		unset($fileContent[0]);

		$this->parseFilename($file);
		$this->parse($fileContent, $this->filenameData['type']);

		Logger::getInst()->info('File load is finished');
	}

	private function parse($content, $filetype = 'eu') {
		Logger::getInst()->info("Starting to parse file ($filetype)");

		$needleFields  = $this->typeFields[$filetype];
		$this->counter = 0;

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

			if (!empty($filetype) && !empty($fieldsToInsert)) {
				$this->prepareInsertData($filetype, $fieldsToInsert);

				if (!$this->hasErrors()) {
					$this->insertData();
				}
			}
		}

		Logger::getInst()->info("File ($filetype) parsing is finished - {$this->counter} row(s)");
	}

	private function prepareInsertData($filetype, $insert) {
		$date        = date('Y-m-d', strtotime($this->filenameData['date']));
		$insert[1]   = (int) $insert[1]; // remove zero from price
		$this->query = '';

		if ($filetype == 'eu') {
			$insert[]    = $date;
			$this->query = 'INSERT INTO `market_data` (id_value, price, is_noon, update_date) VALUES (?, ?, ?, ?)';
		} elseif ($filetype == 'us') {
			$market = Adapter::getInst()->exec('SELECT * FROM `markets` WHERE `id_value` = ?', [$insert[0]])->getRow();

			if ($market) {
				$insert[]    = $date;
				$insert[]    = $market['market_id'];
				$this->query = 'INSERT INTO `market_data` (id_value, price, is_noon, update_date, market_id) VALUES (?, ?, ?, ?, ?)';
			}
		} else {
			Logger::getInst()->debug("Error is thrown with message - Unknown filetype");
			$this->addError('Unknown filetype');
		}

		$this->queryParams = $insert;

		return $this;
	}

	private function insertData() {
		if (!empty($this->query) && !empty($this->queryParams)) {
			Adapter::getInst()->exec($this->query, $this->queryParams);
			$this->counter++;
		}
	}

	private function parseFilename($filename) {
		list($title, $type, $date) = explode('.', basename($filename));
		$this->filenameData        = compact(['title', 'type', 'date']);
	}

	private function hasErrors() {
		return !empty($this->errors) ? true : false;
    }

	private function addError($errorMsg) {
		$this->errors[] = $errorMsg;
	}
}
