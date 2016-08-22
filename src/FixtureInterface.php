<?php

namespace Elixir\DB;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface FixtureInterface
{
    /**
     * @return int
     */
    public function getOrder();

    /**
     * @return bool
     */
    public function load();

    /**
     * @return bool
     */
    public function unload();
}
