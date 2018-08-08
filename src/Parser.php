<?php

class Parser
{
    private $data = ['types' => []];

    public function parse(array $rawData): array
    {
        foreach ($rawData['types'] as $rawTypeName => $rawType) {
            $type                  = $this->parseType($rawType);
            $this->data['types'][] = $type;
        }

        return $this->data;
    }

    private function parseType(array $rawType): array
    {
        $type               = $rawType;
        $rawProperties      = $rawType['properties'] ?? [];
        $properties         = $this->parseProperties($rawProperties);
        $type['properties'] = $properties;

        return $type;
    }

    private function parseProperties(array $rawProperties): array
    {
        $properties = [];

        foreach ($rawProperties as $rawPropertyName => $rawProperty) {
            if (is_string($rawProperty)) {
                $properties[] = ['type' => $rawProperty];
                continue;
            }

            $property             = $rawProperty;
            $property['__name__'] = $rawPropertyName ?: $property['__name__'];
            $type                 = $rawProperty['type'] ?? 'string';

            if (is_string($type)) {
                $properties[] = $property;
                continue;
            }

            $parent           = $type;
            $child            = $property;
            $childProperties  = $child['properties']  ?? [];
            $parentProperties = $parent['properties'] ?? [];
            $diff             = array_diff_assoc($childProperties, $parentProperties);

            if ($diff) {// extends

            } elseif ($parentProperties) { // include
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
