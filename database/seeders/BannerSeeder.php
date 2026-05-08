<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::updateOrCreate(
            ['internal_name' => 'Hero Banner 1'],
            [
                'image_path' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&q=80&w=1920',
                'eyebrow' => 'Chính hãng Nhật Bản',
                'heading' => 'Nước Ion Kiềm',
                'sub_heading' => 'Sức Khỏe Vàng',
                'description' => 'Công nghệ điện phân đỉnh cao cho nguồn nước sạch tinh khiết.',
                'button_text' => 'SHOP NOW',
                'button_link' => '/products',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        Banner::updateOrCreate(
            ['internal_name' => 'Hero Banner 2'],
            [
                'image_path' => 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&q=80&w=1920',
                'eyebrow' => 'Bảo hành 8 năm',
                'heading' => 'Fuji Smart K8',
                'sub_heading' => 'Siêu Phẩm 2024',
                'description' => 'Điện cực Smart 4.0 hiệu suất cao, tạo nước Hydrogen vượt trội.',
                'button_text' => 'KHÁM PHÁ NGAY',
                'button_link' => '/products',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );
    }
}
