<?php
/**
 * Created by PhpStorm.
 * User: white
 * Date: 7/13/18
 * Time: 10:11 AM
 */

namespace OlderW\RestfulDoc\Facades;

use Illuminate\Support\Facades\Facade;


class RestfulDoc extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \OlderW\RestfulDoc\RestfulDoc::class;
    }
}