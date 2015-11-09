<?php

namespace Elixir\DB\ObjectMapper\SQL\Relation;

use Elixir\DB\ObjectMapper\RepositoryInterface;
use Elixir\DB\ObjectMapper\SQL\Relation\BaseAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class BelongsTo extends BaseAbstract 
{
    /**
     * @param RepositoryInterface $repository
     * @param string|RepositoryInterface $target
     * @param array $config
     */
    public function __construct(RepositoryInterface $repository, $target, array $config = []) 
    {
        $this->type = self::BELONGS_TO;
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

        if ( false !== $config['pivot'])
        {
            $this->pivot = $config['pivot'];
        }

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
        if (null !== $this->pivot)
        {
            $result = $this->pivot->attach(
                $target->getConnectionManager(), 
                $target->get($this->foreignKey), 
                $this->repository->get($this->localKey)
            );
        }
        else
        {
            $this->repository->set($this->localKey, $target->get($this->foreignKey));
            $result = $this->repository->save();
        }
        
        $this->setRelated($target, ['filled' => true]);
        return $result;
    }
    
    /**
     * @param RepositoryInterface $target
     * @return boolean
     */
    public function dissociate(RepositoryInterface $target)
    {
        if (null !== $this->pivot)
        {
            $result = $this->pivot->detach(
                $target->getConnectionManager(), 
                $target->get($this->foreignKey), 
                $this->repository->get($this->localKey)
            );
        }
        else
        {
            $this->repository->set($this->localKey, $this->repository->getIgnoreValue());
            $result = $this->repository->save();
        }
        
        $this->related = null;
        return $result;
    }
}
