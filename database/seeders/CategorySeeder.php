<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuCategory;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Món khai vị'],
            ['name' => 'Món chính'],
            ['name' => 'Đồ uống'],
            ['name' => 'Tráng miệng'],
        ];

        foreach ($categories as $category) {
            MenuCategory::create($category);
        }
    }
}
