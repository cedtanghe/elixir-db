<?php

namespace Elixir\DB\Query\SQL\MySQL;

use Elixir\DB\Query\SQL\DropTable as BaseDropTable;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class DropTable extends BaseDropTable
{
    /**
     * @var boolean 
     */
    protected $temporary = false;

    /**
     * @var boolean 
     */
    protected $ifExists = false;

    /**
     * @param boolean $value
     * @return DropTable
     */
    public function temporary($value) 
    {
        $this->temporary = $value;
        return $this;
    }

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
            case 'temporary':
                $this->temporary(false);
                break;
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
            case 'temporary':
                return $this->temporary;
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
            case 'temporary':
                $this->temporary($data);
                break;
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
        $SQL = 'DROP ' . "\n";
        $SQL .= $this->renderTemporary();
        $SQL .= 'TABLE ' . "\n";
        $SQL .= $this->renderIfExists();
        $SQL .= $this->table;
        
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
