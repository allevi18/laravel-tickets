<?php


namespace RexlManu\LaravelTickets\Traits;


trait HasTicketReference
{
    public function toReference(): string
    {
        $type = class_basename($this);
        if($type == 'Order')
            $type = 'Booking';
        $result = $this->hash ?? ($this->uuid ?? $this->id);
        return "$type #$result";
    }
}
