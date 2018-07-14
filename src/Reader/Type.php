<?php

namespace Reader;

/**
 */
class Type
{
    /**
     * Тип объекта
     *
     * @var string object|string и т.д.
     */
    public $type;

    /**
     * Описание объекта
     *
     * @var string
     */
    public $description;

    /**
     * Обязательность
     *
     * @var boolean
     */
    public $required;

    /**
     * Название
     *
     * @var string
     */
    public $name;

    /**
     * Список свойств
     *
     * @var self[]
     */
    public $properties = [];
}

