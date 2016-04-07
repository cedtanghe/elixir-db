<?php

namespace Elixir\DB\ObjectMapper\SQL\Extension;

use Elixir\DB\ObjectMapper\ActiveRecordInterface;
use Elixir\DB\ObjectMapper\FindableExtensionInterface;
use Elixir\DB\ObjectMapper\FindableInterface;
use Elixir\DB\ObjectMapper\FindEvent;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class SoftDeletable implements FindableExtensionInterface 
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
     * @var boolean 
     */
    protected $addConstraint = true;
    
    /**
     * @param ActiveRecordInterface $model
     */
    public function __construct(ActiveRecordInterface $model)
    {
        $this->model = $model;
        $this->model->addListener(FindEvent::PARSE_QUERY_FIND, function(FindEvent $e)
        {
            if($this->addConstraint)
            {
                $hasContraint = false;
                
                foreach ($this->findable->get('where') as $where)
                {
                    if (false !== strpos($where, $this->model->getDeletedColumn()))
                    {
                        $hasContraint = true;
                    }
                }
                
                if (!$hasContraint)
                {
                    $this->findable->where(
                        sprintf(
                            '`%s`.`%s` IS NULL',
                            $this->model->getStockageName(),
                            $this->model->getDeletedColumn() 
                        )
                    );
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setFindable(FindableInterface $value) 
    {
        $this->findable = $value;
    }
    
    /**
     * @return FindableInterface
     */
    public function withTrashed()
    {
        $this->addConstraint = false;
        return $this->findable;
    }
    
    /**
     * @return FindableInterface
     */
    public function onlyTrashed()
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` IS NOT NULL',
                $this->model->getStockageName(),
                $this->model->getDeletedColumn() 
            )
        );
        
        $this->addConstraint = false;
        return $this->findable;
    }
    
    /**
     * @param integer|string|\DateTime $date
     * @return FindableInterface
     */
    public function trashedBefore($date)
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` < ?',
                $this->model->getStockageName(),
                $this->model->getDeletedColumn() 
            ),
            $this->convertDate($date)
        );
        
        $this->addConstraint = false;
        return $this->findable;
    }
    
    /**
     * @param integer|string|\DateTime $date
     * @return FindableInterface
     */
    public function trashedAfter($date)
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` > ?',
                $this->model->getStockageName(),
                $this->model->getDeletedColumn() 
            ),
            $this->convertDate($date)
        );
        
        $this->addConstraint = false;
        return $this->findable;
    }
    
    /**
     * @param integer|string|\DateTime $start
     * @param integer|string|\DateTime $end
     * @return FindableInterface
     */
    public function trashedBetween($start, $end)
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` BETWEEN ? AND ?',
                $this->model->getStockageName(),
                $this->model->getDeletedColumn() 
            ),
            $this->convertDate($start),
            $this->convertDate($end)
        );
        
        $this->addConstraint = false;
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
            'withTrashed',
            'onlyTrashed',
            'trashedBefore',
            'trashedAfter',
            'trashedBetween'
        ];
    }
}
