<?php

require './vendor/autoload.php';

$filePath     = null;
$templatePath = __DIR__ . '/template';
$outputDir    = null;

while ($param = array_shift($argv)) {
    list($paramName, $paramValue) = explode('=', $param) + ['', ''];
    switch ($paramName) {
        case '--file-path':
            $filePath = $paramValue;
            break;

        case '--output-dir':
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
    $yaml = YamlParser::parse($filePath);

    $reader = new Reader();
    $source = $reader->read($yaml);

    $writer = new Writer($outputDir);
    $writer->write($source);
}

