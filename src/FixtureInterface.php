<?php

namespace Elixir\DB;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface FixtureInterface 
{
    /**
     * @return integer
     */
    public function getOrder();
    
    /**
     * @return boolean
     */
    public function load();
    
    /**
     * @return boolean
     */
    public function unload();
}
