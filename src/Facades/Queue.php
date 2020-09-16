<?php
namespace Jiajiale\LaravelQueue\Facades;

use Illuminate\Support\Facades\Facade;

class Queue extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-queue';
    }
}