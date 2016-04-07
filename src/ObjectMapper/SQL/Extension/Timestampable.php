<?php

namespace Elixir\DB\ObjectMapper\SQL\Extension;

use Elixir\DB\ObjectMapper\ActiveRecordInterface;
use Elixir\DB\ObjectMapper\FindableExtensionInterface;
use Elixir\DB\ObjectMapper\FindableInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Timestampable implements FindableExtensionInterface 
{
    /**
     * @var FindableInterface 
     */
    protected $findable;
    
    /**
     * @var ActiveRecordInterface 
     */
    protected $model;
    
    /**
     * @param ActiveRecordInterface $model
     */
    public function __construct(ActiveRecordInterface $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function setFindable(FindableInterface $value) 
    {
        $this->findable = $value;
    }
    
    /**
     * @param integer|string|\DateTime $date
     * @return FindableInterface
     */
    public function createdBefore($date)
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` < ?',
                $this->model->getStockageName(),
                $this->model->getCreatedColumn() 
            ),
            $this->convertDate($date)
        );
        
        return $this->findable;
    }
    
    /**
     * @param integer|string|\DateTime $date
     * @return FindableInterface
     */
    public function updatedBefore($date)
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` < ?',
                $this->model->getStockageName(),
                $this->model->getUpdatedColumn() 
            ),
            $this->convertDate($date)
        );
        
        return $this->findable;
    }
    
    /**
     * @param integer|string|\DateTime $date
     * @return FindableInterface
     */
    public function createdAfter($date)
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` > ?',
                $this->model->getStockageName(),
                $this->model->getCreatedColumn() 
            ),
            $this->convertDate($date)
        );
        
        return $this->findable;
    }
    
    /**
     * @param integer|string|\DateTime $date
     * @return FindableInterface
     */
    public function updatedAfter($date)
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` > ?',
                $this->model->getStockageName(),
                $this->model->getUpdatedColumn() 
            ),
            $this->convertDate($date)
        );
        
        return $this->findable;
    }
    
    /**
     * @param integer|string|\DateTime $start
     * @param integer|string|\DateTime $end
     * @return FindableInterface
     */
    public function createdBetween($start, $end)
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` BETWEEN ? AND ?',
                $this->model->getStockageName(),
                $this->model->getCreatedColumn() 
            ),
            $this->convertDate($start),
            $this->convertDate($end)
        );
        
        return $this->findable;
    }
    
    /**
     * @param integer|string|\DateTime $start
     * @param integer|string|\DateTime $end
     * @return FindableInterface
     */
    public function updatedBetween($start, $end)
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` BETWEEN ? AND ?',
                $this->model->getStockageName(),
                $this->model->getUpdatedColumn() 
            ),
            $this->convertDate($start),
            $this->convertDate($end)
        );
        
        return $this->findable;
    }
    
    /**
     * @param integer|string|\DateTime $date
     * @return integer
     */
    protected function convertDate($date)
    {
        if ($date instanceof \DateTime)
        {
            $timestamp = $date->getTimestamp();
            return date($this->model->getDeletedFormat(), $timestamp);
        }
        else if (is_numeric($date))
        {
            $timestamp = strtotime($date);
            return date($this->model->getDeletedFormat(), $timestamp);
        }
        
        return $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegisteredMethods() 
    {
        return [
            'createdBefore',
            'updatedBefore',
            'createdAfter',
            'updatedAfter',
            'createdBetween',
            'updatedBetween'
        ];
    }
}
