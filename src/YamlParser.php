<?php

/**
 *
 * Разбирает RAML
 *
 */
class YamlParser
{
    private $ramlDir;

    /**
     * @param string $filePath
     *
     * @return array
     */
    public function parse($filePath): array
    {
        $mainFile = file_get_contents($filePath);
        $this->ramlDir = dirname($filePath) . '/';

        $source = $this->include($mainFile);

        $yaml = yaml_parse($source);

        return $yaml;
    }

    private function include(string $source): string
    {
        $source = preg_replace_callback(
            '/(.+)\!include\s+(.+)/',
            function (array $matches) {
                $spaceCount = substr_count($matches[1], ' ') + 1;

                $content = trim(file_get_contents($this->ramlDir . $matches[2]));
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
