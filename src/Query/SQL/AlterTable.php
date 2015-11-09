<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\Column;
use Elixir\DB\Query\SQL\Constraint;
use Elixir\DB\Query\SQL\SQLAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class AlterTable extends SQLAbstract 
{
    /**
     * @var string 
     */
    const ADD_COLUMN = 'add_column';

    /**
     * @var string 
     */
    const MODIFY_COLUMN = 'modify_column';

    /**
     * @var string 
     */
    const RENAME_COLUMN = 'rename_column';

    /**
     * @var string 
     */
    const DROP_COLUMN = 'drop_column';

    /**
     * @var string 
     */
    const ADD_CONSTRAINT = 'add_constraint';

    /**
     * @var string 
     */
    const DROP_CONSTRAINT = 'drop_constraint';
    
    /**
     * @var string 
     */
    protected $rename;

    /**
     * @var array
     */
    protected $SQL = [];

    /**
     * @param string $table
     * @return AlterTable
     */
    public function rename($table) 
    {
        $this->rename = $table;
        return $this;
    }

    /**
     * @param Column $column
     * @return AlterTable
     */
    public function addColumn(Column $column)
    {
        $this->SQL[] = [
            'specification' => self::ADD_COLUMN,
            'column' => $column
        ];

        return $this;
    }

    /**
     * @param Column $column
     * @return AlterTable
     */
    public function modifyColumn(Column $column) 
    {
        $this->SQL[] = [
            'specification' => self::MODIFY_COLUMN,
            'column' => $column,
        ];

        return $this;
    }

    /**
     * @param Column|string $oldColumn
     * @param Column|string $newColumn
     * @return AlterTable
     */
    public function renameColumn($oldColumn, $newColumn) 
    {
        $this->SQL[] = [
            'specification' => self::RENAME_COLUMN,
            'old_name' => $oldColumn instanceof Column ? $oldColumn->getName() : $oldColumn,
            'new_name' => $newColumn instanceof Column ? $newColumn->getName() : $newColumn
        ];

        return $this;
    }

    /**
     * @param string|Column $column
     * @return AlterTable
     */
    public function dropColumn($column) 
    {
        $this->SQL[] = [
            'specification' => self::DROP_COLUMN,
            'column' => $column instanceof Column ? $column->getName() : $column
        ];

        return $this;
    }

    /**
     * @param Constraint $constraint
     * @return AlterTable
     */
    public function addConstraint(Constraint $constraint) 
    {
        $this->SQL[] = [
            'specification' => self::ADD_CONSTRAINT,
            'constraint' => $constraint
        ];

        return $this;
    }

    /**
     * @param string|Constraint $constraint
     * @param string $type
     * @return AlterTable
     * @throws \InvalidArgumentException
     */
    public function dropConstraint($constraint = null, $type = null) 
    {
        if ((null === $constraint && $type != Constraint::PRIMARY) ||
            (null === $type && !$constraint instanceof Constraint)) 
        {
            throw new \InvalidArgumentException('Error while drop constraint.');
        }

        $this->SQL[] = [
            'specification' => self::DROP_CONSTRAINT,
            'constraint' => $constraint,
            'type' => $type ? : $constraint->getType()
        ];

        return $this;
    }

    /**
     * @param string $part
     * @return AlterTable
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
            case 'rename':
                $this->rename = null;
                break;
            case 'data':
                $this->SQL = [];
                break;
            case 'columns':
                $i = count($this->SQL);

                while ($i--) 
                {
                    $SQL = $this->SQL[$i];

                    if (in_array($SQL['specification'], [self::ADD_COLUMN,
                                                         self::MODIFY_COLUMN,
                                                         self::RENAME_COLUMN,
                                                         self::DROP_COLUMN]))
                    {
                        array_splice($SQL, $i, 1);
                    }
                }
                break;
            case 'constraints':
                $i = count($this->SQL);

                while ($i--)
                {
                    $SQL = $this->SQL[$i];

                    if (in_array($SQL['specification'], [self::ADD_CONSTRAINT, self::DROP_CONSTRAINT]))
                    {
                        array_splice($SQL, $i, 1);
                    }
                }
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
            case 'rename':
                return $this->rename;
            case 'data':
                return $this->SQL;
            case 'columns':
                $data = [];
                
                foreach($this->SQL as $SQL)
                {
                    if (in_array($SQL['specification'], [self::ADD_COLUMN,
                                                         self::MODIFY_COLUMN,
                                                         self::RENAME_COLUMN,
                                                         self::DROP_COLUMN]))
                    {
                        $data[] = $SQL;
                    }
                }
                
                return $data;
            case 'constraints':
                $data = [];
                
                foreach($this->SQL as $SQL)
                {
                    if (in_array($SQL['specification'], [self::ADD_CONSTRAINT, self::DROP_CONSTRAINT]))
                    {
                        $data[] = $SQL;
                    }
                }
                
                return $data;
        }
        
        return null;
    }
    
    /**
     * @param mixed $data
     * @param string $part
     * @return AlterTable
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
            case 'rename':
                $this->rename($data);
                break;
            case 'data':
            case 'columns':
            case 'constraints':
                $this->SQL = array_merge($this->SQL, $data);
                break;
        }
        
        return $this;
    }

    /**
     * @see SQLInterface::render()
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
            }
        }

        if (null !== $this->rename) 
        {
            $SQL[] = $this->renderRename();
        }

        return implode(';' . "\n", $SQL);
    }
    
    /**
     * @param array $data
     * @return string
     */
    protected function renderAddColumn($data) 
    {
        $column = $data['column'];
        $SQL = 'ALTER TABLE ADD ';

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
            if (strtoupper($attribute) != Column::UPDATE_CURRENT_TIMESTAMP) 
            {
                $SQL .= ' ' . $attribute;
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

        // Comment
        $comment = $column->getComment();

        if (null !== $comment)
        {
            $SQL .= ' COMMENT ' . $this->quote($comment);
        }

        return $SQL;
    }

    /**
     * @param array $data
     * @return string
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

        if (null !== $attribute) 
        {
            if (strtoupper($attribute) != Column::UPDATE_CURRENT_TIMESTAMP) 
            {
                $SQL .= ' ' . $attribute;
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

        // Comment
        $comment = $column->getComment();

        if (null !== $comment) 
        {
            $SQL .= ' COMMENT ' . $this->quote($comment);
        }

        return $SQL;
    }

    /**
     * @param array $data
     * @return type
     */
    protected function renderRenameColumn($data) 
    {
        return 'ALTER TABLE ' . $this->table . ' RENAME COLUMN ' . $data['old_name'] . ' TO ' . $data['new_name'];
    }
    
    /**
     * @param array $data
     * @return type
     */
    protected function renderDropColumn($data) 
    {
        return 'ALTER TABLE ' . $this->table . ' DROP COLUMN ' . $data['column'];
    }

    /**
     * @param array $data
     * @return type
     */
    protected function renderAddConstraint($data)
    {
        $SQL = 'ALTER TABLE ' . $this->table . ' ADD ';

        $constraint = $data['constraint'];
        $columns = $constraint->getColumns();

        if ($constraint->getType() == Constraint::PRIMARY)
        {
            $SQL .= 'PRIMARY KEY (' . implode(', ', $columns) . ')';
        }
        else if ($constraint->getType() == Constraint::FOREIGN_KEY) 
        {
            $SQL .= 'CONSTRAINT ' . $constraint->getName() . ' ';
            $SQL .= 'FOREIGN KEY (' . $columns[0] . ') ';
            $SQL .= 'REFERENCES ' . $constraint->getReferenceTable() . '(' . $constraint->getReferenceColumn() . ') ';
            $SQL .= 'ON DELETE ' . $constraint->getOnDeleteRule() . ' ';
            $SQL .= 'ON UPDATE ' . $constraint->getOnUpdateRule();
        } 
        else 
        {
            foreach ($columns as $column) 
            {
                $SQL .= $constraint->getType() . ' ' . $column . '(' . $column . ')';
            }
        }

        return $SQL;
    }

    /**
     * @param array $data
     * @return type
     */
    protected function renderDropConstraint($data) 
    {
        $SQL = 'ALTER TABLE ' . $this->table . ' DROP ';

        switch ($data['type']) 
        {
            case Constraint::PRIMARY:
                $SQL .= 'PRIMARY KEY';
                break;
            case Constraint::FOREIGN_KEY:
                $SQL .= 'FOREIGN KEY ' . ($data['constraint'] instanceof Constraint ?
                        $data['constraint']->getName() :
                        $data['constraint']);
                break;
            case Constraint::INDEX:
                $SQL .= 'INDEX ' . ($data['constraint'] instanceof Constraint ?
                        current($data['constraint']->getColumns()) :
                        $data['constraint']);
                break;
        }

        return $SQL;
    }

    /**
     * @return string
     */
    protected function renderRename()
    {
        return 'ALTER TABLE ' . $this->table . ' RENAME TO ' . $this->rename;
    }
}
