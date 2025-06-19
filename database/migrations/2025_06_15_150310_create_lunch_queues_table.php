<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('lunch_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained();
            $table->integer('group_number');
            $table->time('lunch_time_start');
            $table->time('lunch_time_end');
            $table->boolean('notified')->default(false);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('lunch_queues');
    }
};
