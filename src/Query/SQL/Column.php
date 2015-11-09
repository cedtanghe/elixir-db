<?php

namespace Elixir\DB\Query\SQL;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Column 
{
    /**
     * @var string
     */
    const TINYINT = 'TINYINT';

    /**
     * @var string
     */
    const SMALLINT = 'SMALLINT';

    /**
     * @var string
     */
    const MEDIUMINT = 'MEDIUMINT';

    /**
     * @var string
     */
    const INT = 'INT';

    /**
     * @var string
     */
    const INTEGER = 'INTEGER';

    /**
     * @var string
     */
    const BIGINT = 'BIGINT';

    /**
     * @var string
     */
    const REAL = 'REAL';

    /**
     * @var string
     */
    const DOUBLE = 'DOUBLE';

    /**
     * @var string
     */
    const FLOAT = 'FLOAT';

    /**
     * @var string
     */
    const DECIMAL = 'DECIMAL';

    /**
     * @var string
     */
    const NUMERIC = 'NUMERIC';

    /**
     * @var string
     */
    const DATE = 'DATE';

    /**
     * @var string
     */
    const TIME = 'TIME';

    /**
     * @var string
     */
    const TIMESTAMP = 'TIMESTAMP';

    /**
     * @var string
     */
    const DATETIME = 'DATETIME';

    /**
     * @var string
     */
    const CHAR = 'CHAR';

    /**
     * @var string
     */
    const VARCHAR = 'VARCHAR';

    /**
     * @var string
     */
    const TINYBLOB = 'TINYBLOB';

    /**
     * @var string
     */
    const BLOB = 'BLOB';

    /**
     * @var string
     */
    const MEDIUMBLOB = 'MEDIUMBLOB';

    /**
     * @var string
     */
    const LONGBLOB = 'LONGBLOB';

    /**
     * @var string
     */
    const TINYTEXT = 'TINYTEXT';

    /**
     * @var string
     */
    const TEXT = 'TEXT';

    /**
     * @var string
     */
    const MEDIUMTEXT = 'MEDIUMTEXT';

    /**
     * @var string
     */
    const LONGTEXT = 'LONGTEXT';

    /**
     * @var string
     */
    const ENUM = 'ENUM';

    /**
     * @var string
     */
    const SET = 'SET';

    /**
     * @var string
     */
    const GEOMETRY = 'GEOMETRY';

    /**
     * @var string
     */
    const POINT = 'POINT';

    /**
     * @var string
     */
    const LINESTRING = 'LINESTRING';

    /**
     * @var string
     */
    const POLYGON = 'POLYGON';

    /**
     * @var string
     */
    const MULTIPOINT = 'MULTIPOINT';

    /**
     * @var string
     */
    const MULTILINESTRING = 'MULTILINESTRING';

    /**
     * @var string
     */
    const MULTIPOLYGON = 'MULTIPOLYGON';

    /**
     * @var string
     */
    const GEOMETRYCOLLECTION = 'GEOMETRYCOLLECTION';

    /**
     * @var string
     */
    const CURRENT_TIMESTAMP = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     */
    const BINARY = 'BINARY';

    /**
     * @var string
     */
    const UNSIGNED = 'UNSIGNED';

    /**
     * @var string
     */
    const UNSIGNED_ZEROFILL = 'UNSIGNED_ZEROFILL';

    /**
     * @var string
     */
    const UPDATE_CURRENT_TIMESTAMP = 'ON UPDATE CURRENT_TIMESTAMP';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var mixed
     */
    protected $default;

    /**
     * @var string
     */
    protected $collation = null;

    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var boolean
     */
    protected $nullable = false;

    /**
     * @var boolean 
     */
    protected $autoIncrement = false;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @param string $name
     * @param string $type
     */
    public function __construct($name = null, $type = null)
    {
        if (null !== $name) 
        {
            $this->setName($name);
        }

        if (null !== $type) 
        {
            $this->setType($type);
        }
    }

    /**
     * @param string $value
     * @return Column
     */
    public function setName($value)
    {
        $this->name = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
     * @return Column
     */
    public function setType($value) 
    {
        $this->type = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() 
    {
        return $this->type;
    }

    /**
     * @param mixed $value
     * @return Column
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue() 
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Column
     */
    public function setDefault($value) 
    {
        $this->default = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefault() 
    {
        return $this->default;
    }

    /**
     * @param string $value
     * @return Column
     */
    public function setCollating($value) 
    {
        $this->collation = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getCollating() 
    {
        return $this->collation;
    }

    /**
     * @param string $value
     * @return Column
     */
    public function setAttribute($value) 
    {
        $this->attribute = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getAttribute() 
    {
        return $this->attribute;
    }

    /**
     * @param boolean $value
     * @return Column
     */
    public function setNullable($value) 
    {
        $this->nullable = $value;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @param boolean $value
     * @return Column
     */
    public function setAutoIncrement($value)
    {
        $this->autoIncrement = $value;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * @param string $value
     * @return Column
     */
    public function setComment($value) 
    {
        $this->comment = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment() 
    {
        return $this->comment;
    }
}
