<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'active_theme'],
            [
                'value' => 'elomus',
                'type' => 'string',
                'label' => 'Giao diện đang kích hoạt',
                'description' => 'Mã của theme đang được sử dụng trên frontend.',
            ]
        );

        $defaultMenu = [
            [
                'label' => 'Trang chủ',
                'url' => '/',
                'is_mega' => false,
            ],
            [
                'label' => 'Sản phẩm',
                'url' => '/san-pham',
                'is_mega' => true,
                'columns' => [
                    [
                        'title' => 'Thương hiệu',
                        'type' => 'links',
                        'links' => [
                            ['label' => 'Panasonic', 'url' => '#'],
                            ['label' => 'Enagic Kangen', 'url' => '#'],
                            ['label' => 'Fuji Smart', 'url' => '#'],
                            ['label' => 'Trim Ion', 'url' => '#'],
                        ]
                    ],
                    [
                        'title' => 'Loại máy',
                        'type' => 'links',
                        'links' => [
                            ['label' => 'Máy gia đình', 'url' => '#'],
                            ['label' => 'Máy y tế', 'url' => '#'],
                            ['label' => 'Máy mini', 'url' => '#'],
                        ]
                    ],
                    [
                        'title' => 'Phụ kiện',
                        'type' => 'links',
                        'links' => [
                            ['label' => 'Lõi lọc tinh', 'url' => '#'],
                            ['label' => 'Bộ tiền lọc', 'url' => '#'],
                            ['label' => 'Bình chứa nước', 'url' => '#'],
                        ]
                    ],
                    [
                        'title' => 'Khuyến mãi Hè',
                        'type' => 'promo_banner',
                        'image_path' => 'menu-banners/promo-summer.jpg', // Will be missing physically, but works as data placeholder
                        'promo_url' => '/san-pham',
                        'promo_text' => 'Ưu đãi hè - Giảm 20%'
                    ]
                ]
            ],
            [
                'label' => 'Tin tức',
                'url' => '/tin-tuc',
                'is_mega' => false,
            ],
            [
                'label' => 'Giới thiệu',
                'url' => '/gioi-thieu',
                'is_mega' => false,
            ],
        ];

        Setting::updateOrCreate(
            ['key' => 'main_menu'],
            [
                'value' => json_encode($defaultMenu, JSON_UNESCAPED_UNICODE),
                'type' => 'json',
                'label' => 'MegaMenu Chính',
                'description' => 'Cấu hình menu thả xuống ở Header.',
            ]
        );
    }
}
