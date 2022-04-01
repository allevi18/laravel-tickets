<?php


namespace RexlManu\LaravelTickets\Traits;


trait HasTicketReference
{
    public function toReference(): string
    {
        $type = class_basename($this);
        if($type == 'Order')
            $type = 'Booking';
        $type = _l($type);
        $result = $this->hash ?? ($this->uuid ?? $this->id);
        return "$type #$result";
    }
}
