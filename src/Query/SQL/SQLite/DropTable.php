<?php

namespace Elixir\DB\Query\SQL\SQLite;

use Elixir\DB\Query\SQL\DropTable as BaseDropTable;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class DropTable extends BaseDropTable
{
    /**
     * @var bool
     */
    protected $ifExists = false;

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
        $SQL = 'DROP TABLE '."\n";
        $SQL .= $this->renderIfExists();
        $SQL .= $this->table;

        return trim($SQL);
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
