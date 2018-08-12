<?php

use Writer\TypeBuilderFactory;

class Writer
{
    private $outputDir;

    public function __construct($outputDir)
    {
        $this->outputDir = $outputDir;
    }

    public function write(array $data)
    {
        foreach ($data['types'] as $type) {
            $builder  = TypeBuilderFactory::make($type);
            $phpClass = $builder->build($type);

            $dir = $this->outputDir . '/' . $type['__relativePath__'];
            @mkdir($dir, 0777, true);
            $fileName = sprintf('%s/%s.php', $dir, $phpClass->getName());
            file_put_contents($fileName, $phpClass);
        }
    }
}
