<?php


namespace RexlManu\LaravelTickets\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use RexlManu\LaravelTickets\Traits\HasConfigModel;

class Ticket extends Model
{
    use HasConfigModel;
    use TicketId;

    protected $fillable = [
        'subject',
        'priority',
        'state',
        'category_id',
        'user_id'
    ];

    protected $appends = ['hash'];


    public function getHashAttribute($value) {
        return $this->getRouteKey();
    }


    public function scopeHashid($query, $hashid)
    {
        return $query->where('id', Hashids::decode($hashid)[0]);
    }

    public function getTable()
    {
        return config('laravel-tickets.database.tickets-table');
    }

    /**
     * returns every user that had sent a message in the ticket
     *
     * @param false $ticketCreatorIncluded if the ticket user should be included
     *
     * @return Collection
     */
    public function getRelatedUsers($ticketCreatorIncluded = false)
    {
        return $this
            ->messages()
            ->whereNotIn('user_id', $ticketCreatorIncluded ? [] : [ $this->user_id ])
            ->pluck('user_id')
            ->unique()
            ->values();
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function opener()
    {
        return $this->belongsTo(config('laravel-tickets.user'));
    }

    public function user()
    {
        return $this->belongsTo(config('laravel-tickets.user'));
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function reference()
    {
        return $this->hasOne(TicketReference::class);
    }

    public function activities()
    {
        return $this->hasMany(TicketActivity::class);
    }

    public function scopeState($query, $state)
    {
        return $query->whereIn('state', is_string($state) ? [ $state ] : $state);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
