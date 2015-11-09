<?php

namespace Elixir\DB;

use Elixir\DB\DBInterface;
use Elixir\DB\PDO;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class DBFactory 
{
    /**
     * @var string
     */
    const PDO_MYSQL = 'pdo_mysql';

    /**
     * @var string
     */
    const PDO_SQLITE = 'pdo_sqlite';
    
    /**
     * @var array 
     */
    public static $factories = [];

    /**
     * @param array $config
     * @return DBInterface
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public static function create(array $config)
    {
        if(!isset(static::$factories['PDO']))
        {
            static::$factories['PDO'] = function($config)
            {
                if (substr($config['type'], 0, 3) == 'pdo') 
                {
                    $username = isset($config['username']) ? $config['username'] : null;
                    $password = isset($config['password']) ? $config['password'] : null;
                    $options = isset($config['options']) ? $config['options'] : [];
                    
                    switch ($config['type']) 
                    {
                        case self::PDO_MYSQL:
                            $DNS = 'mysql:dbname=' . $config['dbname'];

                            if (isset($config['host'])) 
                            {
                                $DNS .= ';host=' . $config['host'];
                            }

                            if (isset($config['port'])) 
                            {
                                $DNS .= ';port=' . $config['port'];
                            }
                            
                            if (!isset($options[\PDO::MYSQL_ATTR_INIT_COMMAND])) 
                            {
                                $options[\PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES \'UTF8\'';
                            }
                            break;
                        case self::PDO_SQLITE:
                            $DNS = 'sqlite:' . (isset($config['dbname']) ? $config['dbname'] : ':memory:');
                            break;
                        default:
                            throw new \RuntimeException(sprintf('Driver %s is not implemented.', $config['type']));
                    }
                    
                    if (!isset($options[\PDO::ATTR_PERSISTENT])) 
                    {
                        $options[\PDO::ATTR_PERSISTENT] = false;
                    }

                    if (!isset($options[\PDO::ATTR_ERRMODE])) 
                    {
                        $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
                    }

                    return new PDO(new \PDO($DNS, $username, $password, $options), true);
                }
            };
        }
        
        foreach(static::$factories as $factory)
        {
            $result = $factory($config);
            
            if(null !== $result)
            {
                return $result;
            }
        }

        throw new \InvalidArgumentException('No adapter has been implemented.');
    }
}
