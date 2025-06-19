<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    /** @use HasFactory<\Database\Factories\OperatorFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'chat_id',
        'is_supervisor'
    ];
    public function sort()
    {
        return $this->hasOne(OrderSort::class, 'operator_id');
    }

    public function lunchQueues()
    {
        return $this->hasMany(LunchQueue::class);
    }
}
