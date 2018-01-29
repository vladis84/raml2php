<?php

require './vendor/autoload.php';

$filePath = null;
while ($param = array_shift($argv)) {
    list($paramName, $paramValue) = explode('=', $param) + ['', ''];
    switch ($paramName) {
        case '--filePath':
            $filePath = $paramValue;
            break;
    }

}

if ($filePath) {
    $parser = new Parser();
    $raml = $parser->parse($filePath);
    
    $reader = new Reader($raml);
    $source = $reader->read();
    
    $writer = new Writer();
    $writer;
}

