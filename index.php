<?php

require './vendor/autoload.php';

$filePath  = null;
$outputDir = null;
$nameSpace = null;

while ($param = array_shift($argv)) {
    list($paramName, $paramValue) = explode('=', $param) + ['', ''];
    switch ($paramName) {
        case '--file-path':
            $filePath = $paramValue;
            break;

        case '--output-dir':
            $outputDir = $paramValue;
            break;

        case '--name-space':
            $nameSpace = $paramValue;
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
    $rawData = YamlParser::parse($filePath);

    $parser = new Parser();
    $data = $parser->parse($rawData);

    $writer = new Writer($outputDir);
    $writer->write($data);
}

