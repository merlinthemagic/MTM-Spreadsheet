<?php
//© 2019 Martin Peter Madsen
namespace MTM\Spreadsheet\Models\Excel;

class Sheet
{
	protected $_id=null;
	protected $_name=null;
	protected $_wkbObj=null;
	protected $_mFileObj=null;
	protected $_xmlObj=null;
	protected $_colCount=0;
	protected $_rows=null;
	
	public function getAsCSV()
	{
		return \MTM\Spreadsheet\Factories::getCSV()->getTool()->getFromArray($this->getAsArray());
	}
	public function getAsArray()
	{
		//fill in the gaps in the raw data
		$base26		= \MTM\Utilities\Factories::getBases()->getBase26();
		$tRow		= array_fill(0, $this->getColumnCount(), null);
		$rDatas		= array();
		$rows		= &$this->getData();
		foreach ($rows as $row) {
			$rData	= $tRow;
			foreach ($row->cells as $cell) {
				$colNbr			= $base26->getAsInt($cell->col);
				$rData[$colNbr]	= $cell->value;
			}
			$rDatas[$row->id]	= $rData;
		}
		return $rDatas;
	}
	public function clearData()
	{
		//save memory
		$this->_rows	= array();
		return $this;
	}
	protected function &getData()
	{
		//protected since its likely the row and cell objes will change to classes
		if ($this->_rows === null) {
			$this->_rows	= array();

			$base26		= \MTM\Utilities\Factories::getBases()->getBase26();
			$shStrs		= $this->getParent()->getSharedStrings();
			$datas		= $this->getXmlObj()->{'sheetData'}->row;
			
			foreach ($datas as $data) {
				
				$rId			= (int) $data->attributes()->r;
				$rowObj			= new \stdClass();
				$rowObj->id		= $rId;
				$rowObj->cells	= array();
				
				foreach ($data->c as $cdata) {

					$cId	= (string) $cdata->attributes()->r;
					$cType	= (string) $cdata->attributes()->t;
					$cVal	= (string) $cdata->v;
					
					if (preg_match("/([A-Z]+)([0-9]+)/i", $cId, $raw) == 1) {
						
						$colNbr	= $base26->getAsInt($raw[1]) + 1; //A is 0
						if ($this->_colCount < $colNbr) {
							$this->_colCount	= $colNbr;
						}

						//raw values == ""  || "str" || "s"
						if ($cType == "s") {
							//value is a shared string
							$cVal	= $shStrs[$cVal]->tVal;
						}

						$cellObj				= new \stdClass();
						$cellObj->id			= $cId;
						$cellObj->col			= $raw[1];
						$cellObj->value			= $cVal;
						$rowObj->cells[$raw[1]]	= $cellObj;
						
					} else {
						throw new \Exception("Invalid cell Id: " . $cId);
					}
				}
				$this->_rows[$rId]	= $rowObj;
			}
			
		}
		return $this->_rows;
	}
	public function getColumnCount()
	{
		$this->getData();
		return $this->_colCount;
	}
	public function getXmlObj()
	{
		if ($this->_xmlObj === null) {
			$this->_xmlObj	= simplexml_load_file($this->getMainFile()->getPathAsString());
		}
		return $this->_xmlObj;
	}
	public function setMainFile($fileObj)
	{
		$this->_mFileObj	= $fileObj;
		return $this;
	}
	public function getMainFile()
	{
		return $this->_mFileObj;
	}
	public function setParent($wkbObj)
	{
		$this->_wkbObj	= $wkbObj;
		return $this;
	}
	public function getParent()
	{
		return $this->_wkbObj;
	}
	public function setName($name)
	{
		$this->_name	= $name;
		return $this;
	}
	public function getName()
	{
		return $this->_name;
	}
	public function setId($id)
	{
		$this->_id	= intval($id);
		return $this;
	}
	public function getId()
	{
		return $this->_id;
	}
}