<?php
//© 2019 Martin Peter Madsen
namespace MTM\Spreadsheet;

class Factories
{
	private static $_cStore=array();
	
	//USE: $aFact		= \MTM\Spreadsheet\Factories::$METHOD_NAME();
	
	public static function getExcel()
	{
		if (array_key_exists(__FUNCTION__, self::$_cStore) === false) {
			self::$_cStore[__FUNCTION__]	= new \MTM\Spreadsheet\Factories\Excel();
		}
		return self::$_cStore[__FUNCTION__];
	}
	public static function getCSV()
	{
		if (array_key_exists(__FUNCTION__, self::$_cStore) === false) {
			self::$_cStore[__FUNCTION__]	= new \MTM\Spreadsheet\Factories\CSV();
		}
		return self::$_cStore[__FUNCTION__];
	}
}