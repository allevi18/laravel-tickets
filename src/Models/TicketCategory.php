<?php


namespace RexlManu\LaravelTickets\Models;


use App\Models\TicketModel;
use Illuminate\Database\Eloquent\Model;
use RexlManu\LaravelTickets\Traits\HasConfigModel;

class TicketCategory extends Model
{

    use HasConfigModel;

    protected $fillable = [
        'translation, priority'
    ];

    public function getTable()
    {
        return config('laravel-tickets.database.ticket-categories-table');
    }

    public function models()
    {
        return $this->belongsToMany(
            TicketModel::class,
            'ticket_category_permissions',
            'category_id',
            'model_id'
        );
    }

}
