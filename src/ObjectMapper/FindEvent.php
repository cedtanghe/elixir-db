<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\ObjectMapper\FindableInterface;
use Elixir\DB\Query\QueryInterface;
use Elixir\Dispatcher\Event;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class FindEvent extends Event
{
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
     * {@inheritdoc}
     * @param array $params
     */
    public function __construct($type, array $params = []) 
    {
        parent::__construct($type);

        $params += ['query' => null];
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
}
