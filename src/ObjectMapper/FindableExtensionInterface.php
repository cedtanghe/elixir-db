<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\ObjectMapper\FindableInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface FindableExtensionInterface
{
    /**
     * @param FindableInterface $value
     */
    public function setFindable(FindableInterface $value);
    
    /**
     * @return array
     */
    public function getRegisteredMethods();
}
