<?php

namespace Elixir\DB\Query;

use Elixir\DB\Query\QueryBuilderInterface;
use Elixir\DB\Query\QueryInterface;
use Elixir\DB\Query\SQL\AlterTable;
use Elixir\DB\Query\SQL\CreateTable;
use Elixir\DB\Query\SQL\Delete;
use Elixir\DB\Query\SQL\DropTable;
use Elixir\DB\Query\SQL\Insert;
use Elixir\DB\Query\SQL\MySQL\AlterTable as MySQLAlterTable;
use Elixir\DB\Query\SQL\MySQL\CreateTable as MySQLCreateTable;
use Elixir\DB\Query\SQL\MySQL\Delete as MySQLDelete;
use Elixir\DB\Query\SQL\MySQL\DropTable as MySQLDropTable;
use Elixir\DB\Query\SQL\MySQL\Insert as MySQLInsert;
use Elixir\DB\Query\SQL\MySQL\Update as MySQLUpdate;
use Elixir\DB\Query\SQL\Select;
use Elixir\DB\Query\SQL\SQLite\AlterTable as SQLiteAlterTable;
use Elixir\DB\Query\SQL\SQLite\CreateTable as SQLiteCreateTable;
use Elixir\DB\Query\SQL\SQLite\Delete as SQLiteDelete;
use Elixir\DB\Query\SQL\SQLite\DropTable as SQLiteDropTable;
use Elixir\DB\Query\SQL\SQLite\Insert as SQLiteInsert;
use Elixir\DB\Query\SQL\SQLite\Select as SQLiteSelect;
use Elixir\DB\Query\SQL\SQLite\Update as SQLiteUpdate;
use Elixir\DB\Query\SQL\TruncateTable;
use Elixir\DB\Query\SQL\Update;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class QueryBuilderFactory 
{
    /**
     * @param string $table
     * @param string $driver
     * @return QueryInterface
     */
    public static function select($table = null, $driver = QueryBuilderInterface::DRIVER_MYSQL) 
    {
        switch (strtolower($driver)) 
        {
            case QueryBuilderInterface::DRIVER_SQLITE:
                return new SQLiteSelect($table);
        }
        
        return new Select($table);
    }

    /**
     * @param string $table
     * @param string $driver
     * @return QueryInterface
     */
    public static function insert($table = null, $driver = QueryBuilderInterface::DRIVER_MYSQL) 
    {
        switch (strtolower($driver))
        {
            case QueryBuilderInterface::DRIVER_MYSQL:
                return new MySQLInsert($table);
            case QueryBuilderInterface::DRIVER_SQLITE:
                return new SQLiteInsert($table);
        }

        return new Insert($table);
    }

    /**
     * @param string $table
     * @param string $driver
     * @return QueryInterface
     */
    public static function update($table = null, $driver = QueryBuilderInterface::DRIVER_MYSQL) 
    {
        switch (strtolower($driver))
        {
            case QueryBuilderInterface::DRIVER_MYSQL:
                return new MySQLUpdate($table);
            case QueryBuilderInterface::DRIVER_SQLITE:
                return new SQLiteUpdate($table);
        }

        return new Update($table);
    }

    /**
     * @param string $table
     * @param string $driver
     * @return QueryInterface
     */
    public static function delete($table = null, $driver = QueryBuilderInterface::DRIVER_MYSQL) 
    {
        switch (strtolower($driver)) 
        {
            case QueryBuilderInterface::DRIVER_MYSQL:
                return new MySQLDelete($table);
            case QueryBuilderInterface::DRIVER_SQLITE:
                return new SQLiteDelete($table);
        }

        return new Delete($table);
    }

    /**
     * @param string $table
     * @param string $driver
     * @return QueryInterface
     */
    public static function createTable($table = null, $driver = QueryBuilderInterface::DRIVER_MYSQL) 
    {
        switch (strtolower($driver))
        {
            case QueryBuilderInterface::DRIVER_MYSQL:
                return new MySQLCreateTable($table);
            case QueryBuilderInterface::DRIVER_SQLITE:
                return new SQLiteCreateTable($table);
        }

        return new CreateTable($table);
    }

    /**
     * @param string $table
     * @param string $driver
     * @return QueryInterface
     */
    public static function createAlterTable($table = null, $driver = QueryBuilderInterface::DRIVER_MYSQL)
    {
        switch (strtolower($driver))
        {
            case QueryBuilderInterface::DRIVER_MYSQL:
                return new MySQLAlterTable($table);
            case QueryBuilderInterface::DRIVER_SQLITE:
                return new SQLiteAlterTable($table);
        }

        return new AlterTable($table);
    }

    /**
     * @param string $table
     * @param string $driver
     * @return QueryInterface
     */
    public static function dropTable($table = null, $driver = QueryBuilderInterface::DRIVER_MYSQL) 
    {
        switch (strtolower($driver)) 
        {
            case QueryBuilderInterface::DRIVER_MYSQL:
                return new MySQLDropTable($table);
            case QueryBuilderInterface::DRIVER_SQLITE:
                return new SQLiteDropTable($table);
        }

        return new DropTable($table);
    }

    /**
     * @param string $table
     * @param string $driver
     * @return QueryInterface
     * @throws \RuntimeException
     */
    public static function truncateTable($table = null, $driver = QueryBuilderInterface::DRIVER_MYSQL)
    {
        switch (strtolower($driver)) 
        {
            case QueryBuilderInterface::DRIVER_SQLITE:
                throw new \RuntimeException('TRUNCATE TABLE command for sqlite does not exist.');
        }

        return new TruncateTable($table);
    }
}
