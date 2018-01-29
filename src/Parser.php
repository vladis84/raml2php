<?php

/**
 * 
 * Разбирает RAML
 *
 */
class Parser
{
    /**
     * @param string $filePath
     * @return array
     */
    public function parse($filePath):array
    {
        return yaml_parse_file($filePath);
    }
}

