<?php

namespace Elixir\DB\Query\SQL;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Expr 
{
    /**
     * @param string $expr
     * @return Expr
     */
    public static function protect($expr)
    {
        return new static($expr);
    }

    /**
     * @var string 
     */
    protected $expr;

    /**
     * @param string $expr
     */
    public function __construct($expr) 
    {
        $this->expr = $expr;
    }

    /**
     * @return string
     */
    public function getExpr() 
    {
        return $this->expr;
    }

    /**
     * @ignore
     */
    public function __toString()
    {
        return $this->getExpr();
    }
}
