<?php

namespace Elixir\DB\Query\SQL\SQLite;

use Elixir\DB\Query\SQL\Insert as BaseInsert;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class Insert extends BaseInsert
{
    /**
     * @var bool
     */
    protected $ignore = false;

    /**
     * @param bool $value
     *
     * @return Insert
     */
    public function ignore($value = true)
    {
        $this->ignore = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reset($part)
    {
        parent::reset($part);

        switch ($part) {
            case 'ignore':
                $this->ignore(false);
                break;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($part)
    {
        switch ($part) {
            case 'ignore':
                return $this->ignore;
        }

        return parent::get($part);
    }

    /**
     * {@inheritdoc}
     */
    public function merge($data, $part)
    {
        parent::merge($data, $part);

        switch ($part) {
            case 'ignore':
                $this->ignore($data);
                break;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $SQL = 'INSERT '."\n";
        $SQL .= $this->renderIgnore();
        $SQL .= 'INTO '.$this->table.' '."\n";
        $SQL .= $this->renderColumns();
        $SQL .= $this->renderValues();

        return trim($SQL);
    }

    /**
     * @return string
     */
    protected function renderIgnore()
    {
        $SQL = '';

        if ($this->ignore) {
            $SQL = 'OR IGNORE '."\n";
        }

        return $SQL;
    }
}
