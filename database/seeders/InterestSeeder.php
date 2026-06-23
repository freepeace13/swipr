<?php

namespace Database\Seeders;

use App\Models\Interest;
use App\Models\InterestCategory;
use Illuminate\Database\Seeder;

class InterestSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(
            file_get_contents(database_path('interests.json')),
            true
        );

        foreach ($data['categories'] as $sort => $category) {
            InterestCategory::updateOrCreate(
                ['id' => $category['id']],
                [
                    'label' => $category['label'],
                    'icon' => $category['icon'] ?? null,
                    'sort_order' => $sort,
                    'is_active' => true,
                ]
            );

            foreach ($category['interests'] as $i => $interest) {
                Interest::updateOrCreate(
                    ['id' => $interest['id']],
                    [
                        'category_id' => $category['id'],
                        'label' => $interest['label'],
                        'icon' => $interest['icon'] ?? null,
                        'sort_order' => $i,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
