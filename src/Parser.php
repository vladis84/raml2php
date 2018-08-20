<?php

class Parser
{
    private $data = ['types' => [], 'operations' => [], 'requests' => [], 'responses' => []];

    /**
     * @var string
     */
    private $rootNameSpace;

    public function parse(array $rawData, string $rootNameSpace): array
    {
        $this->rootNameSpace = trim($rootNameSpace, '\\');

        $types = $rawData['types'] ?? [];
        foreach ($types as $rawTypeName => $rawType) {
            $rawType['__name__']         = $rawTypeName;
            $rawType['__nameSpace__']    = $rootNameSpace;
            $rawType['__relativePath__'] = '';

            $type = $this->parseType($rawType);

            $this->data['types'][] = $type;
        }

        foreach ($rawData as $operationName => $rawOperation) {
            if (strpos($operationName, '/') === 0) {
                $this->data['operations'] = $this->parseOperation($operationName, $rawOperation);
            }
        }

        return $this->data;
    }

    private function parseType(array $rawType): array
    {
        $type = $rawType;

        if (strpos($type['__nameSpace__'], $this->rootNameSpace) === false) {
            $type['__nameSpace__'] = $this->rootNameSpace . '\\' . $type['__nameSpace__'];
        }

        if (isset($rawType['properties'])) {
            $properties         = $this->parseProperties($rawType['properties'], $type);
            $type['properties'] = $properties;
        }

        return $type;
    }

    private function parseProperties(array $rawProperties, array $type): array
    {
        $properties = [];

        foreach ($rawProperties as $rawPropertyName => $rawProperty) {
            if (is_string($rawProperty)) {
                $rawProperty = ['type' => $rawProperty];
            }

            $property             = $rawProperty;
            $property['__name__'] = $rawPropertyName ?: $property['__name__'];
            $fullNameTemplate     = "\\{$this->rootNameSpace}\\%s\\%s";

            // Enum
            if (isset($property['enum'])) {
                $enum = $property;

                $enum['__name__']         = $type['__name__'] . ucfirst($property['__name__']) . 'Enum';
                $enum['__nameSpace__']    = "\\{$this->rootNameSpace}\\Enum";
                $enum['__relativePath__'] = 'Enum';
                $enum['type']             = 'enum';

                $property['__link__'] = $enum['__nameSpace__'] . '\\' . $enum['__name__'];

                $this->data['types'][] = $enum;
            }

            // Массивы объектов
            if (isset($property['items']) && $property['items']['type'] == 'object') {
                $property['type'] = sprintf(
                    $fullNameTemplate,
                    $property['items']['__nameSpace__'],
                    $property['items']['__name__']
                );
                $property['type'] .= '[]';

                $this->data['types'][] = $this->parseType($property['items']);
            }

            $propertyType = $rawProperty['type'] ?? 'string';

            // Для объектов добавляем корневой namespace если его нет
            if (
                is_string($propertyType)
                &&
                strpos($propertyType, $this->rootNameSpace) === false
                &&
                preg_match('/^[A-Z]/', $propertyType)
            ) {
                $property['type'] = '\\' . $this->rootNameSpace . '\\' . $property['type'];
            }

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
                $property['__extends__'] = sprintf($fullNameTemplate, $parent['__nameSpace__'], $parent['__name__']);
                $this->data['types'][]   = $this->parseType($parent);
            } elseif ($parentProperties) { // include
                $property['type']      = sprintf($fullNameTemplate, $parent['__nameSpace__'], $parent['__name__']);
                $this->data['types'][] = $this->parseType($parent);
            } else {
                unset($child['type']);
                $property = array_merge($parent, $child);
            }

            $properties[] = $property;
        }

        return $properties;
    }

    private function parseOperation(string $rawOperationName, array $rawOperation)
    {
        $rawOperationName    = preg_replace('/{.+}/', '', $rawOperationName);
        $operationName       = '';
        $operationNameWorlds = preg_split('~[/-]~', $rawOperationName);
        foreach ($operationNameWorlds as $operationNameWorld) {
            $operationName .= ucfirst($operationNameWorld);
        }

        $rawOperationData = current($rawOperation);
        $rawOperationBody = $rawOperationData['body'] ?? [];
        $rawRequest       = current($rawOperationBody);
        if ($rawRequest) {
            $rawRequest['__name__']         = $operationName . 'Request';
            $rawRequest['__nameSpace__']    = $this->rootNameSpace . '\\Request';
            $rawRequest['__relativePath__'] = 'Request';
            $this->data['types'][]          = $this->parseType($rawRequest);
        }

        $rawResponse = $rawOperationData['responses']['200']['body']['application/json'] ?? [];
        if ($rawResponse) {
            if (is_string($rawResponse['type']) && preg_match('/^[A-Z]/', $rawResponse['type'])) {
                $rawResponse['type'] = '\\' . $this->rootNameSpace . '\\' . $rawResponse['type'];
            }
            $rawResponse['__name__']         = $operationName . 'Response';
            $rawResponse['__nameSpace__']    = $this->rootNameSpace . '\\Response';
            $rawResponse['__relativePath__'] = 'Response';
            $this->data['types'][]           = $this->parseType($rawResponse);
        }
    }
}
