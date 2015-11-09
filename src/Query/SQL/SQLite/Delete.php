<?php

namespace Elixir\DB\Query\SQLite\MySQL;

use Elixir\DB\Query\SQL\Delete as BaseDelete;
use Elixir\DB\Query\SQL\LimitTrait;
use Elixir\DB\Query\SQL\OrderTrait;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Delete extends BaseDelete 
{
    use OrderTrait;
    use LimitTrait;
    
    /**
     * @see BaseDelete::reset()
     */
    public function reset($part) 
    {
        parent::reset($part);
        
        switch ($part) 
        {
            case 'order':
                $this->order = [];
                break;
            case 'limit':
                $this->limit = null;
                break;
            case 'offset':
                $this->offset = null;
                break;
        }

        return $this;
    }
    
    /**
     * @see BaseDelete::get()
     */
    public function get($part) 
    {
        switch ($part) 
        {
            case 'order':
                return $this->order = [];
            case 'limit':
                return $this->limit;
            case 'offset':
                return $this->offset;
        }
        
        return parent::get($part);
    }
    
    /**
     * @see BaseDelete::merge()
     */
    public function merge($data, $part) 
    {
        parent::merge($data, $part);
        
        switch ($part) 
        {
            case 'order':
                $this->order = array_merge($this->order, $data);
                break;
            case 'limit':
                $this->limit($data);
                break;
            case 'offset':
                $this->offset($data);
                break;
        }
        
        return $this;
    }
   
    /**
     * @see BaseDelete::render()
     */
    public function render()
    {
        $SQL = 'DELETE FROM ' . "\n";
        $SQL .= $this->table . ' ' . "\n";
        $SQL .= $this->renderSet();
        $SQL .= $this->renderWhere();
        $SQL .= $this->renderOrder();
        $SQL .= $this->renderLimit();

        return trim($this->parseAlias($SQL));
    }
    
    /**
     * @see OrderTrait::renderOrder()
     */
    protected function renderOrder()
    {
        $SQL = '';

        if (count($this->order) > 0) 
        {
            $SQL .= 'ORDER BY ';
            $first = true;

            foreach ($this->order as $order) 
            {
                $SQL .= ($first ? '' : ', ') . $order['column'] . (self::ORDER_NONE === $order['type'] ? '' : ' COLLATE NOCASE ' . $order['type']);
                $first = false;
            }

            $SQL .= ' ' . "\n";
        }

        return $SQL;
    }
}
