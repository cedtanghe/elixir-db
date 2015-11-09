<?php

namespace Elixir\DB\Query;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface QueryInterface 
{
    /**
     * @see QueryInterface::render()
     */
    public function getQuery();

    /**
     * @return mixed
     */
    public function render();
}
