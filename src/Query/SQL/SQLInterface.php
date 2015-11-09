<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\QueryInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface SQLInterface extends QueryInterface
{
    /**
     * @var string
     */
    const STAR = '*';

    /**
     * @var string
     */
    const QUANTIFIER_ALL = 'ALL';

    /**
     * @var string
     */
    const QUANTIFIER_DISTINCT = 'DISTINCT';

    /**
     * @var string
     */
    const COMBINE_UNION = 'UNION';

    /**
     * @var string
     */
    const COMBINE_UNION_ALL = 'UNION_ALL';

    /**
     * @var string
     */
    const COMBINE_EXPECT = 'EXPECT';

    /**
     * @var string
     */
    const COMBINE_INTERSECT = 'INTERSECT';

    /**
     * @var string
     */
    const JOIN_CROSS = 'CROSS';

    /**
     * @var string
     */
    const JOIN_INNER = 'INNER';

    /**
     * @var string
     */
    const JOIN_OUTER = 'OUTER';

    /**
     * @var string
     */
    const JOIN_LEFT = 'LEFT';

    /**
     * @var string
     */
    const JOIN_RIGHT = 'RIGHT';
    
    /**
     * @var string
     */
    const JOIN_FULL = 'FULL';

    /**
     * @var string
     */
    const JOIN_NATURAL = 'NATURAL';
    
    /**
     * @var string
     */
    const ORDER_ASCENDING = 'ASC';

    /**
     * @var string
     */
    const ORDER_DESCENDING = 'DESC';

    /**
     * @var string
     */
    const ORDER_NONE = null;
    
    /**
     * @var string
     */
    const OPTION_ENGINE = 'ENGINE';

    /**
     * @var string
     */
    const OPTION_AUTO_INCREMENT = 'AUTO_INCREMENT';

    /**
     * @var string
     */
    const OPTION_COMMENT = 'COMMENT';

    /**
     * @var string
     */
    const OPTION_CHARSET = 'DEFAULT CHARSET';

    /**
     * @var string
     */
    const OPTION_WITHOUT_ROWID = 'WITHOUT ROWID';

    /**
     * @var string
     */
    const ENGINE_INNODB = 'InnoDB';

    /**
     * @var string
     */
    const ENGINE_MYISAM = 'MyISAM';

    /**
     * @var string
     */
    const CHARSET_UTF8 = 'utf8';
    
    /**
     * @var string 
     */
    const VALUES_SET = 'set';
    
    /**
     * @var string 
     */
    const VALUES_ADD = 'add';
    
    /**
     * @var string 
     */
    const VALUES_MERGE = 'merge';

    /**
     * @param callable $value
     */
    public function setQuoteMethod(callable $value);

    /**
     * @return callable
     */
    public function getQuoteMethod();

    /**
     * @param mixed $parameter
     * @return mixed
     */
    public function quote($parameter);

    /**
     * @param string $key
     * @param mixed $value
     */
    public function bindValue($key, $value);

    /**
     * @return array
     */
    public function getBindValues();

    /**
     * @param string $SQL
     * @param mixed $param
     * @return string
     */
    public function assemble($SQL, $param = null);
}
