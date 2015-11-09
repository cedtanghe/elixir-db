<?php

namespace Elixir\DB\Query\SQL\MySQL;

use Elixir\DB\Query\SQL\AlterTable as BaseAlterTable;
use Elixir\DB\Query\SQL\Column;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class AlterTable extends BaseAlterTable
{
    /**
     * @var string 
     */
    const COLLATING = 'collating';

    /**
     * @var string 
     */
    const AFTER = 'after';

    /**
     * @var string 
     */
    const FIRST = 'first';

    /**
     * @see BaseAlterTable::renameColumn()
     * @throws \InvalidArgumentException
     */
    public function renameColumn($oldColumn, $newColumn) 
    {
        if (!$oldColumn instanceof Column && !$newColumn instanceof Column)
        {
            throw new \InvalidArgumentException('Mysql requires the definition of the column to rename it.');
        }

        $this->SQL[] = [
            'specification' => self::RENAME_COLUMN,
            'old_column' => $oldColumn,
            'new_column' => $newColumn
        ];

        return $this;
    }

    /**
     * @see AlterTable::addColumn()
     * @param Column|string $previous
     */
    public function addColumnAfter(Column $column, $previous) 
    {
        $this->SQL[] = [
            'specification' => self::ADD_COLUMN,
            'column' => $column,
            'previous' => $previous instanceof Column ? $previous->getName() : $previous,
            'position' => self::AFTER
        ];

        return $this;
    }

    /**
     * @see AlterTable::addColumn()
     */
    public function addColumnFirst(Column $column) 
    {
        $this->SQL[] = [
            'specification' => self::ADD_COLUMN,
            'column' => $column,
            'position' => self::FIRST
        ];

        return $this;
    }

    /**
     * @param string $collating
     * @return AlterTable
     */
    public function collating($collating) 
    {
        $this->SQL[] = [
            'specification' => self::COLLATING,
            'collating' => $collating
        ];

        return $this;
    }

    /**
     * @param string $part
     * @return AlterTable
     */
    public function reset($part) 
    {
        parent::reset($part);

        switch ($part) 
        {
            case 'collating':
                $i = count($this->SQL);

                while ($i--) 
                {
                    $SQL = $this->SQL[$i];

                    if ($SQL['specification'] == self::COLLATING) 
                    {
                        array_splice($SQL, $i, 1);
                    }
                }
                break;
        }
        
        return $this;
    }
    
    /**
     * @see BaseAlterTable::get()
     */
    public function get($part) 
    {
        switch ($part) 
        {
            case 'collating':
                $data = [];
                
                foreach($this->SQL as $SQL)
                {
                    if ($SQL['specification'] == self::COLLATING)
                    {
                        $data[] = $SQL;
                    }
                }
                
                return $data;
        }
        
        return parent::get($part);
    }
    
    /**
     * @see BaseAlterTable::merge()
     */
    public function merge($data, $part) 
    {
        parent::merge($data, $part);
        
        switch ($part) 
        {
            case 'collating':
                $this->SQL = array_merge($this->SQL, $data);
                break;
        }
        
        return $this;
    }

    /**
     * @see BaseAlterTable::render()
     */
    public function render() 
    {
        $SQL = [];

        foreach ($this->SQL as $SQL) 
        {
            switch ($SQL['specification']) 
            {
                case self::ADD_COLUMN:
                    $SQL[] = $this->renderAddColumn($SQL);
                    break;
                case self::MODIFY_COLUMN:
                    $SQL[] = $this->renderModifyColumn($SQL);
                    break;
                case self::RENAME_COLUMN:
                    $SQL[] = $this->renderRenameColumn($SQL);
                    break;
                case self::DROP_COLUMN:
                    $SQL[] = $this->renderDropColumn($SQL);
                    break;
                case self::ADD_CONSTRAINT:
                    $SQL[] = $this->renderAddConstraint($SQL);
                    break;
                case self::DROP_CONSTRAINT:
                    $SQL[] = $this->renderDropConstraint($SQL);
                    break;
                case self::COLLATING:
                    $SQL[] = $this->renderColating($SQL);
                    break;
            }
        }

        if (null !== $this->rename) 
        {
            $SQL[] = $this->renderRename();
        }

        return implode(';' . "\n", $SQL);
    }

    /**
     * @see BaseAlterTable::renderAddColumn()
     */
    protected function renderAddColumn($data) 
    {
        $column = $data['column'];
        $SQL = 'ALTER TABLE ' . $this->table . ' ADD ';

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
        $updateCurrentTimestamp = false;

        if (null !== $attribute)
        {
            if (strtoupper($attribute) != Column::UPDATE_CURRENT_TIMESTAMP)
            {
                $SQL .= ' ' . $attribute;
            } 
            else
            {
                $updateCurrentTimestamp = true;
            }
        }

        // Collating
        $collating = $column->getCollating();

        if (null !== $collating) 
        {
            $pos = strpos($collating, '_');

            if (false !== $pos) 
            {
                $SQL .= ' ' . sprintf(
                    'CHARACTER SET %s COLLATE %s', 
                    substr($collating, 0, strpos($collating, '_')),
                    $collating
                );
            } 
            else 
            {
                $SQL .= ' CHARACTER SET ' . $collating;
            }
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

        if ($updateCurrentTimestamp)
        {
            $SQL .= ' ' . Column::UPDATE_CURRENT_TIMESTAMP;
        }

        // Comment
        $comment = $column->getComment();

        if (null !== $comment) 
        {
            $SQL .= ' COMMENT ' . $this->quote($comment);
        }

        if (isset($data['position']))
        {
            switch ($data['position']) 
            {
                case self::FIRST:
                    $SQL .= ' FIRST';
                    break;
                case self::AFTER:
                    $SQL .= ' AFTER ' . $data['previous'];
                    break;
            }
        }

        return $SQL;
    }

    /**
     * @see BaseAlterTable::renderModifyColumn()
     */
    protected function renderModifyColumn($data) 
    {
        $column = $data['column'];
        $SQL = 'ALTER TABLE ' . $this->table . ' MODIFY ';

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
        $updateCurrentTimestamp = false;

        if (null !== $attribute)
        {
            if (strtoupper($attribute) != Column::UPDATE_CURRENT_TIMESTAMP) 
            {
                $SQL .= ' ' . $attribute;
            } 
            else
            {
                $updateCurrentTimestamp = true;
            }
        }

        // Collating
        $collating = $column->getCollating();

        if (null !== $collating)
        {
            $pos = strpos($collating, '_');

            if (false !== $pos) 
            {
                $SQL .= ' ' . sprintf(
                    'CHARACTER SET %s COLLATE %s', 
                    substr($collating, 0, strpos($collating, '_')), 
                    $collating
                );
            } 
            else
            {
                $SQL .= ' CHARACTER SET ' . $collating;
            }
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

        if ($updateCurrentTimestamp) 
        {
            $SQL .= ' ' . Column::UPDATE_CURRENT_TIMESTAMP;
        }

        // Comment
        $comment = $column->getComment();

        if (null !== $comment) 
        {
            $SQL .= ' COMMENT ' . $this->quote($comment);
        }

        return $SQL;
    }

    /**
     * @see BaseAlterTable::renderRenameColumn()
     */
    protected function renderRenameColumn($data)
    {
        $column = null;

        if ($data['new_column'] instanceof Column) 
        {
            $newName = $data['new_column']->getName();
            $column = $data['new_column'];
        } 
        else 
        {
            $newName = $data['new_column'];
        }

        if ($data['old_column'] instanceof Column) 
        {
            $oldName = $data['old_column']->getName();

            if (null === $column) 
            {
                $column = $data['old_column'];
            }
        } 
        else
        {
            $oldName = $data['old_column'];
        }

        $SQL = 'ALTER TABLE ' . $this->table . ' CHANGE ' . $oldName . ' ' . $newName;

        // Type
        $SQL .= ' ' . $column->getType();
        $value = $column->getValue();

        if (null !== $value)
        {
            $SQL .= '(' . $this->quote($value) . ')';
        }

        return $SQL;
    }

    /**
     * @return string
     */
    protected function renderColating($data)
    {
        $SQL = 'ALTER TABLE ' . $this->table . ' CONVERT TO ';

        $collating = $data['collating'];
        $pos = strpos($collating, '_');

        if (false !== $pos) 
        {
            $SQL .= sprintf(
                'CHARACTER SET %s COLLATE %s', 
                substr($collating, 0, strpos($collating, '_')), 
                $collating
            );
        } 
        else
        {
            $SQL .= 'CHARACTER SET ' . $collating;
        }

        return $SQL;
    }
}
