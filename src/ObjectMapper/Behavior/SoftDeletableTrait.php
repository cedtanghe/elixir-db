<?php

namespace Elixir\DB\ObjectMapper\Model\Behavior;

use Elixir\DB\ObjectMapper\ActiveRecordEvent;
use Elixir\DB\ObjectMapper\EntityEvent;
use Elixir\DB\ObjectMapper\FindEvent;
use Elixir\DB\ObjectMapper\SQL\Extension\SoftDeletable;
use Elixir\DB\Query\QueryBuilderInterface;
use Elixir\DB\Query\SQL\ColumnFactory;
use Elixir\DB\Query\SQL\CreateTable;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait SoftDeletableTrait
{
    /**
     * @param CreateTable $create
     */
    public static function build(CreateTable $create)
    {
        $create->column(
            ColumnFactory::timestamp(static::factory()->getDeletedColumn(), null, null, true)
        );
    }

    /**
     * @var bool
     */
    protected $forceDeleting = false;

    /**
     * @throws \LogicException
     * @throws \RuntimeException
     */
    public function bootSoftDeletableTrait()
    {
        $DB = $this->getConnection();

        if (!method_exists($DB, 'getDriver')) {
            throw new \LogicException(
                'Unable to determine the driver of the connection to the database.'
            );
        }

        $driver = $DB->getDriver();

        switch ($driver) {
            case QueryBuilderInterface::DRIVER_MYSQL:
            case QueryBuilderInterface::DRIVER_SQLITE:
                $this->addListener(FindEvent::PRE_FIND, function (FindEvent $e) {
                    $findable = $e->getQuery();
                    $findable->extend(new SoftDeletable($this));
                });
                break;
            default:
                throw new \RuntimeException(sprintf('The driver "%s" is not supported by this behavior.', $driver));
        }

        $this->addListener(EntityEvent::DEFINE_FILLABLE, function (EntityEvent $e) {
            $this->{$this->getDeletedColumn()} = $this->getIgnoreValue();
        });

        $this->addListener(ActiveRecordEvent::PRE_DELETE, function (ActiveRecordEvent $e) {
            if (!$this->forceDeleting) {
                $this->{$this->getDeletedColumn()} = date($this->getDeletedFormat());
                $result = $this->save();

                $e->setQueryExecuted(true);
                $e->setQuerySuccess($result);
            }
        });
    }

    /**
     * @return string
     */
    public function getDeletedColumn()
    {
        return 'deleted_at';
    }

    /**
     * @return string
     */
    public function getDeletedFormat()
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * @return bool
     */
    public function isTrashed()
    {
        return $this->{$this->getDeletedColumn()} !== $this->getIgnoreValue();
    }

    /**
     * @return bool
     */
    public function forceDelete()
    {
        $this->forceDeleting = true;
        $result = $this->delete();
        $this->forceDeleting = false;

        return $result;
    }

    /**
     * @return bool
     */
    public function restore()
    {
        if ($this->isTrashed()) {
            $this->{$this->getDeletedColumn()} = $this->getIgnoreValue();

            return $this->save();
        }

        return true;
    }
}
