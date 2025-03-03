<?php

namespace Devonab\FilamentEasyFooter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Devonab\FilamentEasyFooter\EasyFooter
 */
class EasyFooter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Devonab\FilamentEasyFooter\EasyFooter::class;
    }
}
