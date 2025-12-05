<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuCategory;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'COMBO',
                'description' => 'Các combo tiết kiệm với nhiều món ăn hấp dẫn'
            ],
            [
                'name' => 'MÓN NHẬU',
                'description' => 'Các món nhậu đặc sắc, phù hợp để nhâm nhi cùng bạn bè'
            ],
            [
                'name' => 'DÊ TƯƠI',
                'description' => 'Các món từ dê tươi được chế biến đặc biệt'
            ],
            [
                'name' => 'THIẾT BẢN',
                'description' => 'Các món cháy tỏi, nướng trên thiết bản nóng hổi'
            ],
            [
                'name' => 'RAU XANH',
                'description' => 'Các món rau xanh tươi ngon, tốt cho sức khỏe'
            ],
            [
                'name' => 'HẢI SẢN',
                'description' => 'Hải sản tươi sống, được chế biến đặc biệt'
            ],
            [
                'name' => 'CÁ CÁC MÓN',
                'description' => 'Các món cá đa dạng, từ nướng đến hấp, chiên'
            ],
            [
                'name' => 'ĐỒ UỐNG',
                'description' => 'Các loại đồ uống giải khát, bia, rượu và nước ngọt'
            ],
        ];

        foreach ($categories as $category) {
            MenuCategory::create($category);
        }
    }
}
