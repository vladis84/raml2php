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
    public static function parse($filePath): array
    {
        $source = file_get_contents($filePath);

        $source = preg_replace_callback(
            '/\!include (.+)$/',
            function (array $matches) {
                return file_get_contents($matches[1]);
            },
            $source
        );

        $yaml = yaml_parse($source);

        return $yaml;
    }
}
