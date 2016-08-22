<?php

namespace Elixir\DB\Query\SQL\MySQL;

use Elixir\DB\Query\SQL\DropTable as BaseDropTable;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class DropTable extends BaseDropTable
{
    /**
     * @var bool
     */
    protected $temporary = false;

    /**
     * @var bool
     */
    protected $ifExists = false;

    /**
     * @param bool $value
     *
     * @return DropTable
     */
    public function temporary($value)
    {
        $this->temporary = $value;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return DropTable
     */
    public function ifExists($value)
    {
        $this->ifExists = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reset($part)
    {
        parent::reset($part);

        switch ($part) {
            case 'temporary':
                $this->temporary(false);
                break;
            case 'if_exists':
                $this->ifExists(false);
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
            case 'temporary':
                return $this->temporary;
            case 'if_exists':
                return $this->ifExists;
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
            case 'temporary':
                $this->temporary($data);
                break;
            case 'if_exists':
                $this->ifExists($data);
                break;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $SQL = 'DROP '."\n";
        $SQL .= $this->renderTemporary();
        $SQL .= 'TABLE '."\n";
        $SQL .= $this->renderIfExists();
        $SQL .= $this->table;

        return trim($SQL);
    }

    /**
     * @return string
     */
    protected function renderTemporary()
    {
        $SQL = '';

        if ($this->temporary) {
            $SQL .= 'TEMPORARY '."\n";
        }

        return $SQL;
    }

    /**
     * @return string
     */
    protected function renderIfExists()
    {
        $SQL = '';

        if ($this->ifExists) {
            $SQL .= 'IF EXISTS '."\n";
        }

        return $SQL;
    }
}
