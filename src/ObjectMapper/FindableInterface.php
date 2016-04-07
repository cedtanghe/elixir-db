<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\ObjectMapper\EntityInterface;
use Elixir\DB\ObjectMapper\FindableExtensionInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface FindableInterface
{
    /**
     * @param FindableExtensionInterface $extension
     * @return FindableInterface
     */
    public function extend(FindableExtensionInterface $extension);

    /**
     * @return boolean
     */
    public function has();
    
    /**
     * @return integer
     */
    public function count();
    
    /**
     * @return array
     */
    public function raw();
    
    /**
     * @return EntityInterface|null
     */
    public function first();
    
    /**
     * @return array
     */
    public function all();
}
