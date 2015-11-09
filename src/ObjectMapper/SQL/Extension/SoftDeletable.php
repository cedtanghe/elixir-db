<?php

namespace Elixir\DB\ObjectMapper\SQL\Extension;

use Elixir\DB\ObjectMapper\FindableExtensionInterface;
use Elixir\DB\ObjectMapper\FindableInterface;
use Elixir\DB\ObjectMapper\RepositoryEvent;
use Elixir\DB\ObjectMapper\RepositoryInterface;

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
     * @var RepositoryInterface 
     */
    protected $repository;
    
    /**
     * @var boolean 
     */
    protected $addConstraint = true;
    
    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->repository->addListener(RepositoryEvent::PARSE_QUERY_FIND, function(RepositoryEvent $e)
        {
            if($this->addConstraint)
            {
                $hasContraint = false;
                
                foreach ($this->findable->get('where') as $where)
                {
                    if (false !== strpos($where, $this->repository->getDeletedColumn()))
                    {
                        $hasContraint = true;
                    }
                }
                
                if (!$hasContraint)
                {
                    $this->findable->where(
                        sprintf(
                            '`%s`.`%s` IS NULL',
                            $this->repository->getStockageName(),
                            $this->repository->getDeletedColumn() 
                        )
                    );
                }
            }
        });
    }

    /**
     * @see FindableExtensionInterface::setFindable()
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
                $this->repository->getStockageName(),
                $this->repository->getDeletedColumn() 
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
                $this->repository->getStockageName(),
                $this->repository->getDeletedColumn() 
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
                $this->repository->getStockageName(),
                $this->repository->getDeletedColumn() 
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
                $this->repository->getStockageName(),
                $this->repository->getDeletedColumn() 
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
            return date($this->repository->getDeletedFormat(), $timestamp);
        }
        else if (is_numeric($date))
        {
            $timestamp = strtotime($date);
            return date($this->repository->getDeletedFormat(), $timestamp);
        }
        
        return $date;
    }

    /**
     * @see FindableExtensionInterface::getRegisteredMethods()
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
