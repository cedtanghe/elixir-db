<?php

namespace Elixir\DB\ObjectMapper\SQL\Relation;

use Elixir\DB\ObjectMapper\RelationInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Relation implements RelationInterface
{
    /**
     * @var mixed
     */
    protected $related;

    /**
     * @var boolean
     */
    protected $filled = false;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::CUSTOM;
    }

    /**
     * {@inheritdoc}
     */
    public function setRelated($value, array $options = []) 
    {
        $options += ['filled' => true];
        
        $this->related = $value;
        $this->filled = $options['filled'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * {@inheritdoc}
     */
    public function isFilled()
    {
        return $this->filled;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $this->setRelated(call_user_func($this->callback), ['filled' => true]);
    }
}
