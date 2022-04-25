<?php
//© 2019 Martin Peter Madsen
namespace MTM\Spreadsheet\Factories;

class Excel extends Base
{
	public function getWorkbook($file=null)
	{
		$rObj	= new \MTM\Spreadsheet\Models\Excel\Workbook();
		if ($file !== null) {
			$rObj->setFile($file);
		}
		return $rObj;
	}
}