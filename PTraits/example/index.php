<?php
require_once($_SERVER['DOCUMENT_ROOT']."/com/php/Loader.php");
Loader::register();

$cal = new \PTraits\example\Calendar();
$cal->import("\PTraits\example\Traits\Dates");

echo "<pre>\n";
echo date("M/d/Y", $cal->getStamp())."\n";
$cal->addDays(5);
echo date("M/d/Y", $cal->getStamp())."\n";
echo "</pre>\n";

if ($_GET['log']) {
	echo "<pre>";
	echo "\n\n\n\n\n\n";
	echo "Trait classes loaded:";
	echo "\n----------------------------------------------------------------------------\n";
	foreach ($cal->getTraits() as $trait) {
		echo $trait."\n";
	}
	echo "\n\nTrait methods loaded:";
	echo "\n----------------------------------------------------------------------------\n";
	foreach ($cal->getMethods() as $method) {
		echo $method['trait']."::".$method['method']."\n";
	}
	echo "\n\nFiles loaded:";
	echo "\n----------------------------------------------------------------------------\n";
	foreach (Loader::log() as $file) {
		echo $file."\n";
	}
	echo "</pre>\n";
}
?>