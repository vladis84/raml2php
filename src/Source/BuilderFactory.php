<?php

namespace Source;

use Source\Builder\ObjectBuilder;

class BuilderFactory
{
    public static function make(array $rawType): BuilderInterface
    {
        $type = $rawType['type'] ?? $rawType;
        
        switch ($type) {
            case 'object':
                return new ObjectBuilder();
        }
    }
}

