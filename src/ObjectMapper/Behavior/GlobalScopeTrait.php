<?php

namespace Elixir\DB\ObjectMapper\Model\Behavior;

use Elixir\DB\ObjectMapper\FindEvent;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait GlobalScopeTrait
{
    /**
     * @var array
     */
    protected $globalScopes = [];
    
    /**
     * @return void
     */
    public function bootGlobalScopeTrait()
    {
        $this->addListener(FindEvent::PRE_FIND, function(FindEvent $e)
        {
            $findable = $e->getQuery();
            
            foreach ($this->globalScopes as $method)
            {
                $findable->scope($method);
            }
        });
    }
    
    /**
     * @param string $method
     */
    public function addGlobalScope($method)
    {
        if (!in_array($method, $this->globalScopes))
        {
            $this->globalScopes[] = $method;
        }
    }
    
    /**
     * @return array
     */
    public function getGlobalScopes()
    {
        return $this->globalScopes;
    }
}
