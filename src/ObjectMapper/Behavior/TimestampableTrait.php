<?php

namespace Elixir\DB\ObjectMapper\Model\Behavior;

use Elixir\DB\ObjectMapper\RepositoryEvent;
use Elixir\DB\ObjectMapper\SQL\Extension\Timestampable;
use Elixir\DB\Query\QueryBuilderInterface;
use Elixir\DB\Query\SQL\Column;
use Elixir\DB\Query\SQL\ColumnFactory;
use Elixir\DB\Query\SQL\CreateTable;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait TimestampableTrait
{
    /**
     * @param CreateTable $create
     */
    public static function build(CreateTable $create)
    {
        $r = static::factory();
        
        $create->column(
            ColumnFactory::timestamp($r->getCreatedColumn(), Column::CURRENT_TIMESTAMP, null, false)
        );
        
        $create->column(
            ColumnFactory::timestamp($r->getUpdatedColumn(), Column::CURRENT_TIMESTAMP, Column::UPDATE_CURRENT_TIMESTAMP, false)
        );
    }
    
    /**
     * @return void
     */
    public function bootTimestampableTrait()
    {
        $DB = $this->getConnection();
        
        if (method_exists($DB, 'getDriver'))
        {
            $driver = $DB->getDriver();
            
            switch ($driver) 
            {
                case QueryBuilderInterface::DRIVER_MYSQL:
                case QueryBuilderInterface::DRIVER_SQLITE:
                    $this->addListener(RepositoryEvent::PRE_FIND, function(RepositoryEvent $e)
                    {
                        $findable = $e->getQuery();
                        $findable->extend(new Timestampable($this));
                    });
                    break;
            }
        }
        
        $this->addListener(RepositoryEvent::DEFINE_FILLABLE, function(RepositoryEvent $e)
        {
            $this->{$this->getCreatedColumn()} = $this->getIgnoreValue();
            $this->{$this->getUpdatedColumn()} = $this->getIgnoreValue();
        });

        $this->addListener(RepositoryEvent::PRE_INSERT, function(RepositoryEvent $e) 
        {
            $this->touch(false);
        });
        
        $this->addListener(RepositoryEvent::PRE_UPDATE, function(RepositoryEvent $e) 
        {
            $this->touch(false);
        });
    }
    
    /**
     * @return string
     */
    public function getCreatedColumn()
    {
        return 'created_at';
    }
    
    /**
     * @return string
     */
    public function getCreatedFormat()
    {
        return 'Y-m-d H:i:s';
    }
    
    /**
     * @return string
     */
    public function getUpdatedColumn()
    {
        return 'updated_at';
    }
    
    /**
     * @return string
     */
    public function getUpdatedFormat()
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * @param boolean $save
     * @return boolean
     */
    public function touch($save = false) 
    {
        if ($this->{$this->getCreatedColumn()} === $this->getIgnoreValue()) 
        {
            $this->{$this->getCreatedColumn()} = date($this->getCreatedFormat());
            $this->{$this->getUpdatedColumn()} = date($this->getUpdatedFormat(), strtotime($this->{$this->getCreatedColumn()}));
        } 
        else 
        {
            $this->{$this->getUpdatedColumn()} = date($this->getUpdatedFormat());
        }

        if ($save) 
        {
            return $this->save();
        }

        return true;
    }
}
