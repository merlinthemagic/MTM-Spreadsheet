<?php
//© 2019 Martin Peter Madsen
namespace MTM\Spreadsheet\Factories;

class CSV extends Base
{
	public function getTool()
	{
		if (array_key_exists(__FUNCTION__, $this->_cStore) === false) {
			$this->_cStore[__FUNCTION__]	= new \MTM\Spreadsheet\Tools\CSV\Actions();
		}
		return $this->_cStore[__FUNCTION__];
	}
}