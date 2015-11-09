<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\Column;
use Elixir\DB\Query\SQL\Constraint;
use Elixir\DB\Query\SQL\SQLAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class CreateTable extends SQLAbstract 
{
    /**
     * @var boolean 
     */
    protected $temporary = false;

    /**
     * @var array 
     */
    protected $columns = [];

    /**
     * @var array 
     */
    protected $constraints = [];

    /**
     * @var array 
     */
    protected $options = [];

    /**
     * @param boolean $value
     * @return CreateTable
     */
    public function temporary($value)
    {
        $this->temporary = (bool)$value;
        return $this;
    }

    /**
     * @param Column $column
     * @return CreateTable
     */
    public function column(Column $column)
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @param Constraint $constraint
     * @return CreateTable
     */
    public function constraint(Constraint $constraint)
    {
        if ($constraint->getType() == Constraint::PRIMARY) 
        {
            foreach ($this->constraints as $constraint)
            {
                if ($constraint->getType() == Constraint::PRIMARY)
                {
                    $constraint->addColumn($constraint->getName());
                    return;
                }
            }
        }

        $this->constraints[] = $constraint;
        return $this;
    }

    /**
     * @param string $option
     * @param mixed $value
     * @return CreateTable
     */
    public function option($option, $value = null)
    {
        $this->options[$option] = $value;
        return $this;
    }
    
    /**
     * @param string $part
     * @return CreateTable
     */
    public function reset($part) 
    {
        switch ($part) 
        {
            case 'table':
                $this->table = null;
                break;
            case 'alias':
                $this->alias(null);
                break;
            case 'temporary':
                $this->temporary = false;
                break;
            case 'columns':
                $this->columns = [];
                break;
            case 'constraints':
                $this->constraints = [];
                break;
            case 'options':
                $this->options = [];
                break;
        }

        return $this;
    }
    
    /**
     * @param string $part
     * @return mixed
     */
    public function get($part) 
    {
        switch ($part) 
        {
            case 'table':
                return $this->table;
            case 'alias':
                return $this->alias;
            case 'temporary':
                return $this->temporary;
            case 'columns':
                return $this->columns;
            case 'constraints':
                return $this->constraints;
            case 'options':
                return $this->options;
        }
        
        return null;
    }
    
    /**
     * @param mixed $data
     * @param string $part
     * @return CreateTable
     */
    public function merge($data, $part) 
    {
        switch ($part) 
        {
            case 'table':
                $this->table($data);
                break;
            case 'alias':
                $this->alias($data);
                break;
            case 'temporary':
                $this->temporary($data);
                break;
            case 'columns':
                $this->columns = array_merge($this->columns, $data);
                break;
            case 'constraints':
                $this->constraints = array_merge($this->constraints, $data);
                break;
            case 'options':
                $this->options = array_merge($this->options, $data);
                break;
        }
        
        return $this;
    }

    /**
     * @see SQLInterface::render()
     */
    public function render()
    {
        $SQL = 'CREATE ' . "\n";
        $SQL .= $this->renderTemporary();
        $SQL .= 'TABLE ' . "\n";
        $SQL .= $this->table . ' ' . "\n";
        $SQL .= $this->renderColumns();
        $SQL .= $this->renderOptions();

        return trim($SQL);
    }

    /**
     * @return string
     */
    protected function renderTemporary()
    {
        $SQL = '';

        if ($this->temporary)
        {
            $SQL .= 'TEMPORARY ' . "\n";
        }

        return $SQL;
    }

    /**
     * @return string
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
                if (strtoupper($attribute) != Column::UPDATE_CURRENT_TIMESTAMP)
                {
                    $col .= ' ' . $attribute;
                }
            }

            // Collating
            $collating = $column->getCollating();

            if (null !== $collating) 
            {
                $pos = strpos($collating, '_');

                if (false !== $pos) 
                {
                    $col .= ' ' . sprintf(
                        'CHARACTER SET %s COLLATE %s', 
                        substr($collating, 0, strpos($collating, '_')), 
                        $collating
                    );
                }
                else 
                {
                    $col .= ' CHARACTER SET ' . $collating;
                }
            }

            // Nullable
            $col .= ' ' . ($column->isNullable() ? 'NULL' : 'NOT NULL');

            // Auto-increment
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

            // Comment
            $comment = $column->getComment();

            if (null !== $comment) 
            {
                $col .= ' COMMENT ' . $this->quote($comment);
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

    /**
     * @return string
     */
    protected function renderOptions() 
    {
        $SQL = '';

        if (count($this->options) > 0)
        {
            $options = [];

            foreach ($this->options as $key => $value)
            {
                $options[] = $key . ' = ' . $this->quote($value);
            }

            $SQL .= implode(' ' . "\n", $options);
        }

        return $SQL;
    }
}
