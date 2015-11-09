<?php

namespace Elixir\DB\Query\SQL\SQLite;

use Elixir\DB\Query\SQL\DropTable as BaseDropTable;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class DropTable extends BaseDropTable
{
    /**
     * @var boolean 
     */
    protected $ifExists = false;

    /**
     * @param boolean $value
     * @return DropTable
     */
    public function ifExists($value)
    {
        $this->ifExists = $value;
        return $this;
    }
    
    /**
     * @see BaseDropTable::reset()
     */
    public function reset($part) 
    {
        parent::reset($part);
        
        switch ($part) 
        {
            case 'if_exists':
                $this->ifExists(false);
                break;
        }

        return $this;
    }
    
    /**
     * @see BaseDropTable::get()
     */
    public function get($part) 
    {
        switch ($part) 
        {
            case 'if_exists':
                return $this->ifExists;
        }
        
        return parent::get($part);
    }
    
    /**
     * @see BaseDropTable::merge()
     */
    public function merge($data, $part) 
    {
        parent::merge($data, $part);
        
        switch ($part) 
        {
            case 'if_exists':
                $this->ifExists($data);
                break;
        }
        
        return $this;
    }

    /**
     * @see BaseDropTable::render()
     */
    public function render() 
    {
        $SQL = 'DROP TABLE ' . "\n";
        $SQL .= $this->renderIfExists();
        $SQL .= $this->table;
        
        return trim($SQL);
    }

    /**
     * @return string
     */
    protected function renderIfExists() 
    {
        $SQL = '';

        if ($this->ifExists) 
        {
            $SQL .= 'IF EXISTS ' . "\n";
        }

        return $SQL;
    }
}
