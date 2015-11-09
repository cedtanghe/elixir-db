<?php

namespace Elixir\DB\ObjectMapper\SQL\Extension;

use Elixir\DB\ObjectMapper\FindableExtensionInterface;
use Elixir\DB\ObjectMapper\FindableInterface;
use Elixir\DB\ObjectMapper\RepositoryInterface;

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
     * @var RepositoryInterface 
     */
    protected $repository;
    
    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @see FindableExtensionInterface::setFindable()
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
                $this->repository->getStockageName(),
                $this->repository->getCreatedColumn() 
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
                $this->repository->getStockageName(),
                $this->repository->getUpdatedColumn() 
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
                $this->repository->getStockageName(),
                $this->repository->getCreatedColumn() 
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
                $this->repository->getStockageName(),
                $this->repository->getUpdatedColumn() 
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
                $this->repository->getStockageName(),
                $this->repository->getCreatedColumn() 
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
                $this->repository->getStockageName(),
                $this->repository->getUpdatedColumn() 
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
            'createdBefore',
            'updatedBefore',
            'createdAfter',
            'updatedAfter',
            'createdBetween',
            'updatedBetween'
        ];
    }
}
