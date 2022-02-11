<?php


namespace RexlManu\LaravelTickets\Traits;


trait HasConfigModel
{

    public function getKeyType()
    {
        return config('laravel-tickets.models.key-type');
    }

    public function isIncrementing()
    {
        return config('laravel-tickets.models.incrementing');
    }
    
}
