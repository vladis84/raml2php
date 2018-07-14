<?php

use Reader\BuilderFactory;
use Reader\Source;

class Reader
{
    /**
     *
     * @var array
     */
    private $ramlData;

    /**
     * 
     * @var Source
     */
    private $source;

    public function __construct()
    {
        $this->source = new Source();
    }

    public function read(array $ramlData): Source
    {
        $this->ramlData = $ramlData;

        $this->fillTypes();
        
        return $this->source;
    }

    private function fillTypes()
    {
        $rawTypes = $this->ramlData['types'] ?? [];

        foreach ($rawTypes as $typeName => $rawType) {
            $builder = BuilderFactory::make($rawType);

            $type = $builder->make($typeName, $rawType);
            $this->source->addType($type);
        }
    }
}
