<?php

namespace Elixir\DB\Query\SQL;

use Elixir\STDLib\MacroableTrait;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ConstraintFactory
{
    use MacroableTrait;

    /**
     * @param Column|string|array $columns
     *
     * @return Constraint
     */
    public static function index($columns)
    {
        return static::create($columns, ['type' => Constraint::INDEX]);
    }

    /**
     * @param Column|string|array $columns
     *
     * @return Constraint
     */
    public static function primary($columns)
    {
        return static::create($columns, ['type' => Constraint::PRIMARY]);
    }

    /**
     * @param Column|string|array $columns
     *
     * @return Constraint
     */
    public static function unique($columns)
    {
        return static::create($columns, ['type' => Constraint::UNIQUE]);
    }

    /**
     * @param Column|string|array $columns
     *
     * @return Constraint
     */
    public static function fullText($columns)
    {
        return static::create($columns, ['type' => Constraint::FULLTEXT]);
    }

    /**
     * @param Column|string $column
     * @param string        $referenceTable
     * @param string        $referenceColumn
     * @param string        $name
     * @param string        $onDeleteRule
     * @param string        $onUpdateRule
     *
     * @return Constraint
     */
    public static function foreign($column, $referenceTable, $referenceColumn, $name = null, $onDeleteRule = null, $onUpdateRule = null)
    {
        return static::create(
            $column, [
                'type' => Constraint::FOREIGN_KEY,
                'referenceTable' => $referenceTable,
                'referenceColumn' => $referenceColumn,
                'name' => $name,
                'onDeleteRule' => $onDeleteRule,
                'onUpdateRule' => $onUpdateRule,
            ]
        );
    }

    /**
     * @param Column|string|array $columns
     * @param array               $definition
     *
     * @return Constraint
     *
     * @throws \InvalidArgumentException
     */
    public static function create($columns, array $definition)
    {
        $constraint = new Constraint($columns);

        if (empty($definition['type'])) {
            throw new \InvalidArgumentException(
                sprintf('The type of "%s" constraint is not defined.', current($constraint->getColumns()))
            );
        }

        if ($definition['type'] == Constraint::FOREIGN_KEY) {
            if (empty($definition['referenceTable']) || empty($definition['referenceColumn'])) {
                throw new \InvalidArgumentException(
                    sprintf('The reference of "%s" constraint is not defined.', current($constraint->getColumns()))
                );
            }
        }

        // Type
        $constraint->setType($definition['type']);

        // Name
        if (!empty($definition['name'])) {
            $constraint->setName($definition['name']);
        }

        // Reference table
        if (!empty($definition['referenceTable'])) {
            $constraint->setReferenceTable($definition['referenceTable']);
        }

        // Reference column
        if (!empty($definition['referenceColumn'])) {
            $constraint->setReferenceColumn($definition['referenceColumn']);
        }

        // Delete rule
        if (!empty($definition['onDeleteRule'])) {
            $constraint->setOnDeleteRule($definition['onDeleteRule']);
        }

        // Delete rule
        if (!empty($definition['onUpdateRule'])) {
            $constraint->setOnUpdateRule($definition['onUpdateRule']);
        }

        return $constraint;
    }
}
