<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\NewsletterSubscriber;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with complete Sober Furniture sample data with full product galleries.
     */
    public function run(): void
    {
        // ── 1. ADMIN USER ───────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin Sober',
                'password' => Hash::make('password'),
            ]
        );

        // ── 2. SAMPLE CUSTOMERS ─────────────────────────────────────────────
        $customer1 = Customer::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name'     => 'Nguyễn Văn An',
                'phone'    => '0901234567',
                'password' => Hash::make('password'),
                'status'   => 'active',
            ]
        );

        $customer2 = Customer::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name'     => 'Trần Thị Mai',
                'phone'    => '0912345678',
                'password' => Hash::make('password'),
                'status'   => 'active',
            ]
        );

        // ── 3. NEWSLETTER SUBSCRIBERS ───────────────────────────────────────
        NewsletterSubscriber::firstOrCreate(
            ['email' => 'subscriber@example.com'],
            ['subscribed_at' => Carbon::now()]
        );

        // ── 4. CATEGORIES (Sober Furniture) ─────────────────────────────────
        $catChairs = Category::firstOrCreate(
            ['slug' => 'ghe-armchair'],
            [
                'name'        => 'Ghế & Armchair',
                'description' => 'Các mẫu ghế ăn gỗ sồi, ghế thư giãn bọc da cao cấp phong cách Bắc Âu.',
            ]
        );

        $catTables = Category::firstOrCreate(
            ['slug' => 'ban-tra-ban-an'],
            [
                'name'        => 'Bàn Trà & Bàn Ăn',
                'description' => 'Bàn làm việc, bàn trà tròn, bàn ăn gỗ tự nhiên thiết kế tối giản.',
            ]
        );

        $catLamps = Category::firstOrCreate(
            ['slug' => 'den-chieu-sang'],
            [
                'name'        => 'Đèn Chiếu Sáng',
                'description' => 'Đèn thả trần nghệ thuật, đèn bàn xi măng và đèn sàn trang trí tinh tế.',
            ]
        );

        $catSofas = Category::firstOrCreate(
            ['slug' => 'sofa-phong-khach'],
            [
                'name'        => 'Sofa & Phòng Khách',
                'description' => 'Sofa đệm nỉ êm ái, sofa băng vải bố cao cấp chuẩn phong cách Scandinavian.',
            ]
        );

        $catAccessories = Category::firstOrCreate(
            ['slug' => 'phu-kien-trang-tri'],
            [
                'name'        => 'Phụ Kiện Trang Trí',
                'description' => 'Đồng hồ treo tường, lọ gốm, khay gỗ và các vật phẩm decor không gian sống.',
            ]
        );

        $catShelves = Category::firstOrCreate(
            ['slug' => 'tu-ke-go'],
            [
                'name'        => 'Tủ & Kệ Gỗ',
                'description' => 'Hệ thống kệ sách mở, tủ ngăn kéo gỗ tự nhiên tối ưu diện tích.',
            ]
        );

        // ── 5. PRODUCTS (Sober Furniture Catalog with Full Multi-Image Galleries) ──
        $productsData = [
            // ── Featured Products (Home v12 Featured Grid) ──
            [
                'category_id'      => $catLamps->id,
                'name'             => 'Đèn Thả Trần Ambit Pendant Lamp',
                'slug'             => 'ambit-pendant-lamp',
                'sku'              => 'LMP-001',
                'description'      => 'Đèn thả trần Ambit Pendant Lamp mang thiết kế tối giản kinh điển của vùng Bắc Âu. Chao đèn được dập bằng nhôm nguyên khối, phủ sơn tĩnh điện mờ chống bám vân tay, cho ánh sáng ấm cúng lan tỏa đều khắp không gian bàn ăn hoặc đảo bếp.',
                'image_path'       => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=700&auto=format&fit=crop&q=80',
                'price'            => 4500000,
                'stock'            => 25,
                'weight'           => 1800,
                'status'           => 'active',
                'is_featured'      => true,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1540932239986-30128078f3c5?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Trắng Mờ (Matte White)', 'Xám Tro (Ash Gray)', 'Đen Tuyển (Matte Black)'],
                    'material'        => 'Nhôm nguyên khối sơn tĩnh điện',
                    'dimensions'      => 'Đường kính 40cm, Dây treo 300cm',
                ],
                'seo_title'        => 'Đèn Thả Trần Ambit Pendant Lamp — MYSHOP',
                'seo_description'  => 'Đèn thả trần Ambit Pendant Lamp phong cách Bắc Âu tối giản chất liệu nhôm nguyên khối.',
            ],
            [
                'category_id'      => $catAccessories->id,
                'name'             => 'Bộ Cối Xay Tiêu Bottle Grinders Set',
                'slug'             => 'bottle-grinders-set',
                'sku'              => 'ACC-002',
                'description'      => 'Bộ đôi cối xay muối tiêu hình dáng bình nước độc đáo. Lõi xay gốm siêu bền, nắp xoay điều chỉnh độ mịn linh hoạt, vỏ silicon mịn chống trượt giúp căn bếp luôn ngăn nắp và hiện đại.',
                'image_path'       => 'https://images.unsplash.com/photo-1584990347449-399a9a3b6f12?w=700&auto=format&fit=crop&q=80',
                'price'            => 1250000,
                'stock'            => 50,
                'weight'           => 600,
                'status'           => 'active',
                'is_featured'      => true,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1590736969955-71cc94801759?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1584990347449-399a9a3b6f12?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1590736969955-71cc94801759?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Set Hồng Đất & Nâu', 'Set Xám & Trắng'],
                    'material'        => 'Lõi gốm sứ Ceramic, nắp gỗ sồi, vỏ silicon cao cấp',
                ],
                'seo_title'        => 'Bộ Cối Xay Tiêu Bottle Grinders Set — MYSHOP',
                'seo_description'  => 'Bộ cối xay muối tiêu cao cấp thiết kế Bắc Âu tối giản cho căn bếp sang trọng.',
            ],
            [
                'category_id'      => $catAccessories->id,
                'name'             => 'Đồng Hồ Tối Giản Freakish Clock',
                'slug'             => 'freakish-clock',
                'sku'              => 'ACC-003',
                'description'      => 'Đồng hồ treo tường Freakish Clock với thiết kế đĩa xoay hiển thị giờ độc nhất vô nhị. Thiết kế không kim truyền thống giúp tạo điểm nhấn nghệ thuật trên các mảng tường phòng khách hay phòng làm việc.',
                'image_path'       => 'https://images.unsplash.com/photo-1563861826100-9cb868fdbe1c?w=700&auto=format&fit=crop&q=80',
                'price'            => 2650000,
                'stock'            => 30,
                'weight'           => 800,
                'status'           => 'active',
                'is_featured'      => true,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1563861826100-9cb868fdbe1c?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Vàng Mù Tạt', 'Đen Tuyển', 'Trắng Băng'],
                    'material'        => 'Thép không gỉ sơn tĩnh điện',
                    'dimensions'      => 'Đường kính 30cm, Độ dày 4cm',
                ],
                'seo_title'        => 'Đồng Hồ Treo Tường Freakish Clock — MYSHOP',
                'seo_description'  => 'Đồng hồ treo tường nghệ thuật phong cách tối giản Bắc Âu.',
            ],
            [
                'category_id'      => $catChairs->id,
                'name'             => 'Ghế Ăn Gỗ Sồi Synnes Dining Chair',
                'slug'             => 'synnes-dining-chair',
                'sku'              => 'CHR-004',
                'description'      => 'Ghế ăn Synnes Dining Chair kết hợp giữa kỹ thuật uốn cong gỗ thủ công và kết cấu khung chịu lực vững chãi. Tựa lưng ôm sát cơ thể mang đến cảm giác ngồi thoải mái suốt bữa ăn gia đình.',
                'image_path'       => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=700&auto=format&fit=crop&q=80',
                'price'            => 5800000,
                'stock'            => 20,
                'weight'           => 4500,
                'status'           => 'active',
                'is_featured'      => true,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1503602642458-232111445657?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Gỗ Sồi Tự Nhiên (Natural Oak)', 'Gỗ Óc Chó (Walnut Dark)', 'Đen Sơn Mờ (Stained Black)'],
                    'material'        => 'Gỗ sồi nhập khẩu Bắc Mỹ nguyên khối',
                    'dimensions'      => 'Dài 47.5cm x Rộng 48.5cm x Cao 80cm (Chiều cao ngồi 45cm)',
                ],
                'seo_title'        => 'Ghế Ăn Gỗ Sồi Synnes Dining Chair — MYSHOP',
                'seo_description'  => 'Ghế ăn gỗ sồi tự nhiên Synnes Dining Chair phong cách Bắc Âu tinh tế.',
            ],
            [
                'category_id'      => $catTables->id,
                'name'             => 'Bàn Làm Việc Copenhague Desk',
                'slug'             => 'copenhague-desk',
                'sku'              => 'DSK-005',
                'description'      => 'Bàn làm việc Copenhague Desk lấy cảm hứng từ nội thất trường đại học Đan Mạch. Mặt bàn gỗ sồi vát cạnh thanh thoát, chân chữ A chắc chắn, tích hợp khe luồn dây cáp ẩn tinh tế.',
                'image_path'       => 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=700&auto=format&fit=crop&q=80',
                'price'            => 14200000,
                'stock'            => 12,
                'weight'           => 18000,
                'status'           => 'active',
                'is_featured'      => true,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Mặt Linoleum Xám Khói - Chân Sồi', 'Mặt Gỗ Sồi Tự Nhiên'],
                    'material'        => 'Khung gỗ sồi khối, mặt phủ linoleum chống xước',
                    'dimensions'      => 'Dài 130cm x Rộng 65cm x Cao 74cm',
                ],
                'seo_title'        => 'Bàn Làm Việc Copenhague Desk — MYSHOP',
                'seo_description'  => 'Bàn làm việc gỗ sồi Bắc Âu tối giản Copenhague Desk.',
            ],
            [
                'category_id'      => $catChairs->id,
                'name'             => 'Ghế Đẩu Nghệ Thuật Arte 60 Stool',
                'slug'             => 'arte-60-stool',
                'sku'              => 'CHR-006',
                'description'      => 'Mẫu ghế đẩu tròn 3 chân kinh điển của kiến trúc hiện đại Phần Lan do Alvar Aalto thiết kế. Cấu trúc chân chữ L uốn nhiệt dẻo kết hợp mặt đẩu tròn thanh thoát, dễ dàng xếp chồng nhiều chiếc tạo thành một tác phẩm điêu khắc xoắn ốc tuyệt đẹp. Hoàn hảo làm ghế ngồi tiếp khách, bàn phụ đầu giường hoặc đôn trưng bày cây xanh.',
                'image_path'       => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?w=900&auto=format&fit=crop&q=80',
                'price'            => 3200000,
                'stock'            => 40,
                'weight'           => 3200,
                'status'           => 'active',
                'is_featured'      => true,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1503602642458-232111445657?w=900&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1503602642458-232111445657?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=900&auto=format&fit=crop&q=80',
                    ],
                    'album'           => [
                        [
                            'url'     => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?w=1200&auto=format&fit=crop&q=80',
                            'tag'     => 'Phòng Khách Tối Giản',
                            'title'   => 'Ghế Đẩu Arte 60 Trong Không Gian Mở',
                            'caption' => 'Đường cong chân chữ L kinh điển tôn vinh vẻ đẹp thuần khiết của vân gỗ bạch dương tự nhiên.',
                        ],
                        [
                            'url'     => 'https://images.unsplash.com/photo-1503602642458-232111445657?w=1200&auto=format&fit=crop&q=80',
                            'tag'     => 'Gia Công Thủ Công',
                            'title'   => 'Khớp Nối Uốn Nhiệt Dẻo Độc Bản',
                            'caption' => 'Kỹ thuật uốn ép gỗ bằng hơi nước độc quyền giữ kết cấu bền bỉ qua hàng thập kỷ sử dụng.',
                        ],
                        [
                            'url'     => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=1200&auto=format&fit=crop&q=80',
                            'tag'     => 'Studio & Góc Đọc Sách',
                            'title'   => 'Đôn Đặt Đèn Bàn & Sách Cạnh Giường',
                            'caption' => 'Ứng dụng đa năng làm bàn phụ hoặc đôn trang trí gốm và hoa mang lại sự ấm áp cho căn phòng.',
                        ],
                        [
                            'url'     => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=1200&auto=format&fit=crop&q=80',
                            'tag'     => 'Nghệ Thuật Xếp Chồng',
                            'title'   => 'Cấu Trúc Xoắn Ốc Khi Xếp Gọn',
                            'caption' => 'Khả năng xếp chồng vô tận giúp tối ưu hóa diện tích sinh hoạt mà vẫn tạo nên điểm nhấn kiến trúc.',
                        ],
                    ],
                    'color'           => ['Gỗ Bạch Dương Tự Nhiên', 'Mặt Cam San Hô', 'Mặt Đen Tuyển'],
                    'material'        => 'Gỗ dán bạch dương Phần Lan uốn nhiệt dẻo cao cấp',
                    'dimensions'      => 'Đường kính mặt 38cm, Chiều cao 44cm, Trọng lượng 3.2kg',
                    'origin'          => 'Thiết kế Scandinavian Nordic, Gia công tiêu chuẩn xuất khẩu EU',
                ],
                'seo_title'        => 'Ghế Đẩu Tròn Arte 60 Stool — MYSHOP',
                'seo_description'  => 'Ghế đẩu gỗ 3 chân Arte 60 Stool xếp chồng tiện dụng chuẩn phong cách Bắc Âu.',
            ],
            [
                'category_id'      => $catLamps->id,
                'name'             => 'Đèn Bàn Xi Măng Gỗ Cement Wood Lamp',
                'slug'             => 'cement-wood-lamp',
                'sku'              => 'LMP-007',
                'description'      => 'Sự kết hợp thô mộc giữa chân đế bê tông đúc khuôn và thân gỗ sồi ấm áp. Chao đèn vải lanh thô lọc ánh sáng dịu mắt, hoàn hảo làm đèn đọc sách đầu giường hoặc bàn làm việc.',
                'image_path'       => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?w=700&auto=format&fit=crop&q=80',
                'price'            => 2150000,
                'stock'            => 18,
                'weight'           => 2400,
                'status'           => 'active',
                'is_featured'      => true,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1540932239986-30128078f3c5?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Bê Tông Xám Sáng', 'Bê Tông Than Chì'],
                    'material'        => 'Chân bê tông đúc, khớp gỗ sồi, chao vải linen',
                    'dimensions'      => 'Cao 45cm, Đường kính chao 25cm',
                ],
                'seo_title'        => 'Đèn Bàn Xi Măng Gỗ Cement Wood Lamp — MYSHOP',
                'seo_description'  => 'Đèn bàn trang trí đế bê tông kết hợp gỗ tự nhiên.',
            ],
            [
                'category_id'      => $catSofas->id,
                'name'             => 'Sofa Băng Vải Nỉ Outline Sofa',
                'slug'             => 'outline-sofa-nordic',
                'sku'              => 'SFA-008',
                'description'      => 'Sofa băng 3 chỗ Outline Sofa mang đường nét thanh mảnh, hiện đại với phần tựa tay mỏng tạo cảm giác thanh thoát cho phòng khách. Đệm mút D40 êm ái đàn hồi cao kết hợp vải nỉ dệt thô cao cấp.',
                'image_path'       => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=700&auto=format&fit=crop&q=80',
                'price'            => 28500000,
                'stock'            => 8,
                'weight'           => 45000,
                'status'           => 'active',
                'is_featured'      => true,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Xám Lông Chuột (Dark Gray)', 'Xanh Rêu Bắc Âu (Moss Green)', 'Màu Be Cát (Sand Beige)'],
                    'material'        => 'Khung gỗ thông tự nhiên, chân nhôm đúc, vải nỉ bọc cao cấp',
                    'dimensions'      => 'Dài 220cm x Sâu 84cm x Cao 71cm',
                ],
                'seo_title'        => 'Sofa Băng Vải Nỉ Bắc Âu Outline Sofa — MYSHOP',
                'seo_description'  => 'Sofa băng 3 chỗ phong cách Scandinavian sang trọng.',
            ],

            // ── New Arrivals (Home v12 New Arrivals Grid) ──
            [
                'category_id'      => $catAccessories->id,
                'name'             => 'Heo Gốm Tiết Kiệm Wood Piggy Bank',
                'slug'             => 'wood-piggy-bank',
                'sku'              => 'ACC-009',
                'description'      => 'Ống heo tiết kiệm bằng gốm phủ men bóng kết hợp nút gỗ sồi tự nhiên. Thiết kế điêu khắc tối giản như một tác phẩm nghệ thuật trưng bày trên kệ sách hay bàn làm việc.',
                'image_path'       => 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=700&auto=format&fit=crop&q=80',
                'price'            => 950000,
                'stock'            => 35,
                'weight'           => 500,
                'status'           => 'active',
                'is_featured'      => false,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1563861826100-9cb868fdbe1c?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Gốm Trắng Men Mờ', 'Gốm Nâu Đất Nung'],
                    'material'        => 'Gốm nung nhiệt độ cao, nút cao su bọc gỗ sồi',
                ],
                'seo_title'        => 'Heo Gốm Tiết Kiệm Wood Piggy Bank — MYSHOP',
                'seo_description'  => 'Ống tiết kiệm gốm decor phong cách Scandinavian.',
            ],
            [
                'category_id'      => $catLamps->id,
                'name'             => 'Đèn Bàn Cổ Điển Tribeca Reade Table Lamp',
                'slug'             => 'tribeca-reade-table-lamp',
                'sku'              => 'LMP-010',
                'description'      => 'Đèn bàn kim loại lấy cảm hứng từ phong cách New York thập niên 1930. Thiết kế chân uốn hình học để lộ bóng đèn Edison cổ điển tạo hiệu ứng ánh sáng quyến rũ.',
                'image_path'       => 'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=700&auto=format&fit=crop&q=80',
                'price'            => 1980000,
                'stock'            => 22,
                'weight'           => 1500,
                'status'           => 'active',
                'is_featured'      => false,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1540932239986-30128078f3c5?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1540932239986-30128078f3c5?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Đồng Thau Đánh Bóng (Brass)', 'Thép Sơn Đen Nhám'],
                    'material'        => 'Đồng thau nguyên chất hoặc thép sơn tĩnh điện',
                    'dimensions'      => 'Cao 34cm, Rộng 15cm',
                ],
                'seo_title'        => 'Đèn Bàn Cổ Điển Tribeca Reade Table Lamp — MYSHOP',
                'seo_description'  => 'Đèn bàn kim loại đồng thau phong cách cổ điển sang trọng.',
            ],
            [
                'category_id'      => $catAccessories->id,
                'name'             => 'Ly Giữ Nhiệt Cao Cấp To Go Cup',
                'slug'             => 'to-go-cup-minimal',
                'sku'              => 'ACC-011',
                'description'      => 'Bình giữ nhiệt 2 lớp inox 304 phủ sơn tĩnh điện nhám. Nắp bật 360 độ cho phép uống từ bất kỳ góc nào mà không lo rò rỉ, giữ nhiệt nóng 8 giờ và lạnh 16 giờ.',
                'image_path'       => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=700&auto=format&fit=crop&q=80',
                'price'            => 680000,
                'stock'            => 60,
                'weight'           => 350,
                'status'           => 'active',
                'is_featured'      => false,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1534040385115-33dcb3acba5b?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1534040385115-33dcb3acba5b?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1584990347449-399a9a3b6f12?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Xanh Olive', 'Xám Đậm', 'Trắng Sữa'],
                    'material'        => 'Thép không gỉ 304, nắp nhựa PP không chứa BPA',
                    'capacity'        => '350ml',
                ],
                'seo_title'        => 'Ly Giữ Nhiệt To Go Cup — MYSHOP',
                'seo_description'  => 'Ly giữ nhiệt inox 304 tối giản phong cách Bắc Âu.',
            ],
            [
                'category_id'      => $catTables->id,
                'name'             => 'Bàn Trà Tròn Gỗ Walnut Around Coffee Table',
                'slug'             => 'around-coffee-table',
                'sku'              => 'TBL-012',
                'description'      => 'Bàn sofa tròn Around Coffee Table có gờ viền nâng cao xung quanh giúp ngăn đồ vật rơi vỡ. Chân gỗ sồi thon gọn tạo khoảng thoáng bên dưới, rất thích hợp cho căn hộ hiện đại.',
                'image_path'       => 'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?w=700&auto=format&fit=crop&q=80',
                'price'            => 8900000,
                'stock'            => 15,
                'weight'           => 9500,
                'status'           => 'active',
                'is_featured'      => false,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1532372320572-cda25653a26d?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1532372320572-cda25653a26d?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Gỗ Óc Chó (Walnut)', 'Gỗ Sồi Sáng (Light Oak)', 'Xám Tro'],
                    'material'        => 'Gỗ sồi uốn ép nhiệt phủ veneer óc chó',
                    'dimensions'      => 'Đường kính 72cm, Cao 36cm',
                ],
                'seo_title'        => 'Bàn Trà Tròn Gỗ Walnut Around Coffee Table — MYSHOP',
                'seo_description'  => 'Bàn trà sofa tròn gỗ sồi tự nhiên phong cách tối giản.',
            ],
            [
                'category_id'      => $catChairs->id,
                'name'             => 'Ghế Bành Thư Giãn Doze Lounge Chair',
                'slug'             => 'doze-lounge-chair',
                'sku'              => 'CHR-013',
                'description'      => 'Ghế thư giãn Doze Lounge Chair kết hợp giữa phong cách hiện đại và sự tiện nghi tối đa. Tựa lưng cao ôm trọn cơ thể, chân thép thanh mảnh tạo nét sang trọng cho góc đọc sách.',
                'image_path'       => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=700&auto=format&fit=crop&q=80',
                'price'            => 18500000,
                'stock'            => 10,
                'weight'           => 16000,
                'status'           => 'active',
                'is_featured'      => false,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Vải Dạ Màu Be', 'Vải Nhung Xanh Đậm', 'Da Bò Cognac'],
                    'material'        => 'Khung thép đúc bọc mút định hình, chân thép sơn tĩnh điện',
                    'dimensions'      => 'Dài 95cm x Rộng 100cm x Cao 106cm',
                ],
                'seo_title'        => 'Ghế Bành Thư Giãn Doze Lounge Chair — MYSHOP',
                'seo_description'  => 'Ghế bành armchair thư giãn đọc sách cao cấp.',
            ],
            [
                'category_id'      => $catShelves->id,
                'name'             => 'Hệ Kệ Sách Khung Gỗ Compile Shelving System',
                'slug'             => 'compile-shelving-system',
                'sku'              => 'SHF-014',
                'description'      => 'Hệ thống kệ trang trí module Compile Shelving System cho phép lắp ghép và mở rộng linh hoạt theo chiều ngang và dọc. Khung gỗ sồi chắc chắn kết hợp đợt kệ sơn mờ sang trọng.',
                'image_path'       => 'https://images.unsplash.com/photo-1594026112284-02bb6f3352fe?w=700&auto=format&fit=crop&q=80',
                'price'            => 16800000,
                'stock'            => 14,
                'weight'           => 22000,
                'status'           => 'active',
                'is_featured'      => false,
                'attributes_json'  => [
                    'secondary_image' => 'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?w=700&auto=format&fit=crop&q=80',
                    'gallery'         => [
                        'https://images.unsplash.com/photo-1594026112284-02bb6f3352fe?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?w=700&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=700&auto=format&fit=crop&q=80',
                    ],
                    'color'           => ['Trắng Sữa & Sồi', 'Xám Đậm & Sồi', 'Toàn Bộ Đen'],
                    'material'        => 'Gỗ sồi nhập khẩu và thép dập sơn tĩnh điện',
                    'dimensions'      => 'Dài 120cm x Sâu 42cm x Cao 150cm',
                ],
                'seo_title'        => 'Hệ Kệ Sách Khung Gỗ Compile Shelving — MYSHOP',
                'seo_description'  => 'Kệ sách trang trí module gỗ sồi tự nhiên cao cấp.',
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $data) {
            $createdProducts[$data['slug']] = Product::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        // ── 6. PRODUCT VARIANTS ──────────────────────────────────────────────
        $chairProduct = $createdProducts['synnes-dining-chair'] ?? null;
        if ($chairProduct) {
            ProductVariant::updateOrCreate(
                ['sku' => 'CHR-004-OAK'],
                [
                    'product_id'      => $chairProduct->id,
                    'name'            => 'Gỗ Sồi Tự Nhiên (Natural Oak)',
                    'price'           => 5800000,
                    'stock'           => 10,
                    'is_active'       => true,
                    'attributes_json' => ['color' => 'Natural Oak', 'material' => 'Oak Wood'],
                ]
            );

            ProductVariant::updateOrCreate(
                ['sku' => 'CHR-004-WAL'],
                [
                    'product_id'      => $chairProduct->id,
                    'name'            => 'Gỗ Óc Chó (Walnut Dark)',
                    'price'           => 6400000,
                    'stock'           => 10,
                    'is_active'       => true,
                    'attributes_json' => ['color' => 'Walnut Dark', 'material' => 'Walnut Wood'],
                ]
            );
        }

        $lampProduct = $createdProducts['ambit-pendant-lamp'] ?? null;
        if ($lampProduct) {
            ProductVariant::updateOrCreate(
                ['sku' => 'LMP-001-WHT'],
                [
                    'product_id'      => $lampProduct->id,
                    'name'            => 'Màu Trắng Mờ (Matte White)',
                    'price'           => 4500000,
                    'stock'           => 15,
                    'is_active'       => true,
                    'attributes_json' => ['color' => 'Matte White'],
                ]
            );

            ProductVariant::updateOrCreate(
                ['sku' => 'LMP-001-BLK'],
                [
                    'product_id'      => $lampProduct->id,
                    'name'            => 'Màu Đen Tuyển (Matte Black)',
                    'price'           => 4500000,
                    'stock'           => 10,
                    'is_active'       => true,
                    'attributes_json' => ['color' => 'Matte Black'],
                ]
            );
        }

        $stoolProduct = $createdProducts['arte-60-stool'] ?? null;
        if ($stoolProduct) {
            ProductVariant::updateOrCreate(
                ['sku' => 'CHR-006-BIRCH'],
                [
                    'product_id'      => $stoolProduct->id,
                    'name'            => 'Gỗ Bạch Dương Tự Nhiên',
                    'price'           => 3200000,
                    'stock'           => 20,
                    'is_active'       => true,
                    'attributes_json' => [
                        'color' => 'Gỗ Bạch Dương Tự Nhiên',
                        'image' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?w=900&auto=format&fit=crop&q=80',
                    ],
                ]
            );

            ProductVariant::updateOrCreate(
                ['sku' => 'CHR-006-CORAL'],
                [
                    'product_id'      => $stoolProduct->id,
                    'name'            => 'Mặt Cam San Hô',
                    'price'           => 3450000,
                    'stock'           => 12,
                    'is_active'       => true,
                    'attributes_json' => [
                        'color' => 'Mặt Cam San Hô',
                        'image' => 'https://images.unsplash.com/photo-1503602642458-232111445657?w=900&auto=format&fit=crop&q=80',
                    ],
                ]
            );

            ProductVariant::updateOrCreate(
                ['sku' => 'CHR-006-BLACK'],
                [
                    'product_id'      => $stoolProduct->id,
                    'name'            => 'Mặt Đen Tuyển',
                    'price'           => 3450000,
                    'stock'           => 8,
                    'is_active'       => true,
                    'attributes_json' => [
                        'color' => 'Mặt Đen Tuyển',
                        'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=900&auto=format&fit=crop&q=80',
                    ],
                ]
            );
        }

        // ── 7. COUPONS ──────────────────────────────────────────────────────
        Coupon::updateOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type'             => 'percentage',
                'value'            => 10,
                'min_order_amount' => 1000000,
                'usage_limit'      => 500,
                'used_count'       => 12,
                'is_active'        => true,
                'expires_at'       => Carbon::now()->addMonths(6),
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'FREESHIP'],
            [
                'type'             => 'fixed',
                'value'            => 50000,
                'min_order_amount' => 500000,
                'usage_limit'      => 1000,
                'used_count'       => 45,
                'is_active'        => true,
                'expires_at'       => Carbon::now()->addYear(),
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'SOBER100K'],
            [
                'type'             => 'fixed',
                'value'            => 100000,
                'min_order_amount' => 2000000,
                'usage_limit'      => 200,
                'used_count'       => 5,
                'is_active'        => true,
                'expires_at'       => Carbon::now()->addMonths(3),
            ]
        );

        // ── 8. FLASH SALE CAMPAIGN ──────────────────────────────────────────
        $flashSale = FlashSale::updateOrCreate(
            ['name' => 'Flash Sale Mùa Hè 2026 — Nội Thất Bắc Âu'],
            [
                'start_time' => Carbon::now()->subDays(1),
                'end_time'   => Carbon::now()->addDays(7),
                'status'     => 'active',
            ]
        );

        if ($lampProduct) {
            FlashSaleItem::updateOrCreate(
                [
                    'flash_sale_id' => $flashSale->id,
                    'product_id'    => $lampProduct->id,
                ],
                [
                    'price'         => 3990000,
                    'quantity'      => 20,
                    'sold_quantity' => 8,
                ]
            );
        }

        $clockProduct = $createdProducts['freakish-clock'] ?? null;
        if ($clockProduct) {
            FlashSaleItem::updateOrCreate(
                [
                    'flash_sale_id' => $flashSale->id,
                    'product_id'    => $clockProduct->id,
                ],
                [
                    'price'         => 2190000,
                    'quantity'      => 15,
                    'sold_quantity' => 5,
                ]
            );
        }

        // ── 9. SAMPLE PRODUCT REVIEWS ───────────────────────────────────────
        if ($chairProduct) {
            ProductReview::firstOrCreate(
                [
                    'product_id'  => $chairProduct->id,
                    'customer_id' => $customer1->id,
                ],
                [
                    'rating'      => 5,
                    'comment'     => 'Ghế gỗ sồi cực kỳ chắc chắn và hoàn thiện sắc sảo đến từng chi tiết. Rất hài lòng với chất lượng phục vụ và đóng gói giao hàng của MYSHOP!',
                    'status'      => 'approved',
                ]
            );
        }

        if ($lampProduct) {
            ProductReview::firstOrCreate(
                [
                    'product_id'  => $lampProduct->id,
                    'customer_id' => $customer2->id,
                ],
                [
                    'rating'      => 5,
                    'comment'     => 'Ánh sáng đèn tỏa ra rất dịu mắt, màu sơn tĩnh điện mờ nhìn rất sang. Đặt ở bàn ăn ai đến nhà cũng khen ngợi.',
                    'status'      => 'approved',
                ]
            );
        }

        // ── 10. PROVINCES SEEDER ────────────────────────────────────────────
        $this->call(ProvinceSeeder::class);
    }
}
