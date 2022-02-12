<?php


namespace RexlManu\LaravelTickets\Models;


use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kyslik\ColumnSortable\Sortable;
use Nicolaslopezj\Searchable\SearchableTrait;
use RexlManu\LaravelTickets\Traits\HasConfigModel;
use RexlManu\LaravelTickets\Traits\TicketId;


class Ticket extends Model
{
    use HasConfigModel;
    use TicketId;
    use SearchableTrait;
    use Uuid;
    use Sortable;

    protected $fillable = [
        'subject',
        'priority',
        'state',
        'category_id',
        'user_id',
        'uuid'
    ];

    protected $searchable = [
        'columns' => [
            'tickets.id' => 8,
            'tickets.uuid' => 10,
            'users.email' => 10,
            'tickets.subject' => 5,
        ],
        'joins' => [
            'users' => ['tickets.user_id','users.id'],
            //'sellers' => ['listings.user_id','listings.id'],
        ],
    ];
    protected $appends = ['hash'];

    public $sortable = [
        'id',
        'user_id',
        'category_id',
        'priority',
        'state',
        'created_at',
        'updated_at'];



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
