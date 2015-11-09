<?php

namespace Elixir\DB\ObjectMapper;

use Elixir\Dispatcher\Event;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class EntityEvent extends Event
{
    /**
     * @var string
     */
    const DEFINE_FILLABLE = 'define_fillable';
    
    /**
     * @var string
     */
    const DEFINE_GUARDED = 'define_guarded';
}
