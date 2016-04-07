<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\ObjectMapper\FindableInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface FindInterface
{
    /**
     * @param mixed $options
     * @return FindableInterface
     */
    public function find($options = null);
}
