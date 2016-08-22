<?php

namespace Elixir\DB\Query\SQL;

use Elixir\STDLib\MacroableTrait;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ColumnFactory
{
    use MacroableTrait;

    /**
     * @param string $name
     * @param bool   $default
     *
     * @return Column
     */
    public static function boolean($name, $default = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::TINYINT,
                'value' => 1,
                'attribute' => Column::UNSIGNED,
                'default' => $default ? 1 : 0,
                'autoIncrement' => false,
            ]
        );
    }

    /**
     * @param string $name
     * @param int    $length
     * @param bool   $attribute
     * @param bool   $autoIncrement
     *
     * @return Column
     */
    public static function tinyInt($name, $length = 4, $attribute = Column::UNSIGNED, $autoIncrement = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::TINYINT,
                'value' => $length,
                'attribute' => $attribute,
                'autoIncrement' => $autoIncrement,
            ]
        );
    }

    /**
     * @param string $name
     * @param int    $length
     * @param bool   $attribute
     * @param bool   $autoIncrement
     *
     * @return Column
     */
    public static function smallInt($name, $length = 6, $attribute = Column::UNSIGNED, $autoIncrement = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::SMALLINT,
                'value' => $length,
                'attribute' => $attribute,
                'autoIncrement' => $autoIncrement,
            ]
        );
    }

    /**
     * @param string $name
     * @param int    $length
     * @param bool   $attribute
     * @param bool   $autoIncrement
     *
     * @return Column
     */
    public static function mediumInt($name, $length = 9, $attribute = Column::UNSIGNED, $autoIncrement = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::MEDIUMINT,
                'value' => $length,
                'attribute' => $attribute,
                'autoIncrement' => $autoIncrement,
            ]
        );
    }

    /**
     * @param string $name
     * @param int    $length
     * @param bool   $attribute
     * @param bool   $autoIncrement
     *
     * @return Column
     */
    public static function int($name, $length = 11, $attribute = Column::UNSIGNED, $autoIncrement = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::INT,
                'value' => $length,
                'attribute' => $attribute,
                'autoIncrement' => $autoIncrement,
            ]
        );
    }

    /**
     * @param string $name
     * @param int    $length
     * @param bool   $attribute
     * @param bool   $autoIncrement
     *
     * @return Column
     */
    public static function bigInt($name, $length = 20, $attribute = Column::UNSIGNED, $autoIncrement = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::BIGINT,
                'value' => $length,
                'attribute' => $attribute,
                'autoIncrement' => $autoIncrement,
            ]
        );
    }

    /**
     * @param string $name
     * @param int    $length
     * @param int    $decimal
     * @param bool   $nullable
     *
     * @return Column
     */
    public static function float($name, $length = 8, $decimal = 2, $nullable = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::FLOAT,
                'value' => [$length, $decimal],
                'nullable' => $nullable,
            ]
        );
    }

    /**
     * @param string $name
     * @param int    $length
     * @param int    $decimal
     * @param bool   $nullable
     *
     * @return Column
     */
    public static function double($name, $length = 8, $decimal = 2, $nullable = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::DOUBLE,
                'value' => [$length, $decimal],
                'nullable' => $nullable,
            ]
        );
    }

    /**
     * @param string $name
     *
     * @return Column
     */
    public static function date($name, $nullable = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::DATE,
                'nullable' => $nullable,
            ]
        );
    }

    /**
     * @param string $name
     * @param bool   $nullable
     *
     * @return Column
     */
    public static function dateTime($name, $nullable = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::DATETIME,
                'nullable' => $nullable,
            ]
        );
    }

    /**
     * @param string $name
     * @param string $default
     * @param string $attribute
     * @param bool   $nullable
     *
     * @return Column
     */
    public static function timestamp($name, $default = Column::CURRENT_TIMESTAMP, $attribute = Column::UPDATE_CURRENT_TIMESTAMP, $nullable = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::TIMESTAMP,
                'default' => $default,
                'attribute' => $attribute,
                'nullable' => $nullable,
            ]
        );
    }

    /**
     * @param string $name
     * @param int    $length
     * @param bool   $nullable
     *
     * @return Column
     */
    public static function char($name, $length = 255, $nullable = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::CHAR,
                'value' => $length,
                'nullable' => $nullable,
            ]
        );
    }

    /**
     * @param string $name
     * @param int    $length
     * @param bool   $nullable
     *
     * @return Column
     */
    public static function varchar($name, $length = 255, $nullable = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::VARCHAR,
                'value' => $length,
                'nullable' => $nullable,
            ]
        );
    }

    /**
     * @param string $name
     * @param bool   $nullable
     *
     * @return Column
     */
    public static function text($name, $nullable = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::TEXT,
                'nullable' => $nullable,
            ]
        );
    }

    /**
     * @param string $name
     * @param bool   $nullable
     *
     * @return Column
     */
    public static function binary($name, $nullable = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::BINARY,
                'nullable' => $nullable,
            ]
        );
    }

    /**
     * @param string $name
     * @param bool   $nullable
     *
     * @return Column
     */
    public static function blob($name, $nullable = false)
    {
        return static::create(
            $name,
            [
                'type' => Column::BLOB,
                'nullable' => $nullable,
            ]
        );
    }

    /**
     * @param string $name
     * @param array  $values
     * @param string $default
     *
     * @return Column
     */
    public static function set($name, array $values, $default = null)
    {
        return static::create(
            $name,
            [
                'type' => Column::SET,
                'value' => $values,
                'default' => $default,
            ]
        );
    }

    /**
     * @param string $name
     * @param array  $values
     * @param string $default
     *
     * @return Column
     */
    public static function enum($name, array $values, $default = null)
    {
        return static::create(
            $name,
            [
                'type' => Column::ENUM,
                'value' => $values,
                'default' => $default,
            ]
        );
    }

    /**
     * @param string $name
     * @param array  $definition
     *
     * @return Column
     *
     * @throws \InvalidArgumentException
     */
    public static function create($name, array $definition)
    {
        $column = new Column($name);

        if (!isset($definition['type'])) {
            throw new \InvalidArgumentException(sprintf('The type of "%s" column is not defined.', $name));
        }

        // Type
        $column->setType($definition['type']);

        // Value(s)
        if (!empty($definition['value'])) {
            $column->setValue($definition['value']);
        }

        // Default value
        if (isset($definition['default'])) {
            $column->setDefault($definition['default']);
        }

        // Collating
        if (!empty($definition['collating'])) {
            $column->setCollating($definition['collating']);
        }

        // Attribute
        if (!empty($definition['attribute'])) {
            $column->setAttribute($definition['attribute']);
        }

        // Nullable
        if (isset($definition['nullable'])) {
            $column->setNullable($definition['nullable']);
        }

        // Auto increment
        if (isset($definition['autoIncrement'])) {
            $column->setAutoIncrement($definition['autoIncrement']);
        }

        // Comment
        if (!empty($definition['comment'])) {
            $column->setComment($definition['comment']);
        }

        return $column;
    }
}
