<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\ObjectMapper\RepositoryInterface;
use Elixir\DB\ObjectMapper\SQL\Relation\Pivot;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface RelationInterfaceMetas extends RelationInterface 
{
    /**
     * @return RepositoryInterface
     */
    public function getRepository();
    
    /**
     * @return RepositoryInterface
     */
    public function getTarget();
            
    /**
     * @return string
     */
    public function getForeignKey();
    
    /**
     * @return string
     */
    public function getLocalKey();
    
    /**
     * @return Pivot
     */
    public function getPivot();
    
    /**
     * @return array
     */
    public function getCriterias();
}
