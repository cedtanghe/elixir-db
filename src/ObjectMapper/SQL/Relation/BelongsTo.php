<?php

namespace Elixir\DB\ObjectMapper\SQL\Relation;

use Elixir\DB\ObjectMapper\ActiveRecordInterface;
use Elixir\DB\ObjectMapper\SQL\Relation\RelationAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class BelongsTo extends RelationAbstract 
{
    /**
     * @param ActiveRecordInterface $model
     * @param string|ActiveRecordInterface $target
     * @param array $config
     */
    public function __construct(ActiveRecordInterface $model, $target, array $config = []) 
    {
        $this->type = self::BELONGS_TO;
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
     * @param ActiveRecordInterface $target
     * @return boolean
     */
    public function associate(ActiveRecordInterface $target)
    {
        if (null !== $this->pivot)
        {
            $result = $this->pivot->attach(
                $target->getConnectionManager(), 
                $target->get($this->foreignKey), 
                $this->model->get($this->localKey)
            );
        }
        else
        {
            $this->model->set($this->localKey, $target->get($this->foreignKey));
            $result = $this->model->save();
        }
        
        $this->setRelated($target, ['filled' => true]);
        return $result;
    }
    
    /**
     * @param ActiveRecordInterface $target
     * @return boolean
     */
    public function dissociate(ActiveRecordInterface $target)
    {
        if (null !== $this->pivot)
        {
            $result = $this->pivot->detach(
                $target->getConnectionManager(), 
                $target->get($this->foreignKey), 
                $this->model->get($this->localKey)
            );
        }
        else
        {
            $this->model->set($this->localKey, $this->model->getIgnoreValue());
            $result = $this->model->save();
        }
        
        $this->related = null;
        return $result;
    }
}
