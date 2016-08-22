<?php

namespace Elixir\DB;

use Elixir\DB\Query\QueryInterface;
use Elixir\DB\ResultSet\ResultSetAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface DBInterface
{
    /**
     * @return int
     */
    public function lastInsertId();

    /**
     * @return bool
     */
    public function begin();

    /**
     * @return bool
     */
    public function rollBack();

    /**
     * @return bool
     */
    public function commit();

    /**
     * @return bool
     */
    public function inTransaction();

    /**
     * @param mixed $value
     * @param int   $type
     *
     * @return mixed
     */
    public function quote($value, $type = null);

    /**
     * @param QueryInterface|string $query
     *
     * @return int
     */
    public function exec($query);

    /**
     * @param QueryInterface|string $query
     * @param array                 $bindings
     *
     * @return ResultSetAbstract|bool
     */
    public function query($query, array $bindings = []);
}
