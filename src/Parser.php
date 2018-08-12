<?php

class Parser
{
    private $data = ['types' => []];

    /**
     * @var string
     */
    private $rootNameSpace;

    public function parse(array $rawData, string $rootNameSpace): array
    {
        $this->rootNameSpace = $rootNameSpace;

        foreach ($rawData['types'] as $rawTypeName => $rawType) {
            $rawType['__name__']         = $rawTypeName;
            $rawType['__nameSpace__']    = $rootNameSpace;
            $rawType['__relativePath__'] = '';

            $type = $this->parseType($rawType);

            $this->data['types'][] = $type;
        }

        return $this->data;
    }

    private function parseType(array $rawType): array
    {
        $type          = $rawType;
        $rawProperties = $rawType['properties'] ?? [];

        if (strpos($type['__nameSpace__'], $this->rootNameSpace) === false) {
            $type['__nameSpace__'] = $this->rootNameSpace . '\\' . $type['__nameSpace__'];
        }

        $type['__uses__'] = $type['__uses__'] ?? [];

        $properties         = $this->parseProperties($rawProperties, $type);
        $type['properties'] = $properties;

        return $type;
    }

    private function parseProperties(array $rawProperties, array &$type): array
    {
        $properties = [];

        foreach ($rawProperties as $rawPropertyName => $rawProperty) {
            if (is_string($rawProperty)) {
                $properties[] = ['type' => $rawProperty];
                continue;
            }

            $property             = $rawProperty;
            $property['__name__'] = $rawPropertyName ?: $property['__name__'];;

            if (isset($property['enum'])) {
                $enum                     = $property;
                $enum['__name__']         = $type['__name__'] . ucfirst($property['__name__']);
                $enum['__nameSpace__']    = $type['__nameSpace__'];
                $enum['__relativePath__'] = $type['__relativePath__'];
                $enum['type']             = 'enum';

                $this->data['types'][] = $enum;
            }

            // Массивы объектов
            if (isset($property['items']) && $property['items']['type'] == 'object') {
                $property['type'] = $property['items']['__name__'] . '[]';
                $type['__uses__'][]    = $property['items']['__nameSpace__'] . '\\' . $property['items']['__name__'];
                $this->data['types'][] = $this->parseType($property['items']);
            }

            $propertyType = $rawProperty['type'] ?? 'string';

            if (is_string($propertyType)) {
                $properties[] = $property;
                continue;
            }

            $parent = $propertyType;
            $child  = $property;

            $childProperties  = $child['properties'] ?? [];
            $parentProperties = $parent['properties'] ?? [];

            $diff = array_diff_assoc($childProperties, $parentProperties);

            if ($diff) {// extends
//                $property
            } elseif ($parentProperties) { // include
                $type['__uses__'][]    = $parent['__nameSpace__'] . '\\' . $parent['__name__'];
                $property['type']      = $parent['__name__'];
                $this->data['types'][] = $this->parseType($parent);
            } else {
                unset($child['type']);
                $property = array_merge($parent, $child);
            }

            $properties[] = $property;
        }

        return $properties;
    }
}
