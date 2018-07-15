<?php

use PhpCodeMaker\PhpClass;
use Reader\{
    Source, Type
};
use Writer\TypeBuilder;

class Writer
{
    private $outputDir;

    public function __construct($outputDir)
    {
        $this->outputDir = $outputDir;
    }

    public function write(Source $source)
    {
        $types = $source->getTypes();
        foreach ($types->getList() as $type) {
            $this->writeType($type);
        }
    }

    private function writeType(Type $type, string $parent = '')
    {
        $phpClass = new PhpClass();

        $className = ucfirst($type->name);

        $phpClass
            ->setName($className)
            ->setInherits($parent)
            ->setDescription($type->description)
            ->setNamespace('Test')
        ;

        foreach ($type->properties as $property) {
            $phpProperty = $phpClass->makeProperty($property->name, $property->description);
            $phpProperty->addPhpDoc('@var', $property->type);
            if ($property->required) {
                $phpProperty->addPhpDoc('@required');
            }

            if (!in_array($property->type, ['integer', 'string', 'boolean']) && $property->properties) {
                $parent = $property->type == 'object' ? '' : $property->type;
                $this->writeType($property, $parent);
            }
        }

        $filename = sprintf('%s/%s.php', $this->outputDir, $className);
        file_put_contents($filename, $phpClass);
    }
}
