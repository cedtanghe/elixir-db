<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\ObjectMapper\FindableInterface;
use Elixir\DB\Query\QueryInterface;
use Elixir\Dispatcher\EntityEvent;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class RepositoryEvent extends EntityEvent
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
     * @var string
     */
    const PRE_FIND = 'pre_find';
    
    /**
     * @var string
     */
    const PARSE_QUERY_FIND = 'parse_query_find';

    /**
     * @var string
     */
    const FIND = 'find';
    
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
     * @see Event::__contruct()
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
     * @return boolean
     */
    public function isQueryExecuted()
    {
        return $this->queryExecuted;
    }
    
    /**
     * @param boolean $value
     */
    public function setQueryExecuted($value) 
    {
        $this->queryExecuted = $value;
    }
    
    /**
     * @return boolean
     */
    public function isQuerySuccess()
    {
        return $this->querySuccess;
    }
    
    /**
     * @param boolean $value
     */
    public function setQuerySuccess($value) 
    {
        $this->querySuccess = $value;
    }
}
