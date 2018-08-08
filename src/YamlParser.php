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
    public static function parse(string $filePath): array
    {
        $callback   = [];
        $ramlDir    = dirname($filePath) . '/';
        $yaml_parse = function (string $file) use (&$callback, $ramlDir) {
            $yaml = [];

            $prevCwd = getcwd();
            chdir(dirname($file));
            $filePath = basename($file);

            if (file_exists($filePath)) {
                $yaml = yaml_parse_file($filePath, 0, $cnt, $callback);

                $relativePath = str_replace($ramlDir, '', getcwd());

                $yaml['__nameSpace__']    = str_replace('/', '\\', $relativePath);
                $yaml['__name__']         = preg_replace('/\..+/', '', $filePath);
                $yaml['__relativePath__'] = $relativePath;
            }

            chdir($prevCwd);

            return $yaml;
        };

        $callback['!include'] = $yaml_parse;

        return $yaml_parse($filePath);
    }
}
