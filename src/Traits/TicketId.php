<?php


namespace RexlManu\LaravelTickets\Traits;
use Hashids;

trait TicketId
{

    protected $connection_name = 'ticket';
    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return Hashids::connection($this->connection_name)->encode($this->getKey());
    }
}
