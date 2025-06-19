<?php

namespace Database\Seeders;

use App\Models\Operator;
use App\Models\OrderSort;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operators = Operator::factory()->count(10)->create();

        foreach ($operators as $index => $operator) {
            OrderSort::create([
                'operator_id' => $operator->id,
                'position' => $index,
            ]);
        }
    }
}




