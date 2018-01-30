<?php

class Writer
{
    private $templatePath;
    private $outputDir;

    public function __construct($templatePath = null, $outputDir)
    {
        $this->templatePath = $templatePath;
        $this->outputDir = $outputDir;
    }

    public function write(Source $source)
    {
        /* @var $type Source\Type */
        foreach ($source->types as $type) {
            ob_start();
            require "{$this->templatePath}/{$type->type}.php";
            $fileContent = ob_get_clean();
            file_put_contents("{$this->outputDir}/{$type->name}.php", $fileContent);
        }

//        $this->formartCode();
    }

    private function formartCode()
    {
        echo __DIR__ . "/../vendor/bin/php-cs-fixer fix {$this->outputDir}";
        exec(__DIR__ . "/../vendor/bin/php-cs-fixer fix {$this->outputDir}");
    }
}
