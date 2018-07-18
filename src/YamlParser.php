<?php

/**
 *
 * Разбирает RAML
 *
 */
class YamlParser
{
    /**
     * @param string $filePath
     *
     * @return array
     */
    public function parse($filePath): array
    {
        $ramlDir = dirname($filePath);
        $currentWorkDir = getcwd();
        chdir($ramlDir);

        $mainFile = file_get_contents($filePath);
        $source = $this->include($mainFile);
        chdir($currentWorkDir);

        $yaml = yaml_parse($source);

        return $yaml;
    }

    private function include(string $source): string
    {
        $source = preg_replace_callback(
            '/(.+)\!include\s+(.+)/',
            function (array $matches) {
                $spaceCount = substr_count($matches[1], " ") + 1;

                $currentDir = dirname($matches[2]);
                $content = file_get_contents($matches[2]);
                $content = preg_replace('~\!include\s+.*(.+\.raml)~U', "!include {$currentDir}/$1", $content);

                $content = trim(preg_replace(['/.+raml.+/i', '/^\s+$/'], ['', ''], $content));

                $spaces = str_repeat(' ', $spaceCount);
                $content = str_replace("\n", "\n" . $spaces, $content);

                return $matches[1] . "\n" . $spaces . $content;
            },
            $source
        );

        if (strpos($source, '!include')) {
            return $this->include($source);
        }

        return $source;
    }
}
