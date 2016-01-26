<?php

namespace Elixir\DB\ObjectMapper\SQL\Relation;

use Elixir\DB\ObjectMapper\RepositoryInterface;
use Elixir\DB\ObjectMapper\SQL\Relation\RelationAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class BelongsToMany extends RelationAbstract 
{
    /**
     * @param RepositoryInterface $repository
     * @param string|RepositoryInterface $target
     * @param array $config
     */
    public function __construct(RepositoryInterface $repository, $target, array $config = [])
    {
        $this->type = self::BELONGS_TO_MANY;
        $this->repository = $repository;
        $this->target = $target;
        
        $config += [
            'foreign_key' => null,
            'local_key' => null,
            'pivot' => null,
            'criterias' => []
        ];

        $this->foreignKey = $config['foreign_key'];
        $this->localKey = $config['local_key'];
        $this->pivot = null !== $config['pivot'] && false !== $config['pivot'] ? $config['pivot'] : true;

        foreach ($config['criterias'] as $criteria)
        {
            $this->addCriteria($criteria);
        }
    }
    
    /**
     * @param RepositoryInterface $target
     * @return boolean
     */
    public function associate(RepositoryInterface $target)
    {
        $result = $this->pivot->attach(
            $target->getConnectionManager(), 
            $target->get($this->foreignKey), 
            $this->repository->get($this->localKey)
        );
        
        if (null !== $this->related)
        {
            if (!in_array($target, $this->related, true))
            {
                $this->related[] = $target;
            }
        }
        else
        {
            $this->setRelated([$target], ['filled' => true]);
        }
        
        return $result;
    }
    
    /**
     * @param RepositoryInterface $target
     * @return boolean
     */
    public function dissociate(RepositoryInterface $target)
    {
        $result = $this->pivot->detach(
            $target->getConnectionManager(), 
            $target->get($this->foreignKey), 
            $this->repository->get($this->localKey)
        );
        
        if (null !== $this->related)
        {
            $pos = array_search($target, $this->related);
            
            if (false !== $pos) 
            {
                array_splice($this->related, $pos, 1);
            }
        }
        
        return $result;
    }
}
