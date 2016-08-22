<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\Query\QueryInterface;
use Elixir\Dispatcher\Event;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class ActiveRecordEvent extends Event
{
    /**
     * @var string
     */
    const PRE_UPDATE = 'pre_update';

    /**
     * @var string
     */
    const PARSE_QUERY_UPDATE = 'parse_query_update';

    /**
     * @var string
     */
    const UPDATE = 'update';

    /**
     * @var string
     */
    const PRE_INSERT = 'pre_insert';

    /**
     * @var string
     */
    const PARSE_QUERY_INSERT = 'parse_query_insert';

    /**
     * @var string
     */
    const INSERT = 'insert';

    /**
     * @var string
     */
    const PRE_DELETE = 'pre_delete';

    /**
     * @var string
     */
    const PARSE_QUERY_DELETE = 'parse_query_delete';

    /**
     * @var string
     */
    const DELETE = 'delete';

    /**
     * @var QueryInterface|FindableInterface
     */
    protected $query;

    /**
     * @var booleean
     */
    protected $queryExecuted;

    /**
     * @var booleean
     */
    protected $querySuccess;

    /**
     * {@inheritdoc}
     *
     * @param array $params
     */
    public function __construct($type, array $params = [])
    {
        parent::__construct($type);

        $params += [
            'query' => null,
            'query_executed' => false,
            'query_success' => false,
        ];

        $this->query = $params['query'];
    }

    /**
     * @return QueryInterface|FindableInterface
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param QueryInterface|FindableInterface $value
     */
    public function setQuery($value)
    {
        $this->query = $value;
    }

    /**
     * @return bool
     */
    public function isQueryExecuted()
    {
        return $this->queryExecuted;
    }

    /**
     * @param bool $value
     */
    public function setQueryExecuted($value)
    {
        $this->queryExecuted = $value;
    }

    /**
     * @return bool
     */
    public function isQuerySuccess()
    {
        return $this->querySuccess;
    }

    /**
     * @param bool $value
     */
    public function setQuerySuccess($value)
    {
        $this->querySuccess = $value;
    }
}
