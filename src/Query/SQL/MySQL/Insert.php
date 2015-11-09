<?php

namespace Elixir\DB\Query\SQL\MySQL;

use Elixir\DB\Query\SQL\Insert as BaseInsert;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Insert extends BaseInsert 
{
    /**
     * @var boolean
     */
    protected $ignore = false;

    /**
     * @var array
     */
    protected $duplicateKeyUpdate = [];

    /**
     * @param boolean $value
     * @return Insert
     */
    public function ignore($value = true) 
    {
        $this->ignore = $value;
        return $this;
    }

    /**
     * @param array $values
     * @param string $type
     * @return Insert
     */
    public function duplicateKeyUpdate(array $values, $type = self::VALUES_SET)
    {
        if($type == self::VALUES_SET)
        {
            $this->duplicateKeyUpdate = [];
        }
        
        $this->duplicateKeyUpdate = array_merge($this->duplicateKeyUpdate, $values);
        return $this;
    }
    
    /**
     * @see BaseInsert::reset()
     */
    public function reset($part) 
    {
        parent::reset($part);
        
        switch ($part) 
        {
            case 'ignore':
                $this->ignore(false);
                break;
            case 'duplicate-key-update':
                $this->duplicateKeyUpdate = [];
                break;
        }

        return $this;
    }
    
    /**
     * @see BaseInsert::get()
     */
    public function get($part) 
    {
        switch ($part) 
        {
            case 'ignore':
                return $this->ignore;
            case 'duplicate-key-update':
                return $this->duplicateKeyUpdate;
        }
        
        return parent::get($part);
    }
    
    /**
     * @see BaseInsert::merge()
     */
    public function merge($data, $part) 
    {
        parent::merge($data, $part);
        
        switch ($part) 
        {
            case 'ignore':
                $this->ignore($data);
                break;
            case 'duplicate-key-update':
                $this->duplicateKeyUpdate($data, self::VALUES_MERGE);
                break;
        }
        
        return $this;
    }

    /**
     * @see BaseInsert::render()
     */
    public function render() 
    {
        $SQL = 'INSERT ' . "\n";
        $SQL .= $this->renderIgnore();
        $SQL .= 'INTO ' . $this->table . ' ' . "\n";
        $SQL .= $this->renderColumns();
        $SQL .= $this->renderValues();
        $SQL .= $this->renderDuplicateKeyUpdate();

        return trim($SQL);
    }

    /**
     * @return string
     */
    protected function renderDuplicateKeyUpdate() 
    {
        if (count($this->duplicateKeyUpdate) > 0)
        {
            $SQL = 'ON DUPLICATE KEY UPDATE ';
            $first = true;

            foreach ($this->duplicateKeyUpdate as $key => $value) 
            {
                if (!$this->raw) 
                {
                    $value = $this->quote($value);
                }

                $SQL .= ($first ? '' : ', ') . $key . ' = ' . $value . "\n";
                $first = false;
            }


            return $SQL . "\n";
        }

        return '';
    }

    /**
     * @return string
     */
    protected function renderIgnore() 
    {
        if ($this->ignore) 
        {
            return 'IGNORE ' . "\n";
        }

        return '';
    }

    /**
     * @see BaseInsert::renderColumns()
     */
    protected function renderColumns()
    {
        if (empty($this->values)) 
        {
            $SQL = '() ' . "\n";
        }
        else
        {
            $SQL = parent::renderColumns();
        }

        return $SQL;
    }

    /**
     * @see BaseInsert::renderValues()
     */
    protected function renderValues()
    {
        if (empty($this->values)) 
        {
            $SQL = 'VALUES () ' . "\n";
        }
        else
        {
            $SQL = parent::renderValues();
        }

        return $SQL;
    }
}
