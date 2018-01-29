<?php

use Source\BuilderFactory;

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

    public function __construct(array $ramlData)
    {
        $this->ramlData = $ramlData;
        $this->source = new Source();
    }

    public function read(): Source
    {       
        $this->fillTypes();
        
        return $this->source;
    }

    private function fillTypes()
    {
        $rawTypes = $this->ramlData['types'] ?? [];
        
        foreach ($rawTypes as $typeName => $rawType) {
            $builder = BuilderFactory::make($rawType);

            $type = $builder->make($rawType);
            $type->name = $typeName;
            $this->source->types[] = $type;
        }

        print_r($this->source);
    }
}
