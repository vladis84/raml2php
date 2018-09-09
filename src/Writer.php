<?php

use Parser\TypeBuilderFactory;

class Writer
{
    private $outputDir;

    private $rootNamespace;

    public function __construct($outputDir, $rootNamespace)
    {
        $this->outputDir = $outputDir;
        $this->rootNamespace = $rootNamespace;
    }

    /**
     * @param \PhpCodeMaker\PhpClass[] $phpClasses
     */
    public function write(array $phpClasses)
    {
        foreach ($phpClasses as $phpClass) {
            $nameSpace = $phpClass->getNameSpace()->getName();
            $relativeNamespace = str_replace($this->rootNamespace, '' , $nameSpace);
            $dir = $this->outputDir . '/' . str_replace('\\', '/', $relativeNamespace);

            @mkdir($dir, 0777, true);

            $fileName = sprintf('%s/%s.php', $dir, $phpClass->getName());
            file_put_contents($fileName, $phpClass);
        }
    }
}
