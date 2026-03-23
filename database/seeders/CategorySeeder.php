<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $categories = [
            [
                'name' => 'Technical Issue',
                'description' => 'Problems related to bugs, system errors, or technical failures.',
            ],
            [
                'name' => 'Billing',
                'description' => 'Questions or issues related to payments and invoices.',
            ],
            [
                'name' => 'Account',
                'description' => 'Account access, profile, and authentication issues.',
            ],
            [
                'name' => 'General Inquiry',
                'description' => 'General questions and non-technical support requests.',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }

    }
}
