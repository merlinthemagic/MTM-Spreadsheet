### What is this?

Tools for working with spread sheet files

#### CSV


#####Get data
$fileObj	= \MTM\FS\Factories::getFiles()->getFile("file.csv", "/tmp/");
$rows		= \MTM\Spreadsheet\Factories::getCSV()->getTool()->getFromFile($fileObj);
