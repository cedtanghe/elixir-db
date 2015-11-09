<?php

namespace Elixir\DB\Query\SQL\MySQL;

use Elixir\DB\Query\SQL\JoinTrait;
use Elixir\DB\Query\SQL\LimitTrait;
use Elixir\DB\Query\SQL\OrderTrait;
use Elixir\DB\Query\SQL\Update as BaseUpdate;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Update extends BaseUpdate 
{
    use OrderTrait;
    use LimitTrait;
    use JoinTrait;
    
    /**
     * @see LimitTrait::reset()
     * @throws \LogicException
     */
    public function offset($offset)
    {
        throw new \LogicException('Not implemented in mysql.');
    }
   
    /**
     * @see BaseUpdate::reset()
     */
    public function reset($part) 
    {
        parent::reset($part);
        
        switch ($part) 
        {
            case 'join':
                $this->join = [];
                break;
            case 'order':
                $this->order = [];
                break;
            case 'limit':
                $this->limit = null;
                break;
        }

        return $this;
    }
    
    /**
     * @see BaseUpdate::get()
     */
    public function get($part) 
    {
        switch ($part) 
        {
            case 'join':
                return $this->join;
            case 'order':
                return $this->order = [];
            case 'limit':
                return $this->limit;
        }
        
        return parent::get($part);
    }
    
    /**
     * @see BaseUpdate::merge()
     */
    public function merge($data, $part) 
    {
        parent::merge($data, $part);
        
        switch ($part) 
        {
            case 'join':
                $this->join = array_merge($this->join, $data);
                break;
            case 'order':
                $this->order = array_merge($this->order, $data);
                break;
            case 'limit':
                $this->limit($data);
                break;
        }
        
        return $this;
    }
   
    /**
     * @see BaseUpdate::render()
     */
    public function render()
    {
        $SQL = 'UPDATE ' . "\n";
        $SQL .= $this->table . ' ' . "\n";
        $SQL .= $this->renderJoin();
        $SQL .= $this->renderSet();
        $SQL .= $this->renderWhere();
        $SQL .= $this->renderOrder();
        $SQL .= $this->renderLimit();

        return trim($this->parseAlias($SQL));
    }
}
