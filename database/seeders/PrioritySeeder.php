<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Priority;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
          $priorities = [
            ['name' => 'low'],
            ['name' => 'medium'],
            ['name' => 'high'],
        ];
        

        foreach ($priorities as $priority) {
            Priority::firstOrCreate($priority);
        }
    }
}
