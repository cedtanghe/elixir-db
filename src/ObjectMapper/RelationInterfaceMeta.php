<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\ObjectMapper\ActiveRecordInterface;
use Elixir\DB\ObjectMapper\RelationInterface;
use Elixir\DB\ObjectMapper\SQL\Relation\Pivot;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface RelationInterfaceMeta extends RelationInterface 
{
    /**
     * @return ActiveRecordInterface
     */
    public function getModel();
    
    /**
     * @return ActiveRecordInterface
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
