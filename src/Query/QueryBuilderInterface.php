<?php

namespace Elixir\DB\Query;

use Elixir\DB\Query\QueryInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface QueryBuilderInterface 
{
    /**
     * @var string
     */
    const DRIVER_MYSQL = 'mysql';

    /**
     * @var string
     */
    const DRIVER_SQLITE = 'sqlite';
    
    /**
     * @return string
     */
    public function getDriver();
    
    /**
     * @param string $table
     * @return QueryInterface
     */
    public function createSelect($table = null);

    /**
     * @param string $table
     * @return QueryInterface
     */
    public function createInsert($table = null);

    /**
     * @param string $table
     * @return QueryInterface
     */
    public function createDelete($table = null);

    /**
     * @param string $table
     * @return QueryInterface
     */
    public function createUpdate($table = null);

    /**
     * @param string $table
     * @return QueryInterface
     */
    public function createTable($table = null);

    /**
     * @param string $table
     * @return QueryInterface
     */
    public function createAlterTable($table = null);

    /**
     * @param string $table
     * @return QueryInterface
     */
    public function createDropTable($table = null);

    /**
     * @param string $table
     * @return QueryInterface
     */
    public function createTruncateTable($table = null);
}
