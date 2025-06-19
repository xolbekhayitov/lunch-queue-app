<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LunchQueue extends Model
{
    /** @use HasFactory<\Database\Factories\LunchQueueFactory> */
    use HasFactory;

     protected $fillable = [
        'operator_id',
        'group_number',
        'lunch_time_start',
        'lunch_time_end',
        'notified',
    ];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

}
