<?php

namespace Elixir\DB\Query;

use Elixir\DB\Query\QueryBuilderFactory;
use Elixir\DB\Query\QueryBuilderInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait QueryBuilderTrait
{
    /**
     * @see QueryBuilderInterface::createSelect()
     */
    public function createSelect($table = null) 
    {
        $select = QueryBuilderFactory::select($table, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($select, 'setQuoteMethod')) 
        {
            $select->setQuoteMethod([$this, 'quote']);
        }

        return $select;
    }
    
    /**
     * @see QueryBuilderInterface::createInsert()
     */
    public function createInsert($table = null)
    {
        $insert = QueryBuilderFactory::insert($table, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($insert, 'setQuoteMethod')) 
        {
            $insert->setQuoteMethod([$this, 'quote']);
        }

        return $insert;
    }

    /**
     * @see QueryBuilderInterface::createDelete()
     */
    public function createDelete($table = null) 
    {
        $delete = QueryBuilderFactory::delete($table, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($delete, 'setQuoteMethod')) 
        {
            $delete->setQuoteMethod([$this, 'quote']);
        }

        return $delete;
    }

    /**
     * @see QueryBuilderInterface::createUpdate()
     */
    public function createUpdate($table = null)
    {
        $update = QueryBuilderFactory::update($pTable, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($update, 'setQuoteMethod'))
        {
            $update->setQuoteMethod([$this, 'quote']);
        }

        return $update;
    }

    /**
     * @see QueryBuilderInterface::createTable()
     */
    public function createTable($table = null) 
    {
        $create = QueryBuilderFactory::createTable($table, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($create, 'setQuoteMethod')) 
        {
            $create->setQuoteMethod([$this, 'quote']);
        }

        return $create;
    }

    /**
     * @see QueryBuilderInterface::createAlterTable()
     */
    public function createAlterTable($table = null) 
    {
        $alter = QueryBuilderFactory::createAlterTable($table, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($alter, 'setQuoteMethod'))
        {
            $alter->setQuoteMethod([$this, 'quote']);
        }

        return $alter;
    }

    /**
     * @see QueryBuilderInterface::createDropTable()
     */
    public function createDropTable($table = null) 
    {
        return QueryBuilderFactory::dropTable($table, $this->getDriver());
    }

    /**
     * @see QueryBuilderInterface::createTruncateTable()
     */
    public function createTruncateTable($table = null) 
    {
        return QueryBuilderFactory::truncateTable($table, $this->getDriver());
    }
}
