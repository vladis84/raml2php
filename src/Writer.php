<?php

use PhpCodeMaker\PhpClass;
use PhpCodeMaker\PhpClass\Property;

class Writer
{
    private $outputDir;

    public function __construct($outputDir)
    {
        exec('rm -rf ' . $outputDir . '/*.*');
        $this->outputDir = $outputDir;
    }

    public function write(array $data)
    {
        foreach ($data['types'] as $type) {
            $this->writeType($type);
        }
    }

    private function writeType(array $type)
    {
        $phpClass = new PhpClass();
        $phpClass->setName($type['__name__']);
        $phpClass->setNamespace($type['__nameSpace__']);

        $types = $type['properties'];
        $this->addProperties($phpClass, $types);

        $fileName = sprintf('%s/%s.php', $this->outputDir, $phpClass->getName());
        file_put_contents($fileName, $phpClass);
    }

    private function addProperties(PhpClass $phpClass, array $properties)
    {
        foreach ($properties as $rawProperty) {
            $property = new Property();
            $property->setName($rawProperty['__name__']);

            if (isset($rawProperty['description'])) {
                $property->addPhpDoc($rawProperty['description']);
            }

            $propertyType = $rawProperty['type'];
            if (isset($rawProperty['example'])) {
                $propertyType .= ' ' . $rawProperty['example'];
            }

            $property->addPhpDoc('@var', $propertyType);

            $phpClass->addProperty($property);

            if (in_array($rawProperty['type'], ['object', 'array'])) {
                $this->writeType($rawProperty);
            }
        }
    }
}
