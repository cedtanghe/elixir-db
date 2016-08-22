<?php

namespace Elixir\DB;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface MigrationInterface
{
    /**
     * @return int
     */
    public function getOrder();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return bool
     */
    public function up();

    /**
     * @return bool
     */
    public function down();
}
