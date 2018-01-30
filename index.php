<?php

require './vendor/autoload.php';

$filePath = null;
$templatePath = __DIR__ . '/template';
$outputDir = null;

while ($param = array_shift($argv)) {
    list($paramName, $paramValue) = explode('=', $param) + ['', ''];
    switch ($paramName) {
        case '--filePath':
            $filePath = $paramValue;
            break;
        case '--templatePath':
            $templatePath = $paramValue;
            break;
        case '--outputDir':
            $outputDir = $paramValue;
            break;
    }

}

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

if (!is_writable($outputDir)) {
    die("Директория '{$outputDir}' не доступна для записи");
}

if ($filePath) {
    $parser = new Parser();
    $raml = $parser->parse($filePath);
    
    $reader = new Reader($raml);
    $source = $reader->read();
    
    $writer = new Writer($templatePath, $outputDir);
    $writer->write($source);
}

