<?php

namespace Elixir\DB\Query;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait QueryBuilderTrait
{
    /**
     * {@inheritdoc}
     */
    public function createSelect($table = null)
    {
        $select = QueryBuilderFactory::select($table, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($select, 'setQuoteMethod')) {
            $select->setQuoteMethod([$this, 'quote']);
        }

        return $select;
    }

    /**
     * {@inheritdoc}
     */
    public function createInsert($table = null)
    {
        $insert = QueryBuilderFactory::insert($table, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($insert, 'setQuoteMethod')) {
            $insert->setQuoteMethod([$this, 'quote']);
        }

        return $insert;
    }

    /**
     * {@inheritdoc}
     */
    public function createDelete($table = null)
    {
        $delete = QueryBuilderFactory::delete($table, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($delete, 'setQuoteMethod')) {
            $delete->setQuoteMethod([$this, 'quote']);
        }

        return $delete;
    }

    /**
     * {@inheritdoc}
     */
    public function createUpdate($table = null)
    {
        $update = QueryBuilderFactory::update($pTable, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($update, 'setQuoteMethod')) {
            $update->setQuoteMethod([$this, 'quote']);
        }

        return $update;
    }

    /**
     * {@inheritdoc}
     */
    public function createTable($table = null)
    {
        $create = QueryBuilderFactory::createTable($table, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($create, 'setQuoteMethod')) {
            $create->setQuoteMethod([$this, 'quote']);
        }

        return $create;
    }

    /**
     * {@inheritdoc}
     */
    public function createAlterTable($table = null)
    {
        $alter = QueryBuilderFactory::createAlterTable($table, $this->getDriver());

        if (method_exists($this, 'quote') && method_exists($alter, 'setQuoteMethod')) {
            $alter->setQuoteMethod([$this, 'quote']);
        }

        return $alter;
    }

    /**
     * {@inheritdoc}
     */
    public function createDropTable($table = null)
    {
        return QueryBuilderFactory::dropTable($table, $this->getDriver());
    }

    /**
     * {@inheritdoc}
     */
    public function createTruncateTable($table = null)
    {
        return QueryBuilderFactory::truncateTable($table, $this->getDriver());
    }
}
