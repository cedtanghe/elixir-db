<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\ConnectionManager;
use Elixir\DB\DBInterface;
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
     *
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
     * @return bool
     */
    public function save();

    /**
     * @return bool
     */
    public function insert();

    /**
     * @param array $members
     * @param array $omitMembers
     *
     * @return bool
     */
    public function update(array $members = [], array $omitMembers = []);

    /**
     * @return bool
     */
    public function delete();
}
