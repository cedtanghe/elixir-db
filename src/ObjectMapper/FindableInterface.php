<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\ObjectMapper\Collection;
use Elixir\DB\ObjectMapper\FindableExtensionInterface;
use Elixir\DB\ObjectMapper\RepositoryInterface;

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
     * @return RepositoryInterface|null
     */
    public function first();
    
    /**
     * @return Collection
     */
    public function all();
}
