<?php

namespace Elixir\DB;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait DBUtilTrait 
{
    /**
     * @see DBInterface::query()
     * @return array
     */
    public function select($query, array $bindings = [])
    {
        $stmt = $this->query($query, $bindings);
        return $stmt ? $stmt->all() : [];
    }
    
    /**
     * @see DBInterface::query()
     * @return integer
     */
    public function insert($query, array $bindings = [])
    {
        $stmt = $this->query($query, $bindings);
        return $stmt ? $stmt->rowCount() : false;
    }
    
    /**
     * @see DBInterface::query()
     * @return integer
     */
    public function update($query, array $bindings = [])
    {
        $stmt = $this->query($query, $bindings);
        return $stmt ? $stmt->rowCount() : false;
    }
    
    /**
     * @see DBInterface::query()
     * @return integer
     */
    public function delete($query, array $bindings = [])
    {
        $stmt = $this->query($query, $bindings);
        return $stmt ? $stmt->rowCount() : false;
    }

    /**
     * @param callable $callback
     * @return mixed
     * @throws \Exception
     */
    public function transaction(callable $callback)
    {
        $this->begin();
        
        try
        {
            $result = call_user_func_array($callback, [$this]);
            $this->commit();
        }
        catch (\Exception $e)
        {
            $this->rollBack();
            throw $e;
        }
        
        return $result;
    }
}
