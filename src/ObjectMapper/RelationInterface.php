<?php

namespace Elixir\DB\ObjectMapper;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface RelationInterface 
{
    /**
     * @var string
     */
    const HAS_ONE = 'has_one';

    /**
     * @var string
     */
    const HAS_MANY = 'has_many';

    /**
     * @var string
     */
    const BELONGS_TO = 'belongs_to';
    
    /**
     * @var string
     */
    const BELONGS_TO_MANY = 'belongs_to_many';

    /**
     * @var string
     */
    const CUSTOM = 'custom';

    /**
     * @return string
     */
    public function getType();

    /**
     * @param mixed $value
     * @param array $options
     */
    public function setRelated($value, array $options = []);

    /**
     * @return mixed
     */
    public function getRelated();

    /**
     * @return boolean
     */
    public function isFilled();

    /**
     * @return void
     */
    public function load();
}
