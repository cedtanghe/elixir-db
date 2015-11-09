<?php

namespace Elixir\DB\ObjectMapper;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface EntityInterface
{
    /**
     * @var string
     */
    const READ_ONLY = 'read_only';

    /**
     * @var string
     */
    const FILLABLE = 'fillable';

    /**
     * @var string
     */
    const GUARDED = 'guarded';
    
    /**
     * @var string
     */
    const SYNC_FILLABLE = 'sync_fillable';

    /**
     * @var string
     */
    const SYNC_GUARDED = 'sync_guarded';
    
    /**
     * @var string
     */
    const SYNC_ALL = 'sync_all';
    
    /**
     * @var null
     */
    const IGNORE_VALUE = null;
    
    /**
     * @var string
     */
    const FORMAT_PHP = 'PHP';
    
    /**
     * @var string
     */
    const FORMAT_JSON = 'JSON';
    
    /**
     * @var string
     */
    const ENTITY_SEPARATOR = '::';
    
    /**
     * @return EntityInterface
     */
    public static function factory(array $config = null);

    /**
     * @return mixed
     */
    public function getIgnoreValue();
    
    /**
     * @param string $value
     */
    public function setState($value);

    /**
     * @return string
     */
    public function getState();
    
    /**
     * @return array
     */
    public function getFillableKeys();
    
    /**
     * @return array
     */
    public function getGuardedKeys();
    
    /**
     * @param string $state
     * @return boolean
     */
    public function isModified($state = self::SYNC_ALL);
    
    /**
     * @param string $state
     * @return array
     */
    public function getModified($state = self::SYNC_ALL);
    
    /**
     * @param string $state
     */
    public function sync($state = self::SYNC_ALL);
    
    /**
     * @param array $data
     * @param array $options
     */
    public function hydrate(array $data, array $options = []);

    /**
     * @param array $members
     * @param array $omitMembers
     * @param array $options
     * @return array|string
     */
    public function export(array $members = [], array $omitMembers = [], array $options = []);

    /**
     * @param string $key
     * @return boolean
     */
    public function has($key);

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);
}
