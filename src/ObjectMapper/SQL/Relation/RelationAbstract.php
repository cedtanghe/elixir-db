<?php

namespace Elixir\DB\ObjectMapper\SQL\Relation;

use Elixir\DB\ObjectMapper\ActiveRecordInterface;
use Elixir\DB\ObjectMapper\FindableInterface;
use Elixir\DB\ObjectMapper\RelationInterfaceMeta;
use Elixir\DB\ObjectMapper\SQL\Relation\Pivot;
use Elixir\DB\Query\SQL\JoinClause;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
abstract class RelationAbstract implements RelationInterfaceMeta
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var ActiveRecordInterface 
     */
    protected $model;

    /**
     * @var string|ActiveRecordInterface 
     */
    protected $target;

    /**
     * @var string 
     */
    protected $foreignKey;

    /**
     * @var string 
     */
    protected $localKey;

    /**
     * @var string|boolean|Pivot 
     */
    protected $pivot;

    /**
     * @var array 
     */
    protected $criterias = [];

    /**
     * @var mixed
     */
    protected $related;

    /**
     * @var boolean
     */
    protected $filled = false;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget() 
    {
        if (!$this->target instanceof ActiveRecordInterface) 
        {
            $class = $this->target;
            $this->target = $class::factory();
            $this->target->setConnectionManager($this->model->getConnectionManager());
        }

        return $this->target;
    }

    /**
     * @param string $value
     * @return RelationAbstract
     */
    public function setForeignKey($value)
    {
        $this->foreignKey = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getForeignKey()
    {
        if (null === $this->foreignKey) 
        {
            // Define target
            $this->getTarget();
            
            if (null !== $this->pivot) 
            {
                $this->foreignKey = $this->target->getIdentifier();
            }
            else
            {
                if ($this->type == self::BELONGS_TO)
                {
                    $this->foreignKey = $this->target->getIdentifier();
                }
                else
                {
                    $this->foreignKey = $this->target->getStockageName() . '_id';
                }
            }
        }

        return $this->foreignKey;
    }

    /**
     * @param string $value
     * @return RelationAbstract
     */
    public function setLocalKey($value)
    {
        $this->localKey = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalKey() 
    {
        if (null === $this->localKey)
        {
            if (null !== $this->pivot)
            {
                $this->localKey = $this->model->getIdentifier();
            }
            else
            {
                if ($this->type == self::BELONGS_TO)
                {
                    $this->localKey = $this->model->getStockageName() . '_id';
                }
                else
                {
                    $this->localKey = $this->model->getIdentifier();
                }
            }
        }
        
        return $this->localKey;
    }

    /**
     * @param Pivot $pivot
     * @return RelationAbstract
     */
    public function withPivot(Pivot $pivot)
    {
        $this->pivot = $pPivot;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPivot() 
    {
        if (null !== $this->pivot) 
        {
            // Define target
            $this->getTarget();
            
            if (is_string($this->pivot)) 
            {
                $this->withPivot(new Pivot($this->pivot));
            }
            
            switch ($this->type) 
            {
                case self::HAS_ONE:
                case self::HAS_MANY:
                    if (true === $this->pivot) 
                    {
                        $table = $this->model->getStockageName() . '_' . $this->target->getStockageName();
                        $this->withPivot(new Pivot($table));
                    }
                    
                    if (null === $this->pivot->getFirstKey()) 
                    {
                        $this->pivot->setFirstKey($this->model->getStockageName() . '_id');
                    }

                    if (null === $this->pivot->getSecondKey())
                    {
                        $this->pivot->setSecondKey($this->target->getStockageName() . '_id');
                    }
                    break;
                case self::BELONGS_TO:
                case self::BELONGS_TO_MANY:
                    if (true === $this->pivot) 
                    {
                        $table = $this->target->getStockageName() . '_' . $this->model->getStockageName();
                        $this->withPivot(new Pivot($table));
                    }
            
                    if (null === $this->pivot->getFirstKey()) 
                    {
                        $this->pivot->setFirstKey($this->target->getStockageName() . '_id');
                    }

                    if (null === $this->pivot->getSecondKey())
                    {
                        $this->pivot->setSecondKey($this->model->getStockageName() . '_id');
                    }
                    break; 
            }
        }

        return $this->pivot;
    }

    /**
     * @param callable $criteria
     * @return RelationAbstract
     */
    public function addCriteria(callable $criteria)
    {
        $this->criterias[] = $criteria;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCriterias()
    {
        return $this->criterias;
    }

    /**
     * {@inheritdoc}
     */
    public function setRelated($value, array $options = [])
    {
        $options = array_merge(
            ['filled' => true], 
            $options
        );
        
        $this->related = $value;
        $this->filled = $options['filled'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * {@inheritdoc}
     */
    public function isFilled()
    {
        return $this->filled;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        // Define keys, pivot and target
        $this->getTarget();
        $this->getForeignKey();
        $this->getLocalKey();
        $this->getPivot();

        $findable = $this->target->find();

        if ($this->prepareQuery($findable))
        {
            if ($this->extendQuery($findable))
            {
                $this->setRelated($this->match($findable), ['filled' => true]);
                return;
            }
        }

        switch ($this->type) 
        {
            case self::HAS_ONE:
            case self::BELONGS_TO:
                $this->setRelated(null, ['filled' => true]);
                break;
            case self::HAS_MANY:
            case self::BELONGS_TO_MANY:
                $this->setRelated([], ['filled' => true]);
                break;
        }
    }

    /**
     * @param FindableInterface $findable
     * @return boolean
     */
    protected function prepareQuery(FindableInterface $findable)
    {
        return null !== $this->pivot ? $this->parsePivot($findable) : $this->parseQuery($findable);
    }

    /**
     * @param FindableInterface $findable
     * @return boolean
     */
    protected function parsePivot(FindableInterface $findable) 
    {
        $findable->innerJoin(
            $this->pivot->getPivot(), 
            function(JoinClause $join)
            {
                switch ($this->type) 
                {
                    case self::HAS_ONE:
                    case self::HAS_MANY:
                        $join->on(
                            sprintf(
                                '`%s`.`%s` = ?', 
                                $this->pivot->getPivot(), 
                                $this->pivot->getFirstKey()
                            ), 
                            $this->model->get($this->localKey)
                        );
                        
                        $join->on(
                            sprintf(
                                '`%s`.`%s` = `%s`.`%s`', 
                                $this->pivot->getPivot(), 
                                $this->pivot->getSecondKey(), 
                                $this->target->getStockageName(), 
                                $this->foreignKey
                            )
                        );
                        break;
                    case self::BELONGS_TO:
                    case self::BELONGS_TO_MANY:
                        $join->on(
                            sprintf(
                                '`%s`.`%s` = ?', 
                                $this->pivot->getPivot(), 
                                $this->pivot->getSecondKey()
                            ), 
                            $this->model->get($this->localKey)
                        );
                        
                        $join->on(
                            sprintf(
                                '`%s`.`%s` = `%s`.`%s`', 
                                $this->pivot->getPivot(), 
                                $this->pivot->getFirstKey(), 
                                $this->target->getStockageName(), 
                                $this->foreignKey
                            )
                        );
                        break;
                }

                foreach ($this->pivot->getCriterias() as $criteria)
                {
                    call_user_func_array($criteria, [$join]);
                }
            }
        );

        return true;
    }

    /**
     * @param FindableInterface $findable
     * @return boolean
     */
    protected function parseQuery(FindableInterface $findable)
    {
        $value = $this->model->get($this->localKey);

        if (null === $value)
        {
            return false;
        }

        $findable->where(
            sprintf(
                '`%s`.`%s` = ?', 
                $this->target->getStockageName(), 
                $this->foreignKey
            ), 
            $value
        );

        return true;
    }

    /**
     * @param FindableInterface $findable
     * @return boolean
     */
    protected function extendQuery(FindableInterface $findable)
    {
        foreach ($this->criterias as $criteria) 
        {
            if (false === call_user_func_array($criteria, [$findable, $this]))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @param FindableInterface $findable
     * @return mixed
     */
    protected function match(FindableInterface $findable) 
    {
        switch ($this->type) 
        {
            case self::HAS_ONE:
            case self::BELONGS_TO:
                return $findable->first();
            case self::HAS_MANY:
            case self::BELONGS_TO_MANY:
                return $findable->all();
        }

        return null;
    }
}
