<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LunchQueue extends Model
{
    /** @use HasFactory<\Database\Factories\LunchQueueFactory> */
    use HasFactory;

    protected $fillable = ['operator_id', 'date', 'status', 'lunch_started_at'];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }
}
