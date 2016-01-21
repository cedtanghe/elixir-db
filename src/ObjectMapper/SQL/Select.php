<?php

namespace Elixir\DB\ObjectMapper\SQL;

use Elixir\DB\DBInterface;
use Elixir\DB\ObjectMapper\FindableExtensionInterface;
use Elixir\DB\ObjectMapper\FindableInterface;
use Elixir\DB\ObjectMapper\RelationInterfaceMetas;
use Elixir\DB\ObjectMapper\RepositoryEvent;
use Elixir\DB\ObjectMapper\RepositoryInterface;
use Elixir\DB\ObjectMapper\SQL\EagerLoad;
use Elixir\DB\Query\QueryBuilderInterface;
use Elixir\DB\Query\SQL\SQLInterface;
use Elixir\STDLib\StringUtils;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Select implements FindableInterface 
{
    /**
     * @var RepositoryInterface 
     */
    protected $repository;
    
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
     * @param RepositoryInterface $repository
     * @param mixed $options
     * @throws \LogicException
     */
    public function __construct(RepositoryInterface $repository, $options = null)
    {
        $this->repository = $repository;
        $this->repository->dispatch(
            new RepositoryEvent(
                RepositoryEvent::PRE_FIND, 
                ['query' => $this]
            )
        );
        
        $this->DB = $this->repository->getConnection('db.read');
        
        if (!$this->DB instanceof QueryBuilderInterface)
        {
            throw new \LogicException(
                'This class requires the db object implements the interface "\Elixir\DB\Query\QueryBuilderInterface" for convenience.'
            );
        }
        
        $this->SQL = $this->DB->createSelect('`' . $this->repository->getStockageName() . '`');
    }
    
    /**
     * {@inheritdoc}
     */
    public function extend(FindableExtensionInterface $extension)
    {
        $extension->setFindable($this);
        
        foreach ($extension->getRegisteredMethods() as $method)
        {
            $this->extensions[$method] = $extension;
        }
        
        return $this;
    }
    
    /**
     * @param string $part
     * @return Select
     */
    public function reset($part)
    {
        switch ($part) 
        {
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
     * @return mixed
     */
    public function get($part) 
    {
        switch ($part) 
        {
            case 'extensions':
                return $this->extensions;
            case 'load':
                return $this->load;
            case 'with':
                return $this->with;
        }
    }
    
    /**
     * @param mixed $data
     * @param string $part
     * @return Select
     */
    public function merge($data, $part) 
    {
        switch ($part) 
        {
            case 'extensions':
                foreach ($data as $extension)
                {
                    $this->extend($extension);
                }
                break;
            case 'load':
                foreach ($data as $load)
                {
                    call_user_func_array([$this, 'load'], $load);
                }
                break;
            case 'with':
                foreach ($data as $with)
                {
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
     * @param integer $page
     * @param integer $numberPerPage
     * @return Select
     */
    public function paginate($page, $numberPerPage = 25)
    {
        if($page < 1)
        {
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
        
        if (false === strpos($SQL->render(), 'COUNT('))
        {
            $SQL->column('COUNT(*)', true);
        }
        
        if (false === strpos($SQL->render(), 'GROUP BY'))
        {
            return current($this->DB->query($SQL)->first());
        }
        else
        {
            return count($this->raw());
        }
    }
    
    /**
     * @param string $method
     * @return Select
     */
    public function scope($method)
    {
        if (!in_array($method, $this->scopes))
        {
            $options = [];

            if (func_num_args() > 1) 
            {
                $args = func_get_args();

                array_shift($args);
                $options = $args;
            }

            array_unshift($options, $this);
            call_user_func_array([$this->repository, 'scope' . StringUtils::camelize($method)], $options);
            
            $this->scopes[] = $method;
        }

        return $this;
    }
    
    /**
     * @param string $method
     * @return Select
     */
    public function load($method)
    {
        $options = [];
        
        if (func_num_args() > 1) 
        {
            $args = func_get_args();

            array_shift($args);
            $options = $args;
        }
        
        $this->load[$method] = $options;
        return $this;
    }
    
    /**
     * @param string $member
     * @param EagerLoad $eagerLoad
     * @return Select
     */
    public function with($member, EagerLoad $eagerLoad = null)
    {
        if (null === $eagerLoad)
        {
            $m = explode('.', $member);
            $m = $this->repository->get(array_pop($m));
            
            if ($m instanceof RelationInterfaceMetas)
            {
                $eagerLoad = new EagerLoad(
                    $this->repository,
                    $m->getTarget(),
                    [
                        'foreign_key' => $m->getForeignKey(),
                        'local_key' => $m->getLocalKey(),
                        'pivot' => $m->getPivot(),
                        'type' => $m->getType(),
                        'criterias' => $m->getCriterias()
                    ]
                );
            }
            else
            {
                $parts = explode('\\', get_class($this->repository));
                array_pop($parts);
                
                $class = '\\' . ltrim(implode('\\', $parts) . '\\' . ucfirst($m), '\\');
                $eagerLoad = new EagerLoad(
                    $this->repository,
                    $class, 
                    ['local_key' => $this->repository->getPrimaryKey()]
                );
            }
        }
        
        if (false !== strpos($member, '.')) 
        {
            $members = explode('.', $member);
            $member = array_shift($members);

            if (!isset($this->with[$member]))
            {
                $this->with[$member] = [
                    'with' => [],
                    'eager_load' => null
                ];
            }

            $this->with[$member]['with'][implode('.', $members)] = $eagerLoad;
        }
        else 
        {
            if (!isset($this->with[$members])) 
            {
                $this->with[$member] = [
                    'with' => [],
                    'eager_load' => $eagerLoad
                ];
            } 
            else 
            {
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
        $event = new RepositoryEvent(
            RepositoryEvent::PARSE_QUERY_FIND, 
            ['query' => $this]
        );
        
        $this->repository->dispatch($event);
        $SQL = (string)$event->getQuery();
        
        $result = $this->DB->query($SQL);
        return $result->all();
    }
    
    /**
     * @return Select
     */
    public function current()
    {
        foreach ((array)$this->repository->getPrimaryKey() as $key)
        {
            $this->SQL->where(
                sprintf(
                    '`%s`.`%s` = ?',
                    $this->repository->getStockageName(), 
                    $key
                ), 
                $this->repository->get($key)
            );
        }
        
        return $this;
    }
    
    /**
     * @param integer|array $id
     * @return Select
     * @throws \LogicException
     */
    public function primary($id)
    {
        $key = $this->repository->getPrimaryKey();
        
        if (is_array($key))
        {
            throw new \LogicException('It is impossible to do a search if multiple primary keys are defined.');
        }

        $this->SQL->where(
            sprintf(
                '`%s`.`%s` IN(?)',
                $this->repository->getStockageName(), 
                $key
            ), 
            (array)$id
        );
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function first() 
    {
        $this->SQL->limit(1);
        $repositories = $this->all();
        
        return count($repositories) > 0 ? $repositories[0] : null;
    }
    
    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function all()
    {
        $rows = $this->raw();
        $repositories = [];
        $class = get_class($this->repository);
        
        foreach ($rows as $row)
        {
            $repository = $class::factory();
            $repository->setConnectionManager($this->repository->getConnectionManager());
            $repository->hydrate($row, ['raw' => true, 'sync' => true]);
            
            foreach ($this->load as $member => $arguments) 
            {
                $method = 'load' . StringUtils::camelize($member);

                if (method_exists($repository, $method))
                {
                    call_user_func_array([$repository, $method], $arguments);
                } else 
                {
                    // Use lazy loading
                    $repository->$member;
                }
            }

            $repositories[] = $repository;
        }

        if (count($repositories) > 0)
        {
            foreach ($this->with as $member => $data)
            {
                if (null === $data['eager_load']) 
                {
                    throw new \LogicException(
                        sprintf(
                            'Inconsistency in declaration of eager loading ("%s" must be declared).', 
                            $member
                        )
                    );
                }

                $data['eager_load']->sync($member, $repositories, $data['with']);
            }
        }

        $this->repository->dispatch(new RepositoryEvent(RepositoryEvent::FIND));
        return $repositories;
    }
    
    /**
     * @ignore
     */
    public function __call($name, $arguments)
    {
        if (isset($this->extensions[$name]))
        {
            return call_user_func_array([$this->extensions[$name], $name], $arguments);
        }
        else
        {
            $result = call_user_func_array([$this->SQL, $name], $arguments);

            if ($result instanceof SQLInterface)
            {
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
