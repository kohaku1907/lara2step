<?php

namespace Kohaku1907\Laravel2step\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kohaku1907\Laravel2step\Laravel2step
 */
class Laravel2step extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Kohaku1907\Laravel2step\Laravel2step::class;
    }
}
