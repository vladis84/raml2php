<?php

/**
 *
 * Разбирает RAML
 *
 */
class YamlParser
{
    private $includes = [];

    /**
     * @param string $filePath
     *
     * @return array
     */
    public function parse($filePath): array
    {
        $ramlDir        = dirname($filePath);
        $currentWorkDir = getcwd();
        chdir($ramlDir);

        $mainFile = file_get_contents($filePath);
        $source   = $this->include($mainFile);
        $source   = $this->insertIncludes($source);
        chdir($currentWorkDir);

        $yaml = yaml_parse($source);

        return $yaml;
    }

    private function include(string $source): string
    {
        $source = preg_replace_callback(
            '/(?<declaration>.+)\!include\s+(?<file>.+)/',
            function (array $matches) {
                $dir         = dirname($matches['file']);
                $contentType = basename($matches['file'], '.raml');

                $content = file_get_contents($matches['file']);
                $content = trim(preg_replace(['/.+raml.+/i', '/^\s+$/'], ['', ''], $content));
                $content = preg_replace('~\!include\s+(.*\w+\.raml)~U', "!include {$dir}/$1", $content);

                if (trim($matches['declaration']) != 'type:') {
                    $spaceCount = substr_count($matches['declaration'], ' ') + 1;
                    $spaces     = str_repeat(' ', $spaceCount);
                    $content    = str_replace("\n", "\n" . $spaces, $content);
                    $content    = $matches['declaration'] . "\n" . $spaces . $content;
                } else {
                    $this->includes[$contentType] = $content;
                    $content                      = $matches['declaration'] . ' ' . $contentType;
                }

                return $content;
            },
            $source
        );

        if (strpos($source, '!include')) {
            return $this->include($source);
        }

        return $source;
    }

    private function insertIncludes(string $source): string
    {
        $includes = '';
        foreach ($this->includes as $type => $include) {
            if (strpos($include, '!include')) {
                $include = $this->include($include);
            }
            $include = str_replace("\n", "\n    ", $include);
            $includes.= "  {$type}: \n    {$include}\n";
        }

        $source = str_replace('types:', "types:\n" . $includes, $source);

        return $source;
    }
}
