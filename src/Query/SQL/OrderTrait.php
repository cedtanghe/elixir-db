<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\SQLInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait OrderTrait 
{
    /**
     * @var array 
     */
    protected $order = [];
    
    /**
     * @see OrderTrait::orderBy()
     */
    public function orderAsc($order)
    {
        return $this->order($order, self::ORDER_ASCENDING);
    }
    
    /**
     * @see OrderTrait::orderBy()
     */
    public function orderDesc($order)
    {
        return $this->order($order, self::ORDER_DESCENDING);
    }

    /**
     * @param array|string $order
     * @param string $type
     * @return SQLInterface
     */
    public function order($order, $type = self::ORDER_ASCENDING) 
    {
        foreach ((array)$order as $order) 
        {
            $this->order[] = ['column' => $order, 'type' => $type];
        }
        
        return $this;
    }

    /**
     * @return string
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
                $SQL .= ($first ? '' : ', ') . $order['column'] . (self::ORDER_NONE === $order['type'] ? '' : ' ' . $order['type']);
                $first = false;
            }

            $SQL .= ' ' . "\n";
        }

        return $SQL;
    }
}
