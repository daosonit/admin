<?php 
namespace App\Mytour\Facades;

use Illuminate\Support\Facades\Facade;

class MytourStatic extends Facade {

    protected static function getFacadeAccessor() { return 'MytourStatic'; }

}