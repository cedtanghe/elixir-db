<?php

namespace Elixir\DB\Query\SQL\SQLite;

use Elixir\DB\Query\SQL\AlterTable as BaseAlterTable;
use Elixir\DB\Query\SQL\Column;
use Elixir\DB\Query\SQL\Constraint;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class AlterTable extends BaseAlterTable 
{
    /**
     * @see BaseAlterTable::modifyColumn()
     * @throws \LogicException
     */
    public function modifyColumn(Column $column) 
    {
        throw new \LogicException('Not implemented in sqlite.');
    }

    /**
     * @see BaseAlterTable::renameColumn()
     * @throws \LogicException
     */
    public function renameColumn($oldColumn, $newColumn) 
    {
        throw new \LogicException('Not implemented in sqlite.');
    }

    /**
     * @see BaseAlterTable::dropColumn()
     * @throws \LogicException
     */
    public function dropColumn($column)
    {
        throw new \LogicException('Not implemented in sqlite.');
    }

    /**
     * @see BaseAlterTable::addConstraint()
     * @throws \LogicException
     */
    public function addConstraint(Constraint $constraint)
    {
        throw new \LogicException('Not implemented in sqlite.');
    }

    /**
     * @see BaseAlterTable::dropConstraint()
     * @throws \LogicException
     */
    public function dropConstraint($constraint = null, $type = null) 
    {
        throw new \LogicException('Not implemented in sqlite.');
    }

    /**
     * @see BaseAlterTable::renderAddColumn()
     */
    protected function renderAddColumn($data) 
    {
        $column = $data['column'];
        $SQL = 'ALTER TABLE ' . $this->table . ' ADD COLUMN ';

        // Name
        $SQL .= $column->getName();

        // Type
        $SQL .= ' ' . $column->getType();
        $value = $column->getValue();

        if (null !== $value)
        {
            $SQL .= '(' . $this->quote($value) . ')';
        }

        // Attribute
        $attribute = $column->getAttribute();

        if (null !== $attribute) 
        {
            $SQL .= ' ' . $attribute;
        }

        // Nullable
        $SQL .= ' ' . ($column->isNullable() ? 'NULL' : 'NOT NULL');

        // AutoIncrement
        if ($column->isAutoIncrement()) 
        {
            $SQL .= ' AUTO_INCREMENT PRIMARY KEY';
        }

        // Default
        $default = $column->getDefault();

        if (null !== $default) 
        {
            if ($default != Column::CURRENT_TIMESTAMP)
            {
                $default = $this->quote($default);
            }

            $SQL .= ' DEFAULT ' . $default;
        }

        return $SQL;
    }
}
