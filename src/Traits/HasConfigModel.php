<?php


namespace RexlManu\LaravelTickets\Traits;


trait HasConfigModel
{

    public function getKeyType()
    {
        return 'string';
    }

    public function isIncrementing()
    {
        return !$this instanceof Ticket;
    }
    
}
