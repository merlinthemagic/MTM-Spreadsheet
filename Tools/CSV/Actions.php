<?php
//© 2019 Martin Peter Madsen
namespace MTM\Spreadsheet\Tools\CSV;

class Actions extends Get
{
	public function escapeValue($value)
	{
		if (strpos($value, "\"") !== false) {
			//" is escaped by "".
			//Src: RFC-4180, paragraph "If double-quotes are used to enclose fields, then a double-quote
			//appearing inside a field must be escaped by preceding it with another double quote."
			$value	= str_replace("\"", "\"\"", $value);
		}
		if (strpos($value, ",") !== false) {
			//enclose in ""
			$value	= "\"" . $value. "\"";
		}
		return $value;
	}
}