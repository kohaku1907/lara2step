<?php

namespace Kohaku1907\Lara2step\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kohaku1907\Lara2step\Lara2step
 */
class Lara2step extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kohaku1907\Lara2step\Lara2step::class;
    }
}
