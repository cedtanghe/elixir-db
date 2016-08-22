<?php

namespace Elixir\DB\Query\SQL;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class TruncateTable extends SQLAbstract
{
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return 'TRUNCATE TABLE '.$this->table;
    }
}
