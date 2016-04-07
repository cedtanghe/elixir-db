<?php

namespace Elixir\DB\ObjectMapper\SQL\Extension;

use Elixir\DB\ObjectMapper\ActiveRecordInterface;
use Elixir\DB\ObjectMapper\FindableExtensionInterface;
use Elixir\DB\ObjectMapper\FindableInterface;
use Elixir\DB\ObjectMapper\FindEvent;

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
                    if (false !== strpos($where, $this->model->getVersionedColumn()))
                    {
                        $hasContraint = true;
                    }
                }
                
                if (!$hasContraint)
                {
                    $this->findable->where(
                        sprintf(
                            '`%s`.`%s` = ?',
                            $this->model->getStockageName(),
                            $this->model->getVersionedColumn() 
                        ),
                        $this->model->getCurrentVersion()
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
    public function version($value)
    {
        $this->findable->where(
            sprintf(
                '`%s`.`%s` = ?',
                $this->model->getStockageName(),
                $this->model->getVersionedColumn() 
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
     * {@inheritdoc}
     */
    public function getRegisteredMethods() 
    {
        return [
            'version',
            'unversioned'
        ];
    }
}
