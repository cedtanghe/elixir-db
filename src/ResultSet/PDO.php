<?php

namespace Elixir\DB\ResultSet;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class PDO extends ResultSetAbstract
{
    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @ignore
     */
    public function rewind()
    {
        $this->position = 0;

        return $this->resource->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_FIRST);
    }

    /**
     * @ignore
     */
    public function current()
    {
        return $this->resource->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_REL);
    }

    /**
     * @ignore
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @ignore
     */
    public function next()
    {
        return $this->fetch();
    }

    /**
     * @ignore
     */
    public function valid()
    {
        return $this->position <= $this->count();
    }

    /**
     * @ignore
     */
    public function count()
    {
        return $this->resource->rowCount();
    }

    /**
     * {@inheritdoc}
     */
    public function first()
    {
        return $this->next();
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->resource->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function column($column = 0)
    {
        return $this->resource->fetchColumn($column);
    }

    /**
     * @ignore
     */
    public function fetch($fetchStyle = \PDO::FETCH_ASSOC)
    {
        $r = $this->resource->fetch($fetchStyle);

        if (false !== $r) {
            ++$this->position;
        }

        return $r;
    }

    /**
     * @ignore
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->resource, $method], $arguments);
    }
}
