<?php

namespace Lym125\Tim;

use Illuminate\Support\Facades\Facade as AbstractFacade;

class Facade extends AbstractFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lym125.tim';
    }
}
