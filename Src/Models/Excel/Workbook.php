<?php
//© 2019 Martin Peter Madsen
namespace MTM\Spreadsheet\Models\Excel;

class Workbook
{
	protected $_fileObj=null;
	protected $_sheetObjs=null;
	protected $_workPath=null;
	protected $_sharedStrings=null;

	public function getSheetByName($name)
	{
		foreach ($this->getSheets() as $sheetObj) {
			if ($sheetObj->getName() == $name) {
				return $sheetObj;
			}
		}
		return null;
	}
	public function getSheets()
	{
		if ($this->_sheetObjs === null) {
			$this->_sheetObjs	= array();
			
			$dirObj			= $this->getWorkPath();
			$fileObj		= $dirObj->getChildByName("xl")->getChildByName("_rels")->getChildByName("workbook.xml.rels");
			$xmlObj1		= simplexml_load_file($fileObj->getPathAsString());
			$relObjs		= array();
			foreach ($xmlObj1->Relationship as $rel) {
				$id				= (string) $rel->attributes()->Id;
				$path			= explode("/", (string) $rel->attributes()->Target);
				
				$relObj			= new \stdClass();
				$relObj->id		= $id;
				$relObj->path	= $path;
				$relObjs[$id]	= $relObj;
			}
			
			
			$fileObj	= $dirObj->getChildByName("xl")->getChildByName("workbook.xml");
			$xmlObj2	= simplexml_load_file($fileObj->getPathAsString());
			foreach ($xmlObj2->sheets->sheet as $sheet) {
				
				$id		= (string) $sheet->attributes()->sheetId;
				$name	= (string) $sheet->attributes()->name;
				$refId	= (string) $sheet->attributes("r", true)->id;
				
				if (array_key_exists($refId, $relObjs) === true) {
					
					$relObj			= $relObjs[$refId];
					$mainName		= array_pop($relObj->path);
					$curDir			= $dirObj->getChildByName("xl");
					foreach ($relObj->path as $dirName) {
						$curDir	= $curDir->getChildByName($dirName);
					}
					$mainFile	= $curDir->getChildByName($mainName);
					
					$sheetObj			= new \MTM\Spreadsheet\Models\Excel\Sheet();
					$sheetObj->setParent($this)->setName($name)->setId($id)->setMainFile($mainFile);
					$this->_sheetObjs[]	= $sheetObj;
					
				} else {
					throw new \Exception("Missing sheet referance: " . $refId);
				}
			}
		}
		return $this->_sheetObjs;
	}
	public function getSharedStrings()
	{
		if ($this->_sharedStrings === null) {
			$this->_sharedStrings	= array();
			
			$fileObj	= $this->getWorkPath()->getChildByName("xl")->getChildByName("sharedStrings.xml");
			$xmlObj		= simplexml_load_file($fileObj->getPathAsString());
			
			$i=0;
			foreach ($xmlObj->si as $ssItem) {
				$ssObj					= new \stdClass();
				$ssObj->id				= $i;
				$ssObj->tVal			= (string) $ssItem->t;
				$this->_sharedStrings[]	= $ssObj;
				$i++;
			}
		}
		return $this->_sharedStrings;
	}
	
	public function getWorkPath()
	{
		if ($this->_workPath === null) {

			$dirObj	= \MTM\Compress\Factories::getZip()->getUncompress()->inflate($this->getFile());
			$dirObj->setFromSystem(true);
			
			$xlDir	= $dirObj->getChildByName("xl");
			if (is_object($xlDir) === false) {
				throw new \Exception("xl directory is missing");
			}

			$this->_workPath	= $dirObj;
		}
		return $this->_workPath;
	}
	public function setFile($file)
	{
		if (is_object($file) === false) {
			$file	= \MTM\FS\Factories::getFiles()->getFileFromPath($file);
		}
		$this->_fileObj	= $file;
		return $this;
	}
	public function getFile()
	{
		return $this->_fileObj;
	}
	//make it look pretty
	// 	$dom = new \DOMDocument("1.0");
	// 	$dom->preserveWhiteSpace = false;
	// 	$dom->formatOutput = true;
	// 	$dom->loadXML($xmlObj2->asXML());
	// 	echo "\n <code><pre> \nClass:  ".get_class($this)." \nMethod:  ".__FUNCTION__. "  \n";
	// 	print_r($relObjs);
	// 	echo "\n 2222 \n";
	// 	print_r($refId);
	// 	echo "\n 3333 \n";
	// 	print_r($dom->saveXML());
	// 	echo "\n ".time()."</pre></code> \n ";
	// 	die("end");
}