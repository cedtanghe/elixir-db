<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\Column;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Constraint 
{
    /**
     * @var string
     */
    const INDEX = 'INDEX';

    /**
     * @var string
     */
    const PRIMARY = 'PRIMARY';

    /**
     * @var string
     */
    const UNIQUE = 'UNIQUE';

    /**
     * @var string
     */
    const FULLTEXT = 'FULLTEXT';

    /**
     * @var string
     */
    const FOREIGN_KEY = 'FOREIGN KEY';

    /**
     * @var string
     */
    const REFERENCE_RESTRICT = 'RESTRICT';

    /**
     * @var string
     */
    const REFERENCE_CASCADE = 'CASCADE';

    /**
     * @var string
     */
    const REFERENCE_SET_NULL = 'SET NULL';

    /**
     * @var string
     */
    const REFERENCE_NO_ACTION = 'NO ACTION';

    /**
     * @var string
     */
    const REFERENCE_SET_DEFAULT = 'SET DEFAULT';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var string
     */
    protected $referenceTable;

    /**
     * @var string
     */
    protected $referenceColumn;

    /**
     * @var string
     */
    protected $onDeleteRule = self::REFERENCE_NO_ACTION;

    /**
     * @var string
     */
    protected $onUpdateRule = self::REFERENCE_NO_ACTION;

    /**
     * @param string|array $columns
     * @param string $type
     */
    public function __construct($columns = null, $type = null) 
    {
        if (null !== $columns)
        {
            $this->setColumns((array)$columns);
        }

        if (null !== $type) 
        {
            $this->setType($type);
        }
    }

    /**
     * @param string $value
     * @return Constraint
     */
    public function setType($value) 
    {
        $this->type = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() 
    {
        return $this->type;
    }

    /**
     * @param string $value
     * @return Constraint
     */
    public function setName($value) 
    {
        $this->name = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() 
    {
        if (null === $this->name) 
        {
            $this->name = strtolower('fk_' . $this->referenceTable . '_' . $this->referenceColumn . '_' . current($this->getColumns()));
        }

        return $this->name;
    }

    /**
     * @param array $values
     * @return Constraint
     */
    public function setColumns(array $values) 
    {
        $this->columns = [];

        foreach ($values as $column)
        {
            $this->addColumn($column);
        }

        return $this;
    }

    /**
     * @param string|Column $column
     * @return Constraint
     */
    public function addColumn($column) 
    {
        if ($column instanceof Column) {
            $column = $column->getName();
        }

        $this->columns[] = $column;
        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param string $value
     * @return Constraint
     */
    public function setReferenceTable($value) 
    {
        $this->referenceTable = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceTable()
    {
        return $this->referenceTable;
    }

    /**
     * @param string $value
     * @return Constraint
     */
    public function setReferenceColumn($value)
    {
        $this->referenceColumn = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceColumn() 
    {
        return $this->referenceColumn;
    }

    /**
     * @param string $value
     * @return Constraint
     */
    public function setOnDeleteRule($value)
    {
        $this->onDeleteRule = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getOnDeleteRule()
    {
        return $this->onDeleteRule;
    }

    /**
     * @param string $value
     * @return Constraint
     */
    public function setOnUpdateRule($value) 
    {
        $this->onUpdateRule = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getOnUpdateRule() 
    {
        return $this->onUpdateRule;
    }
}
