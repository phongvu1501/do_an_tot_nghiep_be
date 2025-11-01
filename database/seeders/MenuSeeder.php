<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\MenuCategory;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'name' => 'Gỏi cuốn tôm thịt',
                'category_id' => MenuCategory::where('name', 'Món khai vị')->first()->id,
                'description' => 'Gỏi cuốn tươi ngon với tôm, thịt heo và rau sống.',
                'price' => 45000,
                'image' => 'goi-cuon.jpg',
                'status' => 1,
            ],
            [
                'name' => 'Cơm gà Hải Nam',
                'category_id' => MenuCategory::where('name', 'Món chính')->first()->id,
                'description' => 'Cơm gà Hải Nam truyền thống với nước sốt đặc biệt.',
                'price' => 65000,
                'image' => 'com-ga.jpg',
                'status' => 1,
            ],
            [
                'name' => 'Trà chanh mật ong',
                'category_id' => MenuCategory::where('name', 'Đồ uống')->first()->id,
                'description' => 'Thức uống giải khát, thanh mát và tốt cho sức khỏe.',
                'price' => 30000,
                'image' => 'tra-chanh.jpg',
                'status' => 1,
            ],
            [
                'name' => 'Bánh flan caramel',
                'category_id' => MenuCategory::where('name', 'Tráng miệng')->first()->id,
                'description' => 'Bánh flan mềm mịn, vị ngọt dịu, phủ caramel thơm ngon.',
                'price' => 35000,
                'image' => 'banh-flan.jpg',
                'status' => 1,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
