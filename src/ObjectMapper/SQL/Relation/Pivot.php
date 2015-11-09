<?php

namespace Elixir\DB\ObjectMapper\SQL\Relation;

use Elixir\DB\Query\QueryBuilderInterface;
use Elixir\DB\Query\SQL\SQLInterface;
use Elixir\DI\ContainerInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Pivot 
{
    /**
     * @var string
     */
    protected $pivot;

    /**
     * @var string
     */
    protected $firstKey;

    /**
     * @var string
     */
    protected $secondKey;

    /**
     * @var array 
     */
    protected $criterias = [];

    /**
     * @param string $pivot
     * @param array $config
     */
    public function __construct($pivot, array $config = [])
    {
        $this->pivot = $pivot;

        $config += [
            'first_key' => null,
            'second_key' => null,
            'criterias' => []
        ];
        
        $this->firstKey = $config['first_key'];
        $this->secondKey = $config['second_key'];

        foreach ($config['criterias'] as $criteria)
        {
            $this->addCriteria($criteria);
        }
    }

    /**
     * @return string
     */
    public function getPivot()
    {
        return $this->pivot;
    }

    /**
     * @param string $value
     * @return Pivot
     */
    public function setFirstKey($value) 
    {
        $this->firstKey = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstKey() 
    {
        return $this->firstKey;
    }

    /**
     * @param string $value
     * @return Pivot
     */
    public function setSecondKey($value)
    {
        $this->secondKey = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecondKey() 
    {
        return $this->secondKey;
    }

    /**
     * @param callable $criteria
     * @return Pivot
     */
    public function addCriteria(callable $criteria)
    {
        $this->criterias[] = $criteria;
        return $this;
    }

    /**
     * @return array
     */
    public function getCriterias() 
    {
        return $this->criterias;
    }
    
    /**
     * @param ContainerInterface $connectionManager
     * @param mixed $firstValue
     * @param mixed $secondValue
     * @return boolean
     * @throws \LogicException
     */
    public function attach(ContainerInterface $connectionManager, $firstValue, $secondValue)
    {
        $DB = $connectionManager->get('db.write');
        
        if (!$DB instanceof QueryBuilderInterface)
        {
            throw new \LogicException(
                'This class requires the db object implements the interface "\Elixir\DB\Query\QueryBuilderInterface" for convenience.'
            );
        }
        
        try
        {
            $query = $DB->createInsert('`' . $this->pivot . '`');

            if (method_exists($query, 'ignore'))
            {
                $query->ignore(true);
            }
            
            $query->values(
                [
                    $this->firstKey => $firstValue, 
                    $this->secondKey => $secondValue
                ], 
                SQLInterface::VALUES_SET
            );

            $result = $DB->exec($query);
            return $result > 0;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }
    
    /**
     * @param ContainerInterface $connectionManager
     * @param mixed $firstValue
     * @param mixed $secondValue
     * @return boolean
     * @throws \LogicException
     */
    public function detach(ContainerInterface $connectionManager, $firstValue, $secondValue)
    {
        $DB = $connectionManager->get('db.write');
        
        if (!$DB instanceof QueryBuilderInterface)
        {
            throw new \LogicException(
                'This class requires the db object implements the interface "\Elixir\DB\Query\QueryBuilderInterface" for convenience.'
            );
        }
        
        $query = $DB->createDelete('`' . $this->pivot . '`');
        $query->where(sprintf('`%s`.`%s` = ?', $this->pivot, $this->firstKey), $firstValue);
        $query->where(sprintf('`%s`.`%s` = ?', $this->pivot, $this->secondKey), $secondValue);
        
        $result = $DB->exec($query);
        return $result > 0;
    }
}
