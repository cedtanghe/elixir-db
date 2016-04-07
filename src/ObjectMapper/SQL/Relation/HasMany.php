<?php

namespace Elixir\DB\ObjectMapper\SQL\Relation;

use Elixir\DB\ObjectMapper\ActiveRecordInterface;
use Elixir\DB\ObjectMapper\SQL\Relation\RelationAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class HasMany extends RelationAbstract 
{
    /**
     * @param ActiveRecordInterface $model
     * @param string|ActiveRecordInterface $target
     * @param array $config
     */
    public function __construct(ActiveRecordInterface $model, $target, array $config = [])
    {
        $this->type = self::HAS_MANY;
        $this->model = $model;
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
     * @param ActiveRecordInterface $target
     * @return boolean
     */
    public function attach(ActiveRecordInterface $target)
    {
        if (null !== $this->pivot)
        {
            $result = $this->pivot->attach(
                $this->model->getConnectionManager(), 
                $this->model->get($this->localKey), 
                $target->get($this->foreignKey)
            );
        }
        else
        {
            $target->set($this->foreignKey, $this->localKey);
            $result = $target->save();
        }
        
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
        
        return $r;
    }
    
    /**
     * @param ActiveRecordInterface $target
     * @return boolean
     */
    public function detach(ActiveRecordInterface $target)
    {
        if (null !== $this->pivot)
        {
            $result = $this->pivot->detach(
                $this->model->getConnectionManager(), 
                $this->model->get($this->localKey), 
                $target->get($this->foreignKey)
            );
        }
        else
        {
            $target->set($this->foreignKey, $target->getIgnoreValue());
            $result = $target->save();
        }
        
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
    
    /**
     * @param ActiveRecordInterface $target
     * @return boolean
     */
    public function detachAndDelete(ActiveRecordInterface $target)
    {
        if (null !== $this->pivot)
        {
            $result = $this->pivot->detach(
                $this->model->getConnectionManager(), 
                $this->model->get($this->localKey), 
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
