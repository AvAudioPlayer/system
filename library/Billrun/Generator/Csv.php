<?php

/**
 * @package         Billing
 * @copyright       Copyright (C) 2012-2013 S.D.O.C. LTD. All rights reserved.
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Billing Csv generator class
 *
 * @package  Billing
 * @since    0.5
 */
abstract class Billrun_Generator_Csv extends Billrun_Generator {

	protected $data = null;
	protected $headers = null;
	protected $separator = ",";
	protected $filename = null;
	protected $file_path = null;

	public function __construct($options) {
		parent::__construct($options);
		$this->setFilename();
		$this->buildHeader();
		$this->file_path = $this->export_directory . DIRECTORY_SEPARATOR . $this->filename;
		$this->resetFile();
	}

	/**
	 * write row to csv file for generating info into in
	 * 
	 * @param string $path the path to append into
	 * @param string $str the content to write
	 * 
	 * @return boolean true if succes to write info else false
	 */
	protected function writeToFile($str, $overwrite = false) {
		if ($overwrite) {
			$ret = file_put_contents($this->file_path, $str);
		} else {
			$ret = file_put_contents($this->file_path, $str, FILE_APPEND);
		}
		return $ret;
	}

	abstract protected function buildHeader();

	/**
	 * execute the generate action
	 */
	public function generate() {
		if ($this->data->count()) {
			$this->writeHeaders();
			$this->writeRows();
		}
	}

	protected function writeRows() {
		$file_contents = '';
		$counter = 0;
		foreach ($this->data as $entity) {
			$counter++;
			$row_contents = '';
			if (!is_array($entity)) {
				$entity = $entity->getRawData();
			}
			foreach ($this->headers as $key => $field_name) {
				$row_contents.=(isset($entity[$key]) ? $entity[$key] : "") . $this->separator;
			}

			$file_contents .= trim($row_contents, $this->separator) . PHP_EOL;
			if ($counter == 50000) {
				$this->writeToFile($file_contents);
				$file_contents = '';
				$counter = 0;
			}
		}
		$this->writeToFile($file_contents);
	}

	protected function writeHeaders() {
		$header_str = implode($this->headers, $this->separator) . PHP_EOL;
		$this->writeToFile($header_str);
	}

	abstract protected function setFilename();

	protected function resetFile() {
		$this->writeToFile("", true);
	}

}
