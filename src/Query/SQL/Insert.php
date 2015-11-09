<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\SQLAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Insert extends SQLAbstract
{
    /**
     * @var boolean 
     */
    protected $raw = false;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var string|array
     */
    protected $values = null;

    /**
     * @param boolean $value
     * @return Insert
     */
    public function raw($value) 
    {
        $this->raw = $value;
        return $this;
    }

    /**
     * @param array $columns
     * @return Insert
     */
    public function columns(array $columns) 
    {
        $this->columns = $columns;
        return $this;
    }
    
    /**
     * @param SQLInterface|string|array $values
     * @param string $type
     * @return Insert
     */
    public function values($values, $type = self::VALUES_SET) 
    {
        if ((is_string($values) && false !== strpos(strtoupper($values), 'SELECT')) || $values instanceof SQLInterface) 
        {
            $this->values = $values;

            if ($values instanceof SQLInterface) 
            {
                $this->values = $this->values->getQuery();
            }
        }
        else 
        {
            if ($type == self::VALUES_SET || !is_array($this->values)) 
            {
                $this->values = [];
            }

            $columns = false;

            foreach ((array)$values as $key => $value) 
            {
                if (!$columns) 
                {
                    if (is_string($key))
                    {
                        $this->columns(array_keys($values));
                        $columns = true;
                    }
                }

                if (is_array($value)) 
                {
                    if (!$columns) 
                    {
                        foreach ($value as $k => $v) 
                        {
                            if (is_string($k)) 
                            {
                                $this->columns(array_keys($value));
                                $columns = true;
                            }

                            break;
                        }
                    }

                    $this->values[] = array_values($value);
                } 
                else 
                {
                    $this->values[] = array_values($values);
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * @param string $part
     * @return Insert
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
            case 'columns':
                $this->columns = [];
                break;
            case 'values':
                $this->values = null;
                break;
            case 'data':
                $this->columns = [];
                $this->values = null;
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
            case 'columns':
                return $this->columns;
            case 'values':
                return $this->values;
        }
        
        return null;
    }
    
    /**
     * @param mixed $data
     * @param string $part
     * @return Insert
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
            case 'columns':
                $this->columns($data);
                break;
            case 'values':
            case 'data':
                $this->values($data, self::VALUES_ADD);
                break;
        }
        
        return $this;
    }

    /**
     * @see SQLInterface::render()
     */
    public function render()
    {
        $SQL = 'INSERT ' . "\n";
        $SQL .= 'INTO ' . $this->table . ' ' . "\n";
        $SQL .= $this->renderColumns();
        $SQL .= $this->renderValues();

        return trim($SQL);
    }

    /**
     * @return string
     */
    protected function renderColumns()
    {
        $SQL = '';

        if (!empty($this->values) && count($this->columns) > 0) 
        {
            $SQL .= '(' . implode(', ' . "\n", $this->columns) . ') ' . "\n";
        }

        return $SQL;
    }

    /**
     * @return string
     */
    protected function renderValues() 
    {
        $SQL = '';

        if (empty($this->values)) 
        {
            $SQL .= 'DEFAULT VALUES';
        }
        
        if (is_string($this->values)) 
        {
            $SQL .= '(' . $this->values . ') ' . "\n";
        } 
        else
        {
            $SQL .= 'VALUES ';
            $first = true;

            foreach ($this->values as $values) 
            {
                if (!$this->raw) 
                {
                    $values = array_map(
                        function($value) 
                        {
                            return $this->quote($value);
                        }, 
                        $values
                    );
                }

                $SQL .= ($first ? '' : ', ') . '(' . implode(', ' . "\n", $values) . ') ' . "\n";
                $first = false;
            }
        }

        return $SQL;
    }
}
