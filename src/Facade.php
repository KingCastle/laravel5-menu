<?php namespace Kiwina\Menu;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'menu';
    }
}