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

        $yaml = yaml_parse($source);

        return $yaml;
    }
}
