<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\ConnectionManager;
use Elixir\DB\DBInterface;
use Elixir\DB\ObjectMapper\EntityInterface;
use Elixir\DB\ObjectMapper\FindInterface;
use Elixir\Dispatcher\DispatcherInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface ActiveRecordInterface extends EntityInterface, FindInterface, DispatcherInterface
{
    /**
     * @return ActiveRecordInterface
     */
    public static function factory(array $config = null);
    
    /**
     * @param ConnectionManager $value
     */
    public function setConnectionManager(ConnectionManager $value);

    /**
     * @return ConnectionManager
     */
    public function getConnectionManager();

    /**
     * @param string $key
     * @return DBInterface
     */
    public function getConnection($key = null);
    
    /**
     * @return string
     */
    public function getStockageName();
    
    /**
     * @return mixed
     */
    public function getIdentifier();
    
    /**
     * @return boolean
     */
    public function save();

    /**
     * @return boolean
     */
    public function insert();

    /**
     * @param array $members
     * @param array $omitMembers
     * @return boolean
     */
    public function update(array $members = [], array $omitMembers = []);

    /**
     * @return boolean
     */
    public function delete();
}
