<?php

namespace Elixir\DB\ObjectMapper\SQL;

use Elixir\DB\DBInterface;
use Elixir\DB\ObjectMapper\ActiveRecordInterface;
use Elixir\DB\ObjectMapper\FindableExtensionInterface;
use Elixir\DB\ObjectMapper\FindableInterface;
use Elixir\DB\ObjectMapper\FindEvent;
use Elixir\DB\ObjectMapper\RelationInterfaceMeta;
use Elixir\DB\Query\QueryBuilderInterface;
use Elixir\DB\Query\SQL\SQLInterface;
use function Elixir\STDLib\camelize;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Select implements FindableInterface
{
    /**
     * @var ActiveRecordInterface
     */
    protected $model;

    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * @var array
     */
    protected $load = [];

    /**
     * @var array
     */
    protected $with = [];

    /**
     * @var array
     */
    protected $scopes = [];

    /**
     * @var DBInterface
     */
    protected $DB;

    /**
     * @var SQLInterface
     */
    protected $SQL;

    /**
     * @param ActiveRecordInterface $model
     * @param mixed                 $options
     *
     * @throws \LogicException
     */
    public function __construct(ActiveRecordInterface $model, $options = null)
    {
        $this->model = $model;
        $this->model->dispatch(
            new FindEvent(
                FindEvent::PRE_FIND,
                ['query' => $this]
            )
        );

        $this->DB = $this->model->getConnection('db.read');

        if (!$this->DB instanceof QueryBuilderInterface) {
            throw new \LogicException(
                'This class requires the db object implements the interface "\Elixir\DB\Query\QueryBuilderInterface" for convenience.'
            );
        }

        $this->SQL = $this->DB->createSelect('`'.$this->model->getStockageName().'`');
    }

    /**
     * {@inheritdoc}
     */
    public function extend(FindableExtensionInterface $extension)
    {
        $extension->setFindable($this);

        foreach ($extension->getRegisteredMethods() as $method) {
            $this->extensions[$method] = $extension;
        }

        return $this;
    }

    /**
     * @param string $part
     *
     * @return self
     */
    public function reset($part)
    {
        switch ($part) {
            case 'extensions':
                $this->extensions = [];
                break;
            case 'load':
                $this->load = [];
                break;
            case 'with':
                $this->with = [];
                break;
        }

        return $this;
    }

    /**
     * @param string $part
     *
     * @return mixed
     */
    public function get($part)
    {
        switch ($part) {
            case 'extensions':
                return $this->extensions;
            case 'load':
                return $this->load;
            case 'with':
                return $this->with;
        }
    }

    /**
     * @param mixed  $data
     * @param string $part
     *
     * @return self
     */
    public function merge($data, $part)
    {
        switch ($part) {
            case 'extensions':
                foreach ($data as $extension) {
                    $this->extend($extension);
                }
                break;
            case 'load':
                foreach ($data as $load) {
                    call_user_func_array([$this, 'load'], $load);
                }
                break;
            case 'with':
                foreach ($data as $with) {
                    call_user_func_array([$this, 'with'], $with);
                }
                break;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has()
    {
        return $this->count() > 0;
    }

    /**
     * @param int $page
     * @param int $numberPerPage
     *
     * @return self
     */
    public function paginate($page, $numberPerPage = 25)
    {
        if ($page < 1) {
            $page = 1;
        }

        $this->SQL->limit($numberPerPage);
        $this->SQL->offset(($page - 1) * $numberPerPage);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $SQL = clone $this->SQL;

        if (false === strpos($SQL->render(), 'COUNT(')) {
            $SQL->column('COUNT(*)', true);
        }

        if (false === strpos($SQL->render(), 'GROUP BY')) {
            return current($this->DB->query($SQL)->first());
        } else {
            return count($this->raw());
        }
    }

    /**
     * @param string $method
     *
     * @return self
     */
    public function scope($method)
    {
        if (!in_array($method, $this->scopes)) {
            $options = [];

            if (func_num_args() > 1) {
                $args = func_get_args();

                array_shift($args);
                $options = $args;
            }

            array_unshift($options, $this);
            call_user_func_array([$this->model, 'scope'.camelize($method)], $options);

            $this->scopes[] = $method;
        }

        return $this;
    }

    /**
     * @param string $method
     *
     * @return self
     */
    public function load($method)
    {
        $options = [];

        if (func_num_args() > 1) {
            $args = func_get_args();

            array_shift($args);
            $options = $args;
        }

        $this->load[$method] = $options;

        return $this;
    }

    /**
     * @param string    $member
     * @param EagerLoad $eagerLoad
     *
     * @return self
     */
    public function with($member, EagerLoad $eagerLoad = null)
    {
        if (null === $eagerLoad) {
            $m = explode('.', $member);
            $m = $this->model->get(array_pop($m));

            if ($m instanceof RelationInterfaceMeta) {
                $eagerLoad = new EagerLoad(
                    $this->model,
                    $m->getTarget(),
                    [
                        'foreign_key' => $m->getForeignKey(),
                        'local_key' => $m->getLocalKey(),
                        'pivot' => $m->getPivot(),
                        'type' => $m->getType(),
                        'criterias' => $m->getCriterias(),
                    ]
                );
            } else {
                $parts = explode('\\', get_class($this->model));
                array_pop($parts);

                $class = '\\'.ltrim(implode('\\', $parts).'\\'.ucfirst($m), '\\');
                $eagerLoad = new EagerLoad(
                    $this->model,
                    $class,
                    ['local_key' => $this->model->getIdentifier()]
                );
            }
        }

        if (false !== strpos($member, '.')) {
            $members = explode('.', $member);
            $member = array_shift($members);

            if (!isset($this->with[$member])) {
                $this->with[$member] = [
                    'with' => [],
                    'eager_load' => null,
                ];
            }

            $this->with[$member]['with'][implode('.', $members)] = $eagerLoad;
        } else {
            if (!isset($this->with[$members])) {
                $this->with[$member] = [
                    'with' => [],
                    'eager_load' => $eagerLoad,
                ];
            } else {
                $this->with[$member]['eager_load'] = $eagerLoad;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function raw()
    {
        $event = new FindEvent(
            FindEvent::PARSE_QUERY_FIND,
            ['query' => $this]
        );

        $this->model->dispatch($event);
        $SQL = (string) $event->getQuery();

        $result = $this->DB->query($SQL);

        return $result->all();
    }

    /**
     * @return self
     */
    public function current()
    {
        foreach ((array) $this->model->getIdentifier() as $key) {
            $this->SQL->where(
                sprintf(
                    '`%s`.`%s` = ?',
                    $this->model->getStockageName(),
                    $key
                ),
                $this->model->get($key)
            );
        }

        return $this;
    }

    /**
     * @param int|array $id
     *
     * @return self
     *
     * @throws \LogicException
     */
    public function primary($id)
    {
        $key = $this->model->getIdentifier();

        if (is_array($key)) {
            throw new \LogicException('It is impossible to do a search if multiple primary keys are defined.');
        }

        $this->SQL->where(
            sprintf(
                '`%s`.`%s` IN(?)',
                $this->model->getStockageName(),
                $key
            ),
            (array) $id
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function first()
    {
        $this->SQL->limit(1);
        $models = $this->all();

        return count($models) > 0 ? $models[0] : null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     */
    public function all()
    {
        $rows = $this->raw();
        $models = [];
        $class = get_class($this->model);

        foreach ($rows as $row) {
            $model = $class::factory();
            $model->setConnectionManager($this->model->getConnectionManager());
            $model->hydrate($row, ['raw' => true, 'sync' => true]);

            foreach ($this->load as $member => $arguments) {
                $method = 'load'.camelize($member);

                if (method_exists($model, $method)) {
                    call_user_func_array([$model, $method], $arguments);
                } else {
                    // Use lazy loading
                    $model->$member;
                }
            }

            $models[] = $model;
        }

        if (count($models) > 0) {
            foreach ($this->with as $member => $data) {
                if (null === $data['eager_load']) {
                    throw new \LogicException(
                        sprintf(
                            'Inconsistency in declaration of eager loading ("%s" must be declared).',
                            $member
                        )
                    );
                }

                $data['eager_load']->sync($member, $models, $data['with']);
            }
        }

        $this->model->dispatch(new FindEvent(FindEvent::FIND));

        return $models;
    }

    /**
     * @ignore
     */
    public function __call($name, $arguments)
    {
        if (isset($this->extensions[$name])) {
            return call_user_func_array([$this->extensions[$name], $name], $arguments);
        } else {
            $result = call_user_func_array([$this->SQL, $name], $arguments);

            if ($result instanceof SQLInterface) {
                return $this;
            }

            return $result;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->SQL->render();
    }
}
