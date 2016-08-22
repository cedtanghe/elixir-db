<?php

namespace Elixir\DB\ObjectMapper;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface FindableExtensionInterface
{
    /**
     * @param FindableInterface $value
     */
    public function setFindable(FindableInterface $value);

    /**
     * @return array
     */
    public function getRegisteredMethods();
}
