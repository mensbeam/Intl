<?php
// this script read and names and labels from each concrete
// class in the Encoding set and generates tables mapping labels
// to names and names to classes

use MensBeam\Intl\Encoding\Encoding;

define("BASE", dirname(__DIR__).DIRECTORY_SEPARATOR);
require_once BASE."vendor".DIRECTORY_SEPARATOR."autoload.php";

$ns = "\\MensBeam\\Intl\\Encoding\\";
$labels = [];
$names = [];
foreach (new \GlobIterator(BASE."/lib/Encoding/*.php", \FilesystemIterator::CURRENT_AS_PATHNAME) as $file) {
    $file = basename($file, ".php");
    $className = $ns.$file;
    $class = new \ReflectionClass($className);
    if ($class->implementsInterface(Encoding::class) && $class->isInstantiable()) {
        $name = $class->getConstant("NAME");
        $names[$name] = $className;
        foreach ($class->getConstant("LABELS") as $label) {
            $labels[$label] = $name;
        }
    }
}

$labelList = [];
foreach ($labels as $k => $v) {
    $labelList[] = "'$k'=>\"$v\"";
}
$labelList = "const LABEL_MAP = [".implode(",", $labelList)."];";

$nameList = [];
foreach ($names as $k => $v) {
    $nameList[] = "'$k'=>$v::class";
}
$nameList = "const NAME_MAP = [".implode(",", $nameList)."];";

echo "$labelList\n";
echo "$nameList\n";
