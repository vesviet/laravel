<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user for Filament
        User::updateOrCreate(
            ['email' => 'admin@maydiengiaisaigon.vn'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Create Categories
        $productCat = \App\Models\Category::create(
            ['slug' => 'may-dien-giai-ion-kiem', 'name' => 'Máy Điện Giải Ion Kiềm', 'type' => 'product']
        );

        $articleCat = \App\Models\Category::create(
            ['slug' => 'kien-thuc-suc-khoe', 'name' => 'Kiến Thức Sức Khỏe', 'type' => 'article']
        );

        // Create Products (4 SKUs)
        $products = [
            [
                'name' => 'Máy lọc nước ion kiềm Panasonic TK-AS45',
                'slug' => 'panasonic-tk-as45',
                'sku' => 'PANA-AS45',
                'price' => 25500000,
                'original_price' => 28000000,
                'short_description' => 'Sản phẩm chính hãng Panasonic Nhật Bản, bảo hành 5 năm.',
                'features' => ['Tấm điện cực' => '3 tấm', 'Độ pH' => '5.5 - 9.5', 'Bảo hành' => '5 Năm'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Máy điện giải Kangen LeveLuk SD501',
                'slug' => 'kangen-leveluk-sd501',
                'sku' => 'ENAGIC-SD501',
                'price' => 106000000,
                'original_price' => 110000000,
                'short_description' => 'Dòng máy bán chạy nhất của Enagic, tạo 7 loại nước.',
                'features' => ['Tấm điện cực' => '7 tấm', 'Độ pH' => '2.5 - 11.5', 'Bảo hành' => '5 Năm'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm Fuji Smart K8',
                'slug' => 'fuji-smart-k8',
                'sku' => 'FUJI-K8',
                'price' => 59000000,
                'original_price' => 65000000,
                'short_description' => 'Công nghệ điện cực Smart 4.0 hiệu suất cao từ Fuji Nhật Bản.',
                'features' => ['Tấm điện cực' => '7 tấm', 'Độ pH' => '2.5 - 10.5', 'Bảo hành' => '8 Năm'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Máy điện giải ion kiềm Trim Ion Hyper',
                'slug' => 'trim-ion-hyper',
                'sku' => 'TRIM-HYPER',
                'price' => 50000000,
                'original_price' => null,
                'short_description' => 'Tạo nước ion kiềm giàu hydro siêu cấp, thiết kế nhỏ gọn.',
                'features' => ['Tấm điện cực' => '5 tấm', 'Độ pH' => '3.0 - 10.5', 'Bảo hành' => '5 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Máy điện giải Kangen LeveLuk K8',
                'slug' => 'kangen-leveluk-k8',
                'sku' => 'ENAGIC-K8',
                'price' => 139000000,
                'original_price' => 145000000,
                'short_description' => 'Dòng máy Flagship thế hệ mới nhất của Enagic, cảm ứng toàn phần.',
                'features' => ['Tấm điện cực' => '8 tấm', 'Độ pH' => '2.5 - 11.5', 'Bảo hành' => '5 Năm'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm Panasonic TK-AS66',
                'slug' => 'panasonic-tk-as66',
                'sku' => 'PANA-AS66',
                'price' => 51500000,
                'original_price' => 55000000,
                'short_description' => 'Tạo 7 loại nước, đáp ứng mọi nhu cầu sinh hoạt gia đình.',
                'features' => ['Tấm điện cực' => '5 tấm', 'Độ pH' => '3.0 - 10.0', 'Bảo hành' => '5 Năm'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Máy điện giải ion kiềm Fuji Smart P8',
                'slug' => 'fuji-smart-p8',
                'sku' => 'FUJI-P8',
                'price' => 89000000,
                'original_price' => 95000000,
                'short_description' => 'Nồng độ Hydrogen siêu cao, công suất mạnh mẽ.',
                'features' => ['Tấm điện cực' => '11 tấm', 'Độ pH' => '2.5 - 11.5', 'Bảo hành' => '8 Năm'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm Trim Ion Grace',
                'slug' => 'trim-ion-grace',
                'sku' => 'TRIM-GRACE',
                'price' => 65000000,
                'original_price' => 70000000,
                'short_description' => 'Dòng sản phẩm cao cấp, thiết kế nguyên khối sang trọng.',
                'features' => ['Tấm điện cực' => '5 tấm', 'Độ pH' => '3.0 - 10.5', 'Bảo hành' => '5 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm Impart Excel-FX (MX-99)',
                'slug' => 'impart-excel-fx',
                'sku' => 'IMPART-MX99',
                'price' => 79000000,
                'original_price' => 85000000,
                'short_description' => 'Máy lọc nước có kích thước tấm điện cực lớn nhất, siêu bền bỉ.',
                'features' => ['Tấm điện cực' => '5 tấm lớn', 'Độ pH' => '2.4 - 11.6', 'Bảo hành' => '5 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm Atica MHI-3200',
                'slug' => 'atica-mhi-3200',
                'sku' => 'ATICA-MHI3200',
                'price' => 45000000,
                'original_price' => 49000000,
                'short_description' => 'Giải pháp nước ion kiềm Atica Nhật Bản tối ưu chi phí.',
                'features' => ['Tấm điện cực' => '5 tấm', 'Độ pH' => '5.5 - 9.5', 'Bảo hành' => '5 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm Mitsubishi Cleansui AL700E',
                'slug' => 'mitsubishi-cleansui-al700e',
                'sku' => 'MITSU-AL700E',
                'price' => 38000000,
                'original_price' => 42000000,
                'short_description' => 'Máy thiết kế undersink tiện lợi, tinh tế cho căn bếp hiện đại.',
                'features' => ['Tấm điện cực' => '5 tấm', 'Độ pH' => '5.0 - 10.5', 'Bảo hành' => '3 Năm'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Máy điện giải Kangen Super 501',
                'slug' => 'kangen-super-501',
                'sku' => 'ENAGIC-S501',
                'price' => 155000000,
                'original_price' => 160000000,
                'short_description' => 'Máy lọc nước Enagic chuyên dụng cho gia đình đông người hoặc y tế.',
                'features' => ['Tấm điện cực' => '12 tấm', 'Độ pH' => '2.5 - 11.5', 'Bảo hành' => '5 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm Fuji Smart i9',
                'slug' => 'fuji-smart-i9',
                'sku' => 'FUJI-I9',
                'price' => 109000000,
                'original_price' => 119000000,
                'short_description' => 'Đỉnh cao nước ion kiềm siêu Hydro với công nghệ Super Hydrogen.',
                'features' => ['Tấm điện cực' => '10 tấm', 'Độ pH' => '2.5 - 10.5', 'Bảo hành' => '8 Năm'],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm Panasonic TK-AB50',
                'slug' => 'panasonic-tk-ab50',
                'sku' => 'PANA-AB50',
                'price' => 43500000,
                'original_price' => 48000000,
                'short_description' => 'Thiết kế âm tủ nhỏ gọn sang trọng đến từ Panasonic.',
                'features' => ['Tấm điện cực' => '5 tấm', 'Độ pH' => '5.0 - 9.5', 'Bảo hành' => '5 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm Trim Ion Ti-9000',
                'slug' => 'trim-ion-ti-9000',
                'sku' => 'TRIM-TI9000',
                'price' => 42000000,
                'original_price' => 45000000,
                'short_description' => 'Mẫu máy quen thuộc, bền bỉ của hãng Trim (Nihon Trim).',
                'features' => ['Tấm điện cực' => '5 tấm', 'Độ pH' => '3.5 - 10.5', 'Bảo hành' => '5 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Máy điện giải Kangen LeveLuk JRIV',
                'slug' => 'kangen-leveluk-jriv',
                'sku' => 'ENAGIC-JR4',
                'price' => 84000000,
                'original_price' => 90000000,
                'short_description' => 'Mẫu máy nhỏ gọn cho gia đình ít người của Enagic.',
                'features' => ['Tấm điện cực' => '4 tấm', 'Độ pH' => '2.5 - 11.5', 'Bảo hành' => '5 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm IONIA U-Blue',
                'slug' => 'ionia-u-blue',
                'sku' => 'IONIA-UBLUE',
                'price' => 35000000,
                'original_price' => 39000000,
                'short_description' => 'Thương hiệu hàng đầu Hàn Quốc, thiết kế hiện đại.',
                'features' => ['Tấm điện cực' => '7 tấm', 'Độ pH' => '4.0 - 10.0', 'Bảo hành' => '5 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm OSG NDX-303LMW',
                'slug' => 'osg-ndx-303lmw',
                'sku' => 'OSG-303',
                'price' => 52000000,
                'original_price' => 55000000,
                'short_description' => 'Sản phẩm từ tập đoàn OSG Nhật Bản, uy tín lâu năm.',
                'features' => ['Tấm điện cực' => '5 tấm', 'Độ pH' => '3.5 - 10.0', 'Bảo hành' => '3 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Máy điện giải ion kiềm Tyent UCE-9000T',
                'slug' => 'tyent-uce-9000t',
                'sku' => 'TYENT-UCE9000T',
                'price' => 69000000,
                'original_price' => 75000000,
                'short_description' => 'Màn hình cảm ứng siêu mượt, công nghệ điện cực Hàn Quốc.',
                'features' => ['Tấm điện cực' => '9 tấm', 'Độ pH' => '2.0 - 12.0', 'Bảo hành' => '5 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Máy lọc nước ion kiềm Biontech BTM-207D',
                'slug' => 'biontech-btm-207d',
                'sku' => 'BIONTECH-207D',
                'price' => 45000000,
                'original_price' => 48000000,
                'short_description' => 'Công nghệ tiên tiến từ Hàn Quốc, bộ lọc đôi hiệu quả cao.',
                'features' => ['Tấm điện cực' => '7 tấm', 'Độ pH' => '3.5 - 10.0', 'Bảo hành' => '5 Năm'],
                'is_featured' => false,
                'is_active' => true,
            ]
        ];

        foreach ($products as $p) {
            \App\Models\Product::create(
                array_merge($p, ['category_id' => $productCat->id])
            );
        }

        $this->call([
            SettingSeeder::class,
            PageSeeder::class,
            BannerSeeder::class,
        ]);
    }
}
