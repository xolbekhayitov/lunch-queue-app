<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSort extends Model
{
    /** @use HasFactory<\Database\Factories\OrderSortFactory> */
    use HasFactory;

    protected $table = 'sort_order';

    protected $fillable = ['operator_id', 'position'];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}
