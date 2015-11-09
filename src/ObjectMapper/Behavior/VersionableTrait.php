<?php

namespace Elixir\DB\ObjectMapper\Model\Behavior;

use Elixir\DB\ObjectMapper\RepositoryEvent;
use Elixir\DB\ObjectMapper\SQL\Extension\Versionable;
use Elixir\DB\Query\QueryBuilderInterface;
use Elixir\DB\Query\SQL\ColumnFactory;
use Elixir\DB\Query\SQL\CreateTable;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait VersionableTrait 
{
    /**
     * @param CreateTable $create
     */
    public static function build(CreateTable $create)
    {
        $create->column(
            ColumnFactory::int(static::factory()->getVersionedColumn())
        );
    }
    
    /**
     * @var integer
     */
    protected $recordVersion;
    
    /**
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function bootVersionableTrait() 
    {
        if (!defined('DEFAULT_RECORD_VERSION'))
        {
            define('DEFAULT_RECORD_VERSION', 1);
        }
        
        $DB = $this->getConnection();
        
        if (!method_exists($DB, 'getDriver'))
        {
            throw new \LogicException(
                'Unable to determine the driver of the connection to the database.'
            );
        }

        $driver = $DB->getDriver();
        
        switch ($driver) 
        {
            case QueryBuilderInterface::DRIVER_MYSQL:
            case QueryBuilderInterface::DRIVER_SQLITE:
                $this->addListener(RepositoryEvent::PRE_FIND, function(RepositoryEvent $e)
                {
                    $findable = $e->getQuery();
                    $findable->extend(new Versionable($this));
                });
                break;
            default:
                throw new \RuntimeException(sprintf('The driver "%s" is not supported by this behavior.', $driver));
        }
        
        $this->addListener(RepositoryEvent::DEFINE_FILLABLE, function(RepositoryEvent $e)
        {
            $this->{$this->getVersionedColumn()} = $this->getIgnoreValue();
        });

        $this->addListener(RepositoryEvent::PRE_INSERT, function(RepositoryEvent $e) 
        {
            if (!$this->isVersioned())
            {
                $this->{$this->getVersionedColumn()} = $this->getCurrentVersion();
            }
        });
        
        $this->addListener(RepositoryEvent::PRE_UPDATE, function(RepositoryEvent $e) 
        {
            if (!$this->isVersioned())
            {
                $this->{$this->getVersionedColumn()} = $this->getCurrentVersion();
            }
        });
    }
    
    /**
     * @return integer
     */
    public function getCurrentVersion()
    {
        return $this->recordVersion ?: DEFAULT_RECORD_VERSION;
    }
    
    /**
     * @return string
     */
    public function getVersionedColumn()
    {
        return 'record_version';
    }
    
    /**
     * @return boolean
     */
    public function isVersioned()
    {
        return $this->{$this->getVersionedColumn()} !== $this->getIgnoreValue();
    }
    
    /**
     * @return boolean
     */
    public function isCurrentVersion()
    {
        return $this->{$this->getVersionedColumn()} === $this->getCurrentVersion();
    }
}
