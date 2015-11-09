<?php

namespace Elixir\DB\ObjectMapper\SQL\Relation;

use Elixir\DB\ObjectMapper\Collection;
use Elixir\DB\ObjectMapper\FindableInterface;
use Elixir\DB\ObjectMapper\RelationInterface;
use Elixir\DB\ObjectMapper\RelationInterfaceMetas;
use Elixir\DB\ObjectMapper\RepositoryInterface;
use Elixir\DB\ObjectMapper\SQL\Relation\Pivot;
use Elixir\DB\Query\SQL\JoinClause;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
abstract class BaseAbstract implements RelationInterfaceMetas
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var RepositoryInterface 
     */
    protected $repository;

    /**
     * @var string|RepositoryInterface 
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
     * @see RelationInterface::getType()
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @see RelationInterfaceMetas::getType()
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @see RelationInterfaceMetas::getTarget()
     */
    public function getTarget() 
    {
        if (!$this->target instanceof RepositoryInterface) 
        {
            $class = $this->target;
            $this->target = $class::factory();
            $this->target->setConnectionManager($this->repository->getConnectionManager());
        }

        return $this->target;
    }

    /**
     * @param string $value
     * @return BaseAbstract
     */
    public function setForeignKey($value)
    {
        $this->foreignKey = $value;
        return $this;
    }

    /**
     * @see RelationInterfaceMetas::getForeignKey()
     */
    public function getForeignKey()
    {
        if (null === $this->foreignKey) 
        {
            // Define target
            $this->getTarget();
            
            if (null !== $this->pivot) 
            {
                $this->foreignKey = $this->target->getPrimaryKey();
            }
            else
            {
                if ($this->type == self::BELONGS_TO)
                {
                    $this->foreignKey = $this->target->getPrimaryKey();
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
     * @return BaseAbstract
     */
    public function setLocalKey($value)
    {
        $this->localKey = $value;
        return $this;
    }

    /**
     * @see RelationInterfaceMetas::getLocalKey()
     */
    public function getLocalKey() 
    {
        if (null === $this->localKey)
        {
            if (null !== $this->pivot)
            {
                $this->localKey = $this->repository->getPrimaryKey();
            }
            else
            {
                if ($this->type == self::BELONGS_TO)
                {
                    $this->localKey = $this->repository->getStockageName() . '_id';
                }
                else
                {
                    $this->localKey = $this->repository->getPrimaryKey();
                }
            }
        }
        
        return $this->localKey;
    }

    /**
     * @param Pivot $pivot
     * @return BaseAbstract
     */
    public function withPivot(Pivot $pivot)
    {
        $this->pivot = $pPivot;
        return $this;
    }

    /**
     * @see RelationInterfaceMetas::getPivot()
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
                        $table = $this->repository->getStockageName() . '_' . $this->target->getStockageName();
                        $this->withPivot(new Pivot($table));
                    }
                    
                    if (null === $this->pivot->getFirstKey()) 
                    {
                        $this->pivot->setFirstKey($this->repository->getStockageName() . '_id');
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
                        $table = $this->target->getStockageName() . '_' . $this->repository->getStockageName();
                        $this->withPivot(new Pivot($table));
                    }
            
                    if (null === $this->pivot->getFirstKey()) 
                    {
                        $this->pivot->setFirstKey($this->target->getStockageName() . '_id');
                    }

                    if (null === $this->pivot->getSecondKey())
                    {
                        $this->pivot->setSecondKey($this->repository->getStockageName() . '_id');
                    }
                    break; 
            }
        }

        return $this->pivot;
    }

    /**
     * @param callable $criteria
     * @return BaseAbstract
     */
    public function addCriteria(callable $criteria)
    {
        $this->criterias[] = $criteria;
        return $this;
    }

    /**
     * @see RelationInterfaceMetas::getCriterias()
     */
    public function getCriterias()
    {
        return $this->criterias;
    }

    /**
     * @see RelationInterface::setRelated()
     */
    public function setRelated($value, array $options = [])
    {
        $options = array_merge(
            ['filled' => true], 
            $options
        );
        
        if (is_array($value))
        {
            $value = new Collection($value);
        }

        $this->related = $value;
        $this->filled = $options['filled'];
    }

    /**
     * @see RelationInterface::getRelated()
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * @see RelationInterface::isFilled()
     */
    public function isFilled()
    {
        return $this->filled;
    }

    /**
     * @see RelationInterface::load()
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
                $this->setRelated(new Collection([]), ['filled' => true]);
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
                            $this->repository->get($this->localKey)
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
                            $this->repository->get($this->localKey)
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
        $value = $this->repository->get($this->localKey);

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
                return new Collection($findable->all());
        }

        return null;
    }
}
