<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorSetting extends Model
{
    /** @use HasFactory<\Database\Factories\SupervisorSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'group_size',     // har guruhdagi odamlar soni (masalan: 5)
        'lunch_start',    // ovqatlanish boshlanishi (masalan: '12:00')
        'lunch_end',      // ovqatlanish tugashi (masalan: '13:00')
    ];

}
