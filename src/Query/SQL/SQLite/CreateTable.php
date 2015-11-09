<?php

namespace Elixir\DB\Query\SQL\SQLite;

use Elixir\DB\Query\SQL\Column;
use Elixir\DB\Query\SQL\Constraint;
use Elixir\DB\Query\SQL\CreateTable as BaseCreateTable;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class CreateTable extends BaseCreateTable
{
    /**
     * @var boolean 
     */
    protected $ifNotExists = false;

    /**
     * @param boolean $value
     * @return CreateTable
     */
    public function ifNotExists($value)
    {
        $this->ifNotExists = $value;
        return $this;
    }
    
    /**
     * @see BaseCreateTable::reset()
     */
    public function reset($part) 
    {
        parent::reset($part);
        
        switch ($part) 
        {
            case 'if_not_exists':
                $this->ifNotExists(false);
                break;
        }

        return $this;
    }
    
    /**
     * @see BaseCreateTable::get()
     */
    public function get($part) 
    {
        switch ($part) 
        {
            case 'if_not_exists':
                return $this->ifNotExists;
        }
        
        return parent::get($part);
    }
    
    /**
     * @see BaseCreateTable::merge()
     */
    public function merge($data, $part) 
    {
        parent::merge($data, $part);
        
        switch ($part) 
        {
            case 'if_not_exists':
                $this->ifNotExists($data);
                break;
        }
        
        return $this;
    }

    /**
     * @see BaseCreateTable::render()
     */
    public function render() 
    {
        $SQL = 'CREATE ' . "\n";
        $SQL .= $this->renderTemporary();
        $SQL .= 'TABLE ' . "\n";
        $SQL .= $this->renderIfNotExists();
        $SQL .= $this->table . ' ' . "\n";
        $SQL .= $this->renderColumns();
        $SQL .= $this->renderOptions();

        return trim($SQL);
    }

    /**
     * @return string
     */
    protected function renderIfNotExists()
    {
        $SQL = '';

        if ($this->ifNotExists) 
        {
            $SQL .= 'IF NOT EXISTS ' . "\n";
        }

        return $SQL;
    }

    /**
     * @see BaseCreateTable::renderColumns()
     */
    protected function renderColumns() 
    {
        $SQL = '(';
        $columns = [];

        foreach ($this->columns as $column)
        {
            // Name
            $col = $column->getName();

            // Type
            $col .= ' ' . $column->getType();
            $value = $column->getValue();

            if (null !== $value) 
            {
                $col .= '(' . $this->quote($value) . ')';
            }

            // Attribute
            $attribute = $column->getAttribute();

            if (null !== $attribute) 
            {
                $col .= ' ' . $attribute;
            }

            // Nullable
            $col .= ' ' . ($column->isNullable() ? 'NULL' : 'NOT NULL');

            // AutoIncrement
            if ($column->isAutoIncrement())
            {
                $col .= ' AUTO_INCREMENT ';
                $found = false;

                foreach ($this->constraints as $constraint) 
                {
                    if ($constraint->getType() == Constraint::PRIMARY)
                    {
                        $found = true;
                        break;
                    }
                }

                if (!$found) 
                {
                    $this->constraint(new Constraint($column->getName(), Constraint::PRIMARY));
                }
            }

            // Default
            $default = $column->getDefault();

            if (null !== $default) 
            {
                if ($default != Column::CURRENT_TIMESTAMP) 
                {
                    $default = $this->quote($default);
                }

                $col .= ' DEFAULT ' . $default;
            }

            $columns[] = $col;
        }

        $SQL .= implode(', ' . "\n", $columns);

        // Constraints
        foreach ($this->constraints as $constraint)
        {
            $columns = $constraint->getColumns();

            if ($constraint->getType() == Constraint::PRIMARY)
            {
                $SQL .= ', ' . "\n" . 'PRIMARY KEY (' . implode(', ', $columns) . ')';
            } 
            else if ($constraint->getType() == Constraint::FOREIGN_KEY)
            {
                $SQL .= ', ' . "\n" . 'CONSTRAINT ' . $constraint->getName() . ' ';
                $SQL .= 'FOREIGN KEY (' . $columns[0] . ') ';
                $SQL .= 'REFERENCES ' . $constraint->getReferenceTable() . '(' . $constraint->getReferenceColumn() . ') ';
                $SQL .= 'ON DELETE ' . $constraint->getOnDeleteRule() . ' ';
                $SQL .= 'ON UPDATE ' . $constraint->getOnUpdateRule();
            } 
            else
            {
                foreach ($columns as $column)
                {
                    $SQL .= ', ' . "\n" . $constraint->getType() . ' ' . $column . '(' . $column . ')';
                }
            }
        }

        $SQL .= ') ' . "\n";
        return $SQL;
    }
}
