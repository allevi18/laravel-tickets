<?php


namespace RexlManu\LaravelTickets\Traits;


trait HasTicketReference
{
    public function toReference(): string
    {
        $type = basename(get_class($this));
        $result = $this->hash ?? ($this->uuid ?? $this->id);
        return "$type #$result";
    }
}
