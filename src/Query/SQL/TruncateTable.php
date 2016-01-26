<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\SQLAbstract;

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
        return 'TRUNCATE TABLE ' . $this->table;
    }
}
