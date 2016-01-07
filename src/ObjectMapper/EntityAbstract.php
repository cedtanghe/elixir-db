<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\DB\ObjectMapper\EntityEvent;
use Elixir\Dispatcher\DispatcherTrait;
use Elixir\STDLib\StringUtils;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
abstract class EntityAbstract implements EntityInterface, \JsonSerializable
{
    use DispatcherTrait;
    
    /**
     * @see EntityInterface::factory()
     */
    public static function factory(array $config = null)
    {
        return new static($config);
    }
    
    /**
     * @var array
     */
    protected static $mutatorsGet = [];

    /**
     * @var array
     */
    protected static $mutatorsSet = [];
    
    /**
     * @var string
     */
    protected $className;
    
    /**
     * @var mixed
     */
    protected $ignoreValue = self::IGNORE_VALUE;

    /**
     * @var array
     */
    protected $fillable = [];
    
    /**
     * @var array
     */
    protected $guarded = [];
    
    /**
     * @var array
     */
    protected $original = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $state = self::FILLABLE;
    
    /**
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        $this->className = get_class($this);
        
        $this->state = self::FILLABLE;
        $this->defineFillable();
        $this->dispatch(new EntityEvent(EntityEvent::DEFINE_FILLABLE));
        $this->state = self::GUARDED;
        $this->defineGuarded();
        $this->dispatch(new EntityEvent(EntityEvent::DEFINE_GUARDED));

        if (!empty($config)) 
        {
            $this->hydrate(
                isset($config['hydrate']) ? $config['hydrate'] : $config,
                [
                    'raw' => true, 
                    'sync' => false
                ]
            );
        }
    }
    
    /**
     * Declares columns
     * @return void
     */
    abstract protected function defineFillable();
    
    /**
     * Declares relations and others
     * @return void
     */
    protected function defineGuarded(){}
    
    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
    
    /**
     * @see EntityInterface::setIgnoreValue()
     */
    public function setIgnoreValue($value)
    {
        $this->ignoreValue = $value;
    }

    /**
     * @see EntityInterface::getIgnoreValue()
     */
    public function getIgnoreValue()
    {
        return $this->ignoreValue;
    }
    
    /**
     * @param string $value
     */
    public function setState($value) 
    {
        $this->state = $value;
    }

    /**
     * @return string
     */
    public function getState() 
    {
        return $this->state;
    }
    
    /**
     * @see EntityInterface::getFillableKeys()
     */
    public function getFillableKeys() 
    {
        return $this->fillable;
    }

    /**
     * @see EntityInterface::getGuardedKeys()
     */
    public function getGuardedKeys()
    {
        return $this->guarded;
    }
    
    /**
     * @return boolean
     */
    public function isReadOnly() 
    {
        return $this->state == self::READ_ONLY;
    }

    /**
     * @return boolean
     */
    public function isFillable() 
    {
        return $this->state == self::FILLABLE;
    }

    /**
     * @return boolean
     */
    public function isGuarded()
    {
        return $this->state == self::GUARDED;
    }
    
    /**
     * @see EntityInterface::isModified()
     */
    public function isModified($state = self::SYNC_ALL) 
    {
        $count = $this->getModified($state);
        return count($count) > 0;
    }

    /**
     * @see EntityInterface::getModified()
     */
    public function getModified($state = self::SYNC_ALL) 
    {
        $modified = [];
        $providers = [];
        
        switch($state)
        {
            case self::SYNC_FILLABLE:
                $providers = [$this->fillable];
                break;
            case self::SYNC_GUARDED:
                $providers = [$this->guarded];
                break;
            case self::SYNC_ALL:
                $providers = [$this->fillable, $this->guarded];
                break;
        }

        foreach ($providers as $provider)
        {
            foreach ($provider as $f) 
            {
                $value = $this->get($f);
                
                if (!array_key_exists($f, $this->original) || $this->original[$f] !== $value) 
                {
                    $modified[$f] = $value;
                }
            }
        }

        return $modified;
    }
    
