<?php

namespace Elixir\Test\DB;

use Elixir\DB\DBFactory;
use PHPUnit_Framework_TestCase;

/**
 * Description of DBFactoryTest.
 *
 * @author Nicola Pertosa <nicola.pertosa@gmail.com>
 */
class DBFactoryTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $this->assertInstanceOf(
            'Elixir\DB\PDO',
            DBFactory::create([
                'type' => DBFactory::PDO_MYSQL,
                'dbname' => 'elixir',
                'host' => 'localhost',
                'port' => '3306',
                'username' => 'root',
                'password' => '',
                'options' => [],
            ])
        );
    }
}
