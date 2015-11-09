<?php

namespace Elixir\DB\Query\SQL\SQLite;

use Elixir\DB\Query\SQL\Insert as BaseInsert;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Insert extends BaseInsert 
{
    /**
     * @var boolean
     */
    protected $ignore = false;

    /**
     * @param boolean $value
     * @return Insert
     */
    public function ignore($value = true) 
    {
        $this->ignore = $value;
        return $this;
    }
    
    /**
     * @see BaseInsert::reset()
     */
    public function reset($part) 
    {
        parent::reset($part);
        
        switch ($part) 
        {
            case 'ignore':
                $this->ignore(false);
                break;
        }

        return $this;
    }
    
    /**
     * @see BaseInsert::get()
     */
    public function get($part) 
    {
        switch ($part) 
        {
            case 'ignore':
                return $this->ignore;
        }
        
        return parent::get($part);
    }
    
    /**
     * @see BaseInsert::merge()
     */
    public function merge($data, $part) 
    {
        parent::merge($data, $part);
        
        switch ($part) 
        {
            case 'ignore':
                $this->ignore($data);
                break;
        }
        
        return $this;
    }

    /**
     * @see BaseInsert::render()
     */
    public function render() 
    {
        $SQL = 'INSERT ' . "\n";
        $SQL .= $this->renderIgnore();
        $SQL .= 'INTO ' . $this->table . ' ' . "\n";
        $SQL .= $this->renderColumns();
        $SQL .= $this->renderValues();

        return trim($SQL);
    }
    
    /**
     * @return string
     */
    protected function renderIgnore() 
    {
        $SQL = '';

        if ($this->ignore) 
        {
            $SQL = 'OR IGNORE ' . "\n";
        }

        return $SQL;
    }
}
