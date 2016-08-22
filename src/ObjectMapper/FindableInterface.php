<?php

namespace Elixir\DB\ObjectMapper;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
interface FindableInterface
{
    /**
     * @param FindableExtensionInterface $extension
     *
     * @return FindableInterface
     */
    public function extend(FindableExtensionInterface $extension);

    /**
     * @return bool
     */
    public function has();

    /**
     * @return int
     */
    public function count();

    /**
     * @return array
     */
    public function raw();

    /**
     * @return EntityInterface|null
     */
    public function first();

    /**
     * @return array
     */
    public function all();
}
