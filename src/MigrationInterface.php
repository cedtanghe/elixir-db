<?php

namespace Elixir\DB;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface MigrationInterface 
{
    /**
     * @return integer
     */
    public function getOrder();
    
    /**
     * @return string
     */
    public function getDescription();
    
    /**
     * @return boolean
     */
    public function up();
    
    /**
     * @return boolean
     */
    public function down();
}
