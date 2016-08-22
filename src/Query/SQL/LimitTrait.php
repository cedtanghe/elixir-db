<?php

namespace Elixir\DB\Query\SQL;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait LimitTrait
{
    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @see LimitOffsetTrait::limit();
     * @see LimitOffsetTrait::offset();
     */
    public function limitOffset($limit, $offset)
    {
        $this->limit($limit);
        $this->offset($offset);

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return SQLInterface
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return SQLInterface
     */
    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return string
     */
    protected function renderLimit()
    {
        $SQL = '';

        if (null !== $this->limit) {
            $SQL .= sprintf('LIMIT %d', $this->limit).' ';
        }

        if (null !== $this->offset) {
            $SQL .= sprintf('OFFSET %d', $this->offset).' ';
        }

        if (!empty($SQL)) {
            $SQL .= "\n";
        }

        return $SQL;
    }
}
