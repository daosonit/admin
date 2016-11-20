<?php namespace App\Mytour\Facades;

use Illuminate\Support\Facades\Facade;

class Template extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'template';
    }

}