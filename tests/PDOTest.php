<?php

namespace Elixir\Test\DB;

use Elixir\DB\PDO;
use PHPUnit_Framework_TestCase;

/**
 * Description of PDOTest.
 *
 * @author Nicola Pertosa <nicola.pertosa@gmail.com>
 */
class PDOTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        $connection = new \PDO('mysql:dbname=elixir;host=localhost;port=3306', 'root', '');
        $pdo = new PDO($connection);

        $this->assertInstanceOf('Elixir\DB\PDO', $pdo);
        $this->assertEquals($connection->getAttribute(\PDO::ATTR_DRIVER_NAME), $pdo->getDriver());
        $this->assertEquals(1, $pdo->exec('INSERT INTO `user` (`id`, `firstname`, `name`) VALUES(NULL, "test firstname", "test name")'));
        $this->assertCount((int) $pdo->lastInsertId(), $pdo->query('SELECT * FROM `user`')->fetchAll());
    }
}
