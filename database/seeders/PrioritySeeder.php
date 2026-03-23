<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Seeder;

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