    /**
     * @see EntityInterface::sync()
     */
    public function sync($state = self::SYNC_ALL) 
    {
        $providers = [];
        
        switch($state)
        {
            case self::SYNC_FILLABLE:
                $providers = [$this->fillable];
                break;
            case self::SYNC_GUARDED:
                $providers = [$this->guarded];
                break;
            case self::SYNC_ALL:
                $providers = [$this->fillable, $this->guarded];
                break;
        }
        
        foreach ($providers as $provider)
        {
            foreach ($provider as $f) 
            {
                $this->original[$f] = $this->get($f);
            }
        }
    }

    /**
     * @see EntityInterface::has()
     */
    public function has($key) 
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @see EntityInterface::set()
     * @throws \InvalidArgumentException
     */
    public function set($key, $value) 
    {
        if ($this->isReadOnly()) 
        {
            if (!$this->has($key))
            {
                throw new \InvalidArgumentException(sprintf('Key "%s" is a not declared property.', $key));
            }
        }
        
        if ($this->isFillable()) 
        {
            if (!in_array($key, $this->fillable))
            {
                $this->fillable[] = $key;
            }
        } 
        else if ($this->isGuarded()) 
        {
            if (!in_array($key, $this->guarded)) 
            {
                $this->guarded[] = $key;
            }
        }

        $this->data[$key] = $value;
    }

    /**
     * @see EntityInterface::get()
     */
    public function get($key, $default = null) 
    {
        if (!$this->has($key)) 
        {
            return is_callable($default) ? call_user_func($default) : $default;
        }

        return $this->data[$key];
    }
    
    /**
     * @see EntityInterface::hydrate()
     */
    public function hydrate(array $data, array $options = [])
    {
        $options += [
            'raw' => true,
            'sync' => true
        ];
        
        $references = [];
        $entities = [];

        foreach ($data as $key => $value) 
        {
            if (false !== strpos($key, self::ENTITY_SEPARATOR)) 
            {
                $reference = null;
                $segments = explode(self::ENTITY_SEPARATOR, $key);

                if (count($segments) == 3) 
                {
                    $reference = array_shift($segments);
                    $class = $segments[0];
                } 
                else 
                {
                    if ($segments[0] != $this->className) 
                    {
                        $class = $segments[0];

                        if (!isset($references[$class])) 
                        {
                            $reference = lcfirst(pathinfo($class, PATHINFO_BASENAME));
                            $references[$class] = $reference;
                        } 
                        else 
                        {
                            $reference = $references[$class];
                        }
                    }
                }

                if (null !== $reference) 
                {
                    if (!isset($entities[$reference])) 
                    {
                        $entities[$reference] = ['class' => $class, 'value' => []];
                    }

                    $entities[$reference]['value'][$segments[1]] = $value;
                    continue;
                }
            }

            if (is_array($value))
            {
                $value = $this->hydrateCollection($value, $options);
            }
            
            if ($options['raw']) 
            {
                $this->set($key, $value);
            } 
            else 
            {
                $this->$key = $value;
            }
        }

        if (count($entities) > 0) 
        {
            foreach ($entities as $key => $value) 
            {
                $class = $value['class'];
                $entity = $this->createInstance($class);
                
                if (null === $entities)
                {
                    continue;
                }
                
                $entity->hydrate($value['value'], $options);

                if ($options['raw']) 
                {
                    $this->set($key, $entity);
                } 
                else
                {
                    $this->$key = $entity;
                }
            }
        }

        if ($options['sync'])
        {
            $this->sync();
        }
    }
    
    /**
     * @param array $data
     * @param array $options
     * @return mixed
     */
    protected function hydrateCollection($data, array $options)
    {
        if (isset($data['_class']))
        {   
            $class = $data['_class'];
            $entity = $this->createInstance($class);
            
            if (null === $entity)
            {
                return null;
            }
            
            $entity->hydrate($data, $options);
            
            $data = $entity;
        } 
        else
        {
            foreach ($data as $key => &$value)
            {
                if (is_array($value))
                {
                    $value = $this->hydrateCollection($value, $options);
                }
            }
        }

        return $data;
    }

