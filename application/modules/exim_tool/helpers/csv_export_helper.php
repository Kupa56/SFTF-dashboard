<?php


//create a file pointer
$f = fopen('php://memory', 'w');


class CSVFileExport
{

	private $fields = array();
	private $lines = array();

	/**
	 * CSVFileExport constructor.
	 * @param array $fields
	 * @param array $lines
	 */
	public function __construct()
	{
		$this->fields = array();
		$this->lines = array();
	}

	public function addLine($line)
	{

		$this->lines[] = $line;

	}

	/**
	 * @return array
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * @param array $fields
	 */
	public function setFields($fields)
	{
		$this->fields = $fields;
	}


	public function download($name = "file")
	{

		if (!empty($this->fields) and !empty($this->lines)) {

			$delimiter = ",";
			$filename = $name . "_" . date('Y-m-d') . ".csv";

			//create a file pointer
			$f = fopen('php://memory', 'w');

			$this->fields = array_map("utf8_decode", $this->fields);
			fputcsv($f, $this->fields, $delimiter);

			foreach ($this->lines as $line) {
				$line = array_map("utf8_decode", $line);
				fputcsv($f, $line, $delimiter);
			}

			//move back to beginning of file
			fseek($f, 0);

			//set headers to download file rather than displayed
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="' . $filename . '";');

			//output all remaining data on a file pointer
			fpassthru($f);



		} else {
			echo "Error in exporting!";
		}

	}


}
