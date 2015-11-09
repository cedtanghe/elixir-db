<?php

namespace Elixir\DB\Query\SQL;

use Elixir\DB\Query\SQL\SQLAbstract;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class DropTable extends SQLAbstract
{
    /**
     * @see SQLInterface::render()
     */
    public function render() 
    {
        return 'DROP TABLE ' . $this->table;
    }
}