    /**
     * @see EntityInterface::export()
     */
    public function export(array $members = [], array $omitMembers = [], array $options = [])
    {
        $options += [
            'raw' => true,
            'format' => self::FORMAT_PHP
        ];
        
        $data = [];

        foreach (array_keys($this->data) as $key)
        {
            if (in_array($key, $omitMembers) || (count($members) > 0 && !in_array($key, $members)))
            {
                continue;
            } 
            else 
            {
                $value = $options['raw'] ? $this->get($key) : $this->$key;
                
                if ($value instanceof EntityInterface) 
                {
                    $value = $value->export([], [], $options);
                } 
                else if (is_array($value))
                {
                    $value = $this->exportCollection($value, $options);
                }
                
                $data[$key] = $value;
                $data['_class'] = $this->className;
            }
        }
        
        if (isset($options['format']))
        {
            $data = $this->{'to' . StringUtils::camelize($options['format'])}($data);
        }
        
        return $data;
    }
    
    /**
     * @param array $data
     * @return array
     */
    protected function formatPHP(array $data)
    {
        return $data;
    }
    
    /**
     * @param array $data
     * @return string
     */
    protected function formatJSON(array $data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * @param array $data
     * @param array $options
     * @return array
     */
    protected function exportCollection(array $data, $options) 
    {
        foreach ($data as $key => &$value) 
        {
            if (is_array($value)) 
            {
                $value = $this->exportCollection($value, $options);
            } 
            else if ($value instanceof EntityInterface)
            {
                $value = $value->export([], [], $options);
            }
        }

        return $data;
    }
    
    /**
     * @param string $class
     * @param array $config
     * @return EntityInterface
     */
    protected function createInstance($class, array $config = null)
    {
        return $class::factory($config);
    }

    /**
     * @ignore
     */
    public function __isset($key) 
    {
        return $this->has($key);
    }

    /**
     * @ignore
     */
    public function &__get($key) 
    {
        if (isset(static::$mutatorsGet[$this->className][$key]))
        {
            if (false !== static::$mutatorsGet[$this->className][$key]) 
            {
                $get = $this->{static::$mutatorsGet[$this->className][$key]}();
                return $get;
            }
        } 
        else 
        {
            $method = 'get' . StringUtils::camelize($key);

            if (method_exists($this, $method)) 
            {
                static::$mutatorsGet[$this->className][$key] = $method;
                
                $get = $this->{$method}();
                return $get;
            } 
            else
            {
                static::$mutatorsGet[$this->className][$key] = false;
            }
        }
        
        $get = $this->get($key);
        return $get;
    }

    /**
     * @ignore
     */
    public function __set($key, $value) 
    {
        if (isset(static::$mutatorsSet[$this->className][$key])) 
        {
            if (false !== static::$mutatorsSet[$this->className][$key]) 
            {
                $this->{static::$mutatorsSet[$this->className][$key]}($value);
                return;
            }
        } 
        else 
        {
            $method = 'set' . StringUtils::camelize($key);

            if (method_exists($this, $method)) 
            {
                static::$mutatorsSet[$this->className][$key] = $method;
                $this->{$method}($value);

                return;
            } 
            else
            {
                static::$mutatorsSet[$this->className][$key] = false;
            }
        }

        $this->set($key, $value);
    }

    /**
     * @ignore
     */
    public function __unset($key) 
    {
        $this->set($key, $this->ignoreValue);
    }
    
    /**
     * @ignore
     */
    public function jsonSerialize() 
    {
        return $this->__debugInfo();
    }
    
    /**
     * @ignore
     */
    public function __debugInfo()
    {
        return [
            'name' => $this->name,
            'data' => $this->export()
        ];
    }
}
