<?php

namespace Elixir\DB\ObjectMapper\SQL\Relation;

use Elixir\DB\ObjectMapper\RepositoryInterface;
use Elixir\DB\ObjectMapper\SQL\Relation\BaseAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class HasOne extends BaseAbstract 
{
    /**
     * @param RepositoryInterface $repository
     * @param string|RepositoryInterface $target
     * @param array $config
     */
    public function __construct(RepositoryInterface $repository, $target, array $config = []) 
    {
        $this->type = self::HAS_ONE;
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
        
        if (false !== $config['pivot'])
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
    public function attach(RepositoryInterface $target)
    {
        if (null !== $this->pivot)
        {
            $result = $this->pivot->attach(
                $this->repository->getConnectionManager(), 
                $this->repository->get($this->localKey), 
                $target->get($this->foreignKey)
            );
        }
        else
        {
            $target->set($this->foreignKey, $this->localKey);
            $result = $target->save();
        }
        
        $this->setRelated($target, ['filled' => true]);
        return $result;
    }
    
    /**
     * @param RepositoryInterface $target
     * @return boolean
     */
    public function detach(RepositoryInterface $target)
    {
        if (null !== $this->pivot)
        {
            $result = $this->pivot->detach(
                $this->repository->getConnectionManager(), 
                $this->repository->get($this->localKey), 
                $target->get($this->foreignKey)
            );
        }
        else
        {
            $target->set($this->foreignKey, $target->getIgnoreValue());
            $result = $target->save();
        }
        
        $this->related = null;
        return $result;
    }
    
    /**
     * @param RepositoryInterface $target
     * @return boolean
     */
    public function detachAndDelete(RepositoryInterface $target)
    {
        if (null !== $this->pivot)
        {
            $result = $this->pivot->detach(
                $this->repository->getConnectionManager(), 
                $this->repository->get($this->localKey), 
                $target->get($this->foreignKey)
            );
            
            if ($result)
            {
                $result = $target->delete();
            }
        }
        else
        {
            $target->set($this->foreignKey, $target->getIgnoreValue());
            $result = $target->delete();
        }
        
        $this->related = null;
        return $result;
    }
}
