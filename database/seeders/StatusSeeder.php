<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $statuses = [
            ['name' => 'open'],
            ['name' => 'pending'],
            ['name' => 'resolved'],
            ['name' => 'closed'],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate($status);
        }
    }
}
