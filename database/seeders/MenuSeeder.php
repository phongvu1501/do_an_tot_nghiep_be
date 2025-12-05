<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\MenuCategory;

class MenuSeeder extends Seeder
{
    private $monanImages = [
        '202306061148549949.webp',
        '202309061341366132.webp',
        '202404231646599464.webp',
        '202404231700157219.webp',
        '202405031050025243.webp',
        '202405290916414523.webp',
        '202506271703552855.webp',
        '202506271706594276.webp',
        '202506271712248578.webp',
    ];

    private $rauImages = [
        '202404241056124413.webp',
        '202404241059241917.webp',
        '202405301012158646.webp',
    ];

    private function getRandomMonanImage(): string
    {
        return 'uploads/menus/monan/' . $this->monanImages[array_rand($this->monanImages)];
    }

    private function getRandomRauImage(): string
    {
        return 'uploads/menus/rau/' . $this->rauImages[array_rand($this->rauImages)];
    }

    public function run(): void
    {
        $combo = MenuCategory::where('name', 'COMBO')->first();
        if ($combo) {
            $menus = [
                [
                    'name' => 'Combo 2 người',
                    'category_id' => $combo->id,
                    'description' => 'Combo đặc biệt dành cho 2 người với đầy đủ món ăn: 1 món nướng, 1 món xào, 1 món canh, cơm và đồ uống. Phù hợp cho bữa tối lãng mạn hoặc hẹn hò cùng bạn bè.',
                    'price' => 450000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Combo 4 người',
                    'category_id' => $combo->id,
                    'description' => 'Combo tiết kiệm cho nhóm 4 người với menu phong phú: 2 món nướng, 2 món xào, 1 món canh, 1 món rau, cơm và đồ uống. Lý tưởng cho gia đình hoặc nhóm bạn.',
                    'price' => 850000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Combo 8 người',
                    'category_id' => $combo->id,
                    'description' => 'Combo đại tiệc cho nhóm 8 người với thực đơn đa dạng: 3 món nướng, 3 món xào, 2 món canh, 2 món rau, cơm và đồ uống. Hoàn hảo cho các buổi tụ tập, liên hoan lớn.',
                    'price' => 1600000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
            ];
            foreach ($menus as $menu) {
                Menu::create($menu);
            }
        }

        $monNhau = MenuCategory::where('name', 'MÓN NHẬU')->first();
        if ($monNhau) {
            $menus = [
                [
                    'name' => 'Ếch sốt tiêu gừng chua cay',
                    'category_id' => $monNhau->id,
                    'description' => 'Ếch tươi sốt tiêu gừng, chua cay hấp dẫn',
                    'price' => 159000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Trâu xào rau muống',
                    'category_id' => $monNhau->id,
                    'description' => 'Thịt trâu tươi xào rau muống giòn ngon',
                    'price' => 159000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Lợn mán nướng giềng mẻ',
                    'category_id' => $monNhau->id,
                    'description' => 'Lợn mán nướng với giềng mẻ thơm lừng',
                    'price' => 185000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Nấm sữa nướng giềng mẻ',
                    'category_id' => $monNhau->id,
                    'description' => 'Nấm sữa tươi nướng giềng mẻ đậm đà',
                    'price' => 189000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Gà H\'Mong rang muối',
                    'category_id' => $monNhau->id,
                    'description' => 'Gà H\'Mong thơm ngon rang muối đặc biệt',
                    'price' => 385000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Gà H\'Mong chiên mắm',
                    'category_id' => $monNhau->id,
                    'description' => 'Gà H\'Mong chiên giòn với mắm đậm đà',
                    'price' => 385000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Chân gà sốt thái chua cay',
                    'category_id' => $monNhau->id,
                    'description' => 'Chân gà giòn sần sật sốt thái chua cay',
                    'price' => 109000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Lợn mán xào lăn',
                    'category_id' => $monNhau->id,
                    'description' => 'Lợn mán xào lăn thơm ngon đậm đà',
                    'price' => 189000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Lợn mán hấp lá thơm',
                    'category_id' => $monNhau->id,
                    'description' => 'Lợn mán hấp với lá thơm thơm ngon',
                    'price' => 189000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Lạp xường Tây Bắc',
                    'category_id' => $monNhau->id,
                    'description' => 'Lạp xường Tây Bắc đặc sản thơm ngon',
                    'price' => 149000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Khay đồ nguội Tây Bắc',
                    'category_id' => $monNhau->id,
                    'description' => 'Khay đồ nguội Tây Bắc đa dạng, hấp dẫn',
                    'price' => 215000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Bò một nắng chấm muối kiến vàng',
                    'category_id' => $monNhau->id,
                    'description' => 'Bò một nắng đặc sản chấm muối kiến vàng độc đáo',
                    'price' => 165000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
            ];
            foreach ($menus as $menu) {
                Menu::create($menu);
            }
        }

        $deTuoi = MenuCategory::where('name', 'DÊ TƯƠI')->first();
        if ($deTuoi) {
            $menus = [
                [
                    'name' => 'Dồi dê',
                    'category_id' => $deTuoi->id,
                    'description' => 'Dồi dê nướng thơm lừng, đậm đà hương vị',
                    'price' => 149000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Ba chỉ dê hấp lá tía tô',
                    'category_id' => $deTuoi->id,
                    'description' => 'Ba chỉ dê tươi hấp với lá tía tô thơm ngon',
                    'price' => 245000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Dê trộn dừa non kèm bánh đa',
                    'category_id' => $deTuoi->id,
                    'description' => 'Dê tươi trộn dừa non, kèm bánh đa giòn',
                    'price' => 189000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Nộm dê bóp thấu',
                    'category_id' => $deTuoi->id,
                    'description' => 'Nộm dê bóp thấu chua cay đậm đà',
                    'price' => 185000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Nộm dê rau má',
                    'category_id' => $deTuoi->id,
                    'description' => 'Nộm dê với rau má tươi mát, thanh đạm',
                    'price' => 185000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
            ];
            foreach ($menus as $menu) {
                Menu::create($menu);
            }
        }

        // THIẾT BẢN
        $thietBan = MenuCategory::where('name', 'THIẾT BẢN')->first();
        if ($thietBan) {
            $menus = [
                [
                    'name' => 'Trâu tươi cháy tỏi',
                    'category_id' => $thietBan->id,
                    'description' => 'Trâu tươi cháy tỏi trên thiết bản nóng hổi',
                    'price' => 189000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Bê cháy tỏi',
                    'category_id' => $thietBan->id,
                    'description' => 'Bê tươi cháy tỏi thơm lừng trên thiết bản',
                    'price' => 185000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Nọng heo cháy tỏi',
                    'category_id' => $thietBan->id,
                    'description' => 'Nọng heo cháy tỏi giòn thơm trên thiết bản',
                    'price' => 189000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Nấm sữa cháy tỏi',
                    'category_id' => $thietBan->id,
                    'description' => 'Nấm sữa cháy tỏi thơm ngon trên thiết bản',
                    'price' => 199000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Bê sữa sốt tiêu xanh',
                    'category_id' => $thietBan->id,
                    'description' => 'Bê sữa sốt tiêu xanh đậm đà trên thiết bản',
                    'price' => 189000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Thịt dải heo cháy tỏi',
                    'category_id' => $thietBan->id,
                    'description' => 'Thịt dải heo cháy tỏi giòn thơm trên thiết bản',
                    'price' => 189000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Tràng trứng chim',
                    'category_id' => $thietBan->id,
                    'description' => 'Tràng trứng chim cháy tỏi đặc biệt trên thiết bản',
                    'price' => 189000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
            ];
            foreach ($menus as $menu) {
                Menu::create($menu);
            }
        }

        // RAU XANH
        $rauXanh = MenuCategory::where('name', 'RAU XANH')->first();
        if ($rauXanh) {
        $menus = [
            [
                    'name' => 'Rau ngót lào xào tỏi',
                    'category_id' => $rauXanh->id,
                    'description' => 'Rau ngót lào xào tỏi thơm ngon, thanh đạm',
                    'price' => 75000,
                    'image' => $this->getRandomRauImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Rau bò khai xào tỏi',
                    'category_id' => $rauXanh->id,
                    'description' => 'Rau bò khai xào tỏi đậm đà hương vị',
                    'price' => 79000,
                    'image' => $this->getRandomRauImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Ngồng cải luộc chấm trứng',
                    'category_id' => $rauXanh->id,
                    'description' => 'Ngồng cải luộc tươi ngon chấm trứng',
                    'price' => 79000,
                    'image' => $this->getRandomRauImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Măng trúc xào ba chỉ gác bếp',
                    'category_id' => $rauXanh->id,
                    'description' => 'Măng trúc xào ba chỉ gác bếp đậm đà',
                    'price' => 159000,
                    'image' => $this->getRandomRauImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cải mèo xào tỏi',
                    'category_id' => $rauXanh->id,
                    'description' => 'Cải mèo xào tỏi giòn ngon, thanh đạm',
                    'price' => 59000,
                    'image' => $this->getRandomRauImage(),
                'status' => 1,
            ],
            [
                    'name' => 'Cải mèo luộc chấm trứng',
                    'category_id' => $rauXanh->id,
                    'description' => 'Cải mèo luộc tươi ngon chấm trứng',
                'price' => 65000,
                    'image' => $this->getRandomRauImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Rau ngót lào xào ba chỉ gác bếp',
                    'category_id' => $rauXanh->id,
                    'description' => 'Rau ngót lào xào ba chỉ gác bếp thơm ngon',
                    'price' => 99000,
                    'image' => $this->getRandomRauImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Rau bò khai xào ba chỉ gác bếp',
                    'category_id' => $rauXanh->id,
                    'description' => 'Rau bò khai xào ba chỉ gác bếp đậm đà',
                    'price' => 99000,
                    'image' => $this->getRandomRauImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Đậu pháp xào trứng non sốt XO',
                    'category_id' => $rauXanh->id,
                    'description' => 'Đậu pháp xào trứng non sốt XO đặc biệt',
                    'price' => 165000,
                    'image' => $this->getRandomRauImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cải mèo xào ba chỉ gác bếp',
                    'category_id' => $rauXanh->id,
                    'description' => 'Cải mèo xào ba chỉ gác bếp thơm ngon',
                    'price' => 99000,
                    'image' => $this->getRandomRauImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Măng trúc xào bò',
                    'category_id' => $rauXanh->id,
                    'description' => 'Măng trúc xào bò đậm đà hương vị',
                    'price' => 159000,
                    'image' => $this->getRandomRauImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Ốc móng tay xào rau muống',
                    'category_id' => $rauXanh->id,
                    'description' => 'Ốc móng tay xào rau muống giòn ngon',
                    'price' => 89000,
                    'image' => $this->getRandomRauImage(),
                    'status' => 1,
                ],
            ];
            foreach ($menus as $menu) {
                Menu::create($menu);
            }
        }

        // HẢI SẢN
        $haiSan = MenuCategory::where('name', 'HẢI SẢN')->first();
        if ($haiSan) {
            $menus = [
                [
                    'name' => 'Ốc hương ủ muối thảo mộc',
                    'category_id' => $haiSan->id,
                    'description' => 'Ốc hương tươi ngon ủ với muối thảo mộc đặc biệt',
                    'price' => 269000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Tôm sú ủ muối thảo mộc',
                    'category_id' => $haiSan->id,
                    'description' => 'Tôm sú tươi ủ muối thảo mộc, ngọt thịt đậm đà',
                    'price' => 249000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Tôm chiên hoàng kim',
                    'category_id' => $haiSan->id,
                    'description' => 'Tôm chiên hoàng kim giòn thơm, vàng ruộm',
                    'price' => 249000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Tôm sú sốt ớt pattaya',
                    'category_id' => $haiSan->id,
                    'description' => 'Tôm sú sốt ớt pattaya cay đậm đà',
                    'price' => 249000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Mực hấp sốt Thái',
                    'category_id' => $haiSan->id,
                    'description' => 'Mực tươi hấp sốt Thái chua cay đặc trưng',
                    'price' => 285000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Mực hấp chanh',
                    'category_id' => $haiSan->id,
                    'description' => 'Mực tươi hấp chanh thanh mát, tươi ngon',
                    'price' => 285000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Mực chiên hoàng kim',
                    'category_id' => $haiSan->id,
                    'description' => 'Mực chiên hoàng kim giòn thơm, vàng ruộm',
                    'price' => 285000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Râu mực xào su hào sốt XO',
                    'category_id' => $haiSan->id,
                    'description' => 'Râu mực xào su hào sốt XO đậm đà',
                    'price' => 179000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Tôm sú sốt me đặc biệt',
                    'category_id' => $haiSan->id,
                    'description' => 'Tôm sú sốt me đặc biệt chua ngọt đậm đà',
                    'price' => 249000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Râu mực xào tứ xuyên',
                    'category_id' => $haiSan->id,
                    'description' => 'Râu mực xào tứ xuyên cay đậm đà',
                    'price' => 185000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Mực cháy chà bông',
                    'category_id' => $haiSan->id,
                    'description' => 'Mực cháy chà bông thơm ngon đặc biệt',
                    'price' => 289000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Mực chiên bơ tỏi',
                    'category_id' => $haiSan->id,
                    'description' => 'Mực chiên bơ tỏi thơm lừng, béo ngậy',
                    'price' => 285000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Tôm tắm sốt thái',
                    'category_id' => $haiSan->id,
                    'description' => 'Tôm tắm sốt thái chua cay đậm đà',
                    'price' => 249000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
            ];
            foreach ($menus as $menu) {
                Menu::create($menu);
            }
        }

        // CÁ CÁC MÓN
        $caCacMon = MenuCategory::where('name', 'CÁ CÁC MÓN')->first();
        if ($caCacMon) {
            $menus = [
                [
                    'name' => 'Cá dưa chua tứ xuyên',
                    'category_id' => $caCacMon->id,
                    'description' => 'Cá tươi nấu dưa chua tứ xuyên cay đậm đà',
                    'price' => 469000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cá chẽm hấp chanh',
                    'category_id' => $caCacMon->id,
                    'description' => 'Cá chẽm tươi hấp chanh thanh mát',
                    'price' => 259000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cá chẽm sốt Pattaya',
                    'category_id' => $caCacMon->id,
                    'description' => 'Cá chẽm sốt Pattaya đậm đà hương vị',
                    'price' => 259000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cá chẽm chiên sốt me đặc biệt',
                    'category_id' => $caCacMon->id,
                    'description' => 'Cá chẽm chiên sốt me đặc biệt chua ngọt',
                    'price' => 259000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cá chẽm chiên giòn kèm xoài xanh',
                    'category_id' => $caCacMon->id,
                    'description' => 'Cá chẽm chiên giòn kèm xoài xanh thanh mát',
                    'price' => 259000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cá diêu hồng chiên sốt chili thái',
                    'category_id' => $caCacMon->id,
                    'description' => 'Cá diêu hồng chiên sốt chili thái cay đậm đà',
                    'price' => 425000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cá quả nướng muối ớt',
                    'category_id' => $caCacMon->id,
                    'description' => 'Cá quả nướng muối ớt thơm lừng',
                    'price' => 425000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cá quả nướng riềng mẻ',
                    'category_id' => $caCacMon->id,
                    'description' => 'Cá quả nướng riềng mẻ đậm đà',
                    'price' => 425000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cá chép om dưa',
                    'category_id' => $caCacMon->id,
                    'description' => 'Cá chép om dưa chua đậm đà hương vị',
                    'price' => 425000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cá chép hấp xì dầu hongkong',
                    'category_id' => $caCacMon->id,
                    'description' => 'Cá chép hấp xì dầu hongkong đậm đà',
                    'price' => 425000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Cá chép om cay tứ xuyên',
                    'category_id' => $caCacMon->id,
                    'description' => 'Cá chép om cay tứ xuyên đậm đà',
                    'price' => 425000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
            ];
            foreach ($menus as $menu) {
                Menu::create($menu);
            }
        }

        // ĐỒ UỐNG
        $doUong = MenuCategory::where('name', 'ĐỒ UỐNG')->first();
        if ($doUong) {
            $menus = [
                [
                    'name' => 'Rượu mơ 9 chum (500ml)',
                    'category_id' => $doUong->id,
                    'description' => 'Rượu mơ 9 chum thơm ngon, đậm đà',
                    'price' => 195000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Nước suối',
                    'category_id' => $doUong->id,
                    'description' => 'Nước suối tinh khiết',
                    'price' => 20000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Nước cam ép 320ml',
                    'category_id' => $doUong->id,
                    'description' => 'Nước cam ép tươi ngon',
                    'price' => 20000,
                    'image' => $this->getRandomMonanImage(),
                    'status' => 1,
                ],
                [
                    'name' => 'Tháp Hoegaarden 3 lít',
                    'category_id' => $doUong->id,
                    'description' => 'Tháp bia Hoegaarden 3 lít tươi mát',
                    'price' => 449000,
                    'image' => 'uploads/menus/bia/bia.webp',
                    'status' => 1,
                ],
                [
                    'name' => 'Tháp Carlsberg 3 lít',
                    'category_id' => $doUong->id,
                    'description' => 'Tháp bia Carlsberg 3 lít tươi mát',
                    'price' => 319000,
                    'image' => 'uploads/menus/bia/bia2.webp',
                    'status' => 1,
                ],
                [
                    'name' => 'Tháp Budweiser 3 lít',
                    'category_id' => $doUong->id,
                    'description' => 'Tháp bia Budweiser 3 lít tươi mát',
                    'price' => 315000,
                    'image' => 'uploads/menus/bia/bia3.webp',
                    'status' => 1,
                ],
                [
                    'name' => 'Tháp Blanc 1664 3 lít',
                    'category_id' => $doUong->id,
                    'description' => 'Tháp bia Blanc 1664 3 lít tươi mát',
                    'price' => 365000,
                    'image' => 'uploads/menus/bia/bia4.webp',
                    'status' => 1,
                ],
                [
                    'name' => 'Ly Hoegaarden 250ml',
                    'category_id' => $doUong->id,
                    'description' => 'Ly bia Hoegaarden 250ml tươi mát',
                    'price' => 39000,
                    'image' => 'uploads/menus/bia/bia.webp',
                    'status' => 1,
                ],
                [
                    'name' => 'Ly Carlsberg 330ml',
                    'category_id' => $doUong->id,
                    'description' => 'Ly bia Carlsberg 330ml tươi mát',
                    'price' => 39000,
                    'image' => 'uploads/menus/bia/bia2.webp',
                    'status' => 1,
                ],
                [
                    'name' => 'Ly Budweiser 330ml',
                    'category_id' => $doUong->id,
                    'description' => 'Ly bia Budweiser 330ml tươi mát',
                    'price' => 35000,
                    'image' => 'uploads/menus/bia/bia3.webp',
                    'status' => 1,
                ],
                [
                    'name' => 'Ly Blanc 1664 330ml',
                    'category_id' => $doUong->id,
                    'description' => 'Ly bia Blanc 1664 330ml tươi mát',
                    'price' => 41000,
                    'image' => 'uploads/menus/bia/bia4.webp',
                'status' => 1,
            ],
            [
                    'name' => 'Bia Tiger Crystal',
                    'category_id' => $doUong->id,
                    'description' => 'Bia Tiger Crystal tươi mát',
                    'price' => 29000,
                    'image' => 'uploads/menus/bia/bia.webp',
                'status' => 1,
            ],
            [
                    'name' => 'Bia Heineken',
                    'category_id' => $doUong->id,
                    'description' => 'Bia Heineken tươi mát',
                'price' => 35000,
                    'image' => 'uploads/menus/bia/bia2.webp',
                    'status' => 1,
                ],
                [
                    'name' => 'Bia Corona Extra',
                    'category_id' => $doUong->id,
                    'description' => 'Bia Corona Extra tươi mát',
                    'price' => 45000,
                    'image' => 'uploads/menus/bia/bia3.webp',
                'status' => 1,
            ],
        ];
        foreach ($menus as $menu) {
            Menu::create($menu);
            }
        }
    }
}
