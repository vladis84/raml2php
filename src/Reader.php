<?php

use Reader\{
    Source, TypeBuilder
};

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
        $builder  = new TypeBuilder();

        foreach ($rawTypes as $typeName => $rawType) {
            $type = $builder->make($typeName, $rawType);
            $this->source->addType($type);
        }
    }
}
