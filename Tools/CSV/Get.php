<?php
//© 2019 Martin Peter Madsen
namespace MTM\Spreadsheet\Tools\CSV;

class Get
{
	public function getFromArray($rows)
	{
		if (is_array($rows) === false) {
			throw new \Exception("Invalid input");
		}
		$row	= reset($rows);
		if (is_array($row) === false) {
			throw new \Exception("First row does not contain any cells");
		}
		$cCount	= count($row);
		foreach ($rows as $rId => $row) {
			if (is_array($row) === false) {
				throw new \Exception("Row: " . ($rId + 1) . " does not contain any cells");
			}
			if ($cCount == count($row)) {
				foreach ($row as $cId => $cell) {
					if ($cell != "") {
						$row[$cId] = "\"" . $this->escapeValue($cell) . "\"";
					}
				}
				$rows[$rId]		= implode(", ", $row);
				
			} else {
				throw new \Exception("Row has an incorrect amount of cells: " . $rId);
			}
		}
		return implode("\n", $rows);
	}
	public function getAsArray($str)
	{
		if (is_string($str) === false) {
			throw new \Exception("Invalid input");
		}
		$fileObj	= \MTM\FS\Factories::getFiles()->getTempFile("csv");
		$fileObj->setContent($str);
		$rows		= $this->getFromFile($fileObj);
		$fileObj->delete();
		return $rows;
	}
}