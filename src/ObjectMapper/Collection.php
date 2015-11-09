<?php

namespace Elixir\DB\ObjectMapper;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Collection implements \IteratorAggregate
{
    /**
     * @var array
     */
    protected $data = [];
    
    /**
     * @param array $data
     */
    public function __construct(array $data = []) 
    {
        $this->data = $data;
    }

    /**
     * @param mixed $value
     */
    public function append($value)
    {
        array_push($this->data, $value);
    }

    /**
     * @param mixed $value
     */
    public function prepend($value) 
    {
        array_unshift($this->data, $value);
    }

    /**
     * @param mixed $value
     */
    public function remove($value) 
    {
        $pos = $this->search($value);

        if (false !== $pos) 
        {
            $this->splice($pos, 1);
        }
    }
    
    /**
     * @param mixed $needle
     * @return boolean
     */
    public function in($needle) 
    {
        return in_array($needle, $this->data, true);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function search($value)
    {
        return array_search($value, $this->data);
    }

    /**
     * @param integer $offset
     * @param integer $length
     * @param array $replacement
     * @return array
     */
    public function splice($offset, $length, $replacement = [])
    {
        return array_splice($this->data, $offset, $length, $replacement);
    }
    
    /**
     * @param integer $offset
     * @param integer $length
     * @param boolean $preserveKeys
     * @return array
     */
    public function slice($offset, $length, $preserveKeys = false)
    {
        return array_slice($this->data, $offset, $length, $preserveKeys);
    }
    
    /**
     * @return void
     */
    public function shuffle() 
    {
        shuffle($this->data);
    }
    
    /**
     * @return void
     */
    public function reverse() 
    {
        array_reverse($this->data);
    }
    
    /**
     * @return mixed
     */
    public function shift() 
    {
        return array_shift($this->data);
    }

    /**
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->data);
    }
    
    /**
     * @param string $method
     * @param array $arguments
     * @return boolean
     */
    public function sort($method = 'sort', $arguments = []) 
    {
        return call_user_func_array($method, array_merge($this->data, $arguments));
    }
    
    /**
     * @param \Closure $callback
     */
    public function map(\Closure $callback) 
    {
        array_map($callback, $this->data);
    }
    
    /**
     * @param \Closure $callback
     */
    public function filter(\Closure $callback = null) 
    {
        if (null === $callback)
        {
            array_filter($this->data);
        }
        else
        {
            array_filter($this->data, $callback, ARRAY_FILTER_USE_BOTH);
        }
    }
    
    /**
     * @param array|Collection $data
     */
    public function merge($data) 
    {
        foreach ($data as $value)
        {
            $this->append($value);
        }
    }
    
    /**
     * @ignore
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }
    
    /**
     * @ignore
     */
    public function __debugInfo()
    {
        return $this->data;
    }
}
