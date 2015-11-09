<?php

namespace Elixir\DB\ObjectMapper\SQL\Extension;

use Elixir\DB\ObjectMapper\FindableExtensionInterface;
use Elixir\DB\ObjectMapper\FindableInterface;
use Elixir\DB\ObjectMapper\RepositoryEvent;
use Elixir\DB\ObjectMapper\RepositoryInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Versionable implements FindableExtensionInterface 
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
                    if (false !== strpos($where, $this->repository->getVersionedColumn()))
                    {
                        $hasContraint = true;
                    }
                }
                
                if (!$hasContraint)
                {
                    $this->findable->where(
                        sprintf(
                            '`%s`.`%s` = ?',
                            $this->repository->getStockageName(),
                            $this->repository->getVersionedColumn() 
                        ),
                        $this->repository->getCurrentVersion()
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
    public function version($value)
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` = ?',
                $this->repository->getStockageName(),
                $this->repository->getVersionedColumn() 
            ),
            $value
        );
        
        $this->addConstraint = false;
        return $this->findable;
    }
    
    /**
     * @return FindableInterface
     */
    public function unversioned()
    {
        $this->addConstraint = false;
        return $this->findable;
    }

    /**
     * @see FindableExtensionInterface::getRegisteredMethods()
     */
    public function getRegisteredMethods() 
    {
        return [
            'version',
            'unversioned'
        ];
    }
}
