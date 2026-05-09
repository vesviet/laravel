<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Giới thiệu',
                'slug' => 'gioi-thieu',
                'group' => 'general',
                'template' => 'about',
                'is_published' => true,
                'sort_order' => 1,
                'excerpt' => 'Đơn vị phân phối máy điện giải nước ion kiềm chính hãng hàng đầu tại TP.HCM',
                'content' => '<p><strong>Máy Điện Giải Sài Gòn</strong> tự hào là đơn vị tiên phong trong lĩnh vực phân phối các dòng máy điện giải nước ion kiềm chính hãng tại thị trường Việt Nam. Với sứ mệnh mang đến nguồn nước sạch, an toàn và tốt cho sức khỏe, chúng tôi cam kết cung cấp những sản phẩm chất lượng cao nhất từ các thương hiệu uy tín hàng đầu thế giới.</p><p>Đội ngũ kỹ thuật viên được đào tạo chuyên nghiệp, sẵn sàng tư vấn và lắp đặt miễn phí tận nhà. Chúng tôi tin rằng mỗi gia đình đều xứng đáng được sử dụng nguồn nước tốt nhất cho sức khỏe.</p>',
                'meta_title' => 'Giới thiệu - Máy Điện Giải Sài Gòn',
                'meta_description' => 'Máy Điện Giải Sài Gòn - Đơn vị phân phối máy điện giải nước ion kiềm chính hãng hàng đầu Việt Nam.',
            ],
            [
                'title' => 'Liên Hệ Với Chúng Tôi',
                'slug' => 'lien-he',
                'group' => 'general',
                'template' => 'contact',
                'is_published' => true,
                'sort_order' => 2,
                'excerpt' => 'Chúng tôi luôn sẵn sàng hỗ trợ bạn',
                'content' => null,
                'meta_title' => 'Liên hệ - Máy Điện Giải Sài Gòn',
                'meta_description' => 'Liên hệ Máy Điện Giải Sài Gòn để được tư vấn miễn phí về máy điện giải nước ion kiềm.',
            ],
            [
                'title' => 'Chính sách bảo hành',
                'slug' => 'chinh-sach-bao-hanh',
                'group' => 'policy',
                'template' => 'default',
                'is_published' => true,
                'sort_order' => 1,
                'excerpt' => 'Cam kết bảo hành chính hãng trên toàn quốc',
                'content' => '<h2>Điều kiện bảo hành</h2><p>Tất cả sản phẩm máy điện giải nước ion kiềm do Máy Điện Giải Sài Gòn phân phối đều được bảo hành chính hãng từ <strong>3 - 8 năm</strong> tùy dòng sản phẩm.</p><h2>Quy trình bảo hành</h2><ul><li>Bước 1: Liên hệ Hotline <strong>0901 234 567</strong> hoặc gửi tin nhắn qua website.</li><li>Bước 2: Kỹ thuật viên kiểm tra và xác nhận lỗi trong vòng 24h.</li><li>Bước 3: Sửa chữa hoặc thay thế linh kiện tại nhà khách hàng.</li></ul><h2>Trường hợp không được bảo hành</h2><ul><li>Sản phẩm bị hư hỏng do thiên tai, hỏa hoạn, nguồn điện không ổn định.</li><li>Sản phẩm bị tháo mở, sửa chữa bởi cá nhân hoặc đơn vị không được ủy quyền.</li><li>Tem bảo hành bị rách, mờ hoặc không còn nguyên vẹn.</li></ul>',
                'meta_title' => 'Chính sách bảo hành - Máy Điện Giải Sài Gòn',
                'meta_description' => 'Chính sách bảo hành máy điện giải nước ion kiềm - Bảo hành chính hãng 3-8 năm tại Máy Điện Giải Sài Gòn.',
            ],
            [
                'title' => 'Chính sách đổi trả',
                'slug' => 'chinh-sach-doi-tra',
                'group' => 'policy',
                'template' => 'default',
                'is_published' => true,
                'sort_order' => 2,
                'excerpt' => 'Đổi trả miễn phí trong 7 ngày đầu tiên',
                'content' => '<h2>Điều kiện đổi trả</h2><p>Quý khách có quyền đổi trả sản phẩm trong vòng <strong>7 ngày</strong> kể từ ngày nhận hàng nếu:</p><ul><li>Sản phẩm bị lỗi kỹ thuật từ nhà sản xuất.</li><li>Sản phẩm giao không đúng mẫu mã, chủng loại đã đặt.</li><li>Sản phẩm còn nguyên tem, hộp, phụ kiện đi kèm.</li></ul><h2>Quy trình đổi trả</h2><ul><li>Bước 1: Liên hệ Hotline hoặc gửi yêu cầu qua email.</li><li>Bước 2: Nhân viên kiểm tra và xác nhận trong 24h.</li><li>Bước 3: Thu hồi sản phẩm cũ và giao sản phẩm mới (hoặc hoàn tiền).</li></ul>',
                'meta_title' => 'Chính sách đổi trả - Máy Điện Giải Sài Gòn',
                'meta_description' => 'Chính sách đổi trả máy điện giải - Đổi trả miễn phí trong 7 ngày tại Máy Điện Giải Sài Gòn.',
            ],
            [
                'title' => 'Chính sách giao hàng',
                'slug' => 'chinh-sach-giao-hang',
                'group' => 'policy',
                'template' => 'default',
                'is_published' => false,
                'sort_order' => 3,
                'excerpt' => 'Miễn phí giao hàng và lắp đặt tận nhà',
                'content' => '<h2>Phạm vi giao hàng</h2><p>Máy Điện Giải Sài Gòn giao hàng <strong>miễn phí</strong> tại TP.HCM và các tỉnh lân cận.</p><h2>Thời gian giao hàng</h2><ul><li>Nội thành TP.HCM: 1-2 ngày làm việc.</li><li>Các tỉnh lân cận: 2-4 ngày làm việc.</li><li>Các tỉnh xa: 3-7 ngày làm việc.</li></ul>',
                'meta_title' => 'Chính sách giao hàng - Máy Điện Giải Sài Gòn',
                'meta_description' => 'Chính sách giao hàng máy điện giải - Miễn phí giao hàng và lắp đặt tận nhà tại TP.HCM.',
            ],
            [
                'title' => 'Chính sách bảo mật',
                'slug' => 'chinh-sach-bao-mat',
                'group' => 'policy',
                'template' => 'default',
                'is_published' => false,
                'sort_order' => 4,
                'excerpt' => 'Cam kết bảo mật thông tin khách hàng',
                'content' => '<h2>Thu thập thông tin</h2><p>Chúng tôi chỉ thu thập thông tin cần thiết cho việc xử lý đơn hàng: Họ tên, Số điện thoại, Địa chỉ giao hàng, Email (nếu có).</p><h2>Sử dụng thông tin</h2><p>Thông tin khách hàng chỉ được sử dụng để: Xử lý đơn hàng, Liên hệ hỗ trợ kỹ thuật, Gửi thông tin khuyến mãi (nếu khách đồng ý).</p><h2>Bảo mật thông tin</h2><p>Chúng tôi cam kết <strong>không chia sẻ, bán hoặc trao đổi</strong> thông tin cá nhân của khách hàng cho bất kỳ bên thứ ba nào.</p>',
                'meta_title' => 'Chính sách bảo mật - Máy Điện Giải Sài Gòn',
                'meta_description' => 'Chính sách bảo mật thông tin khách hàng tại Máy Điện Giải Sài Gòn.',
            ],
            [
                'title' => 'Hướng dẫn mua hàng',
                'slug' => 'huong-dan-mua-hang',
                'group' => 'support',
                'template' => 'default',
                'is_published' => false,
                'sort_order' => 1,
                'excerpt' => 'Hướng dẫn các bước mua hàng đơn giản',
                'content' => '<h2>Cách mua hàng trên website</h2><ol><li><strong>Chọn sản phẩm:</strong> Duyệt danh mục sản phẩm, xem thông tin chi tiết.</li><li><strong>Thêm vào giỏ:</strong> Nhấn nút "Thêm vào giỏ hàng".</li><li><strong>Thanh toán:</strong> Nhấn vào giỏ hàng, kiểm tra và tiến hành thanh toán.</li><li><strong>Nhập thông tin:</strong> Điền họ tên, số điện thoại, địa chỉ giao hàng.</li><li><strong>Xác nhận:</strong> Chọn phương thức thanh toán (COD hoặc Chuyển khoản) và xác nhận đơn hàng.</li></ol>',
                'meta_title' => 'Hướng dẫn mua hàng - Máy Điện Giải Sài Gòn',
                'meta_description' => 'Hướng dẫn mua hàng online tại Máy Điện Giải Sài Gòn - Đơn giản, nhanh chóng.',
            ],
            [
                'title' => 'Hướng dẫn thanh toán',
                'slug' => 'huong-dan-thanh-toan',
                'group' => 'support',
                'template' => 'default',
                'is_published' => false,
                'sort_order' => 2,
                'excerpt' => 'Các phương thức thanh toán được chấp nhận',
                'content' => '<h2>Phương thức thanh toán</h2><h3>1. Thanh toán khi nhận hàng (COD)</h3><p>Quý khách thanh toán bằng tiền mặt khi nhận được sản phẩm. Áp dụng cho tất cả đơn hàng.</p><h3>2. Chuyển khoản ngân hàng</h3><p>Chuyển khoản trước khi giao hàng. Hệ thống sẽ tự động tạo mã QR chuyển khoản sau khi đặt hàng thành công.</p>',
                'meta_title' => 'Hướng dẫn thanh toán - Máy Điện Giải Sài Gòn',
                'meta_description' => 'Hướng dẫn thanh toán tại Máy Điện Giải Sài Gòn - COD hoặc Chuyển khoản ngân hàng.',
            ],
            [
                'title' => 'Câu hỏi thường gặp',
                'slug' => 'cau-hoi-thuong-gap',
                'group' => 'support',
                'template' => 'faq',
                'is_published' => false,
                'sort_order' => 3,
                'excerpt' => 'Giải đáp các thắc mắc phổ biến',
                'content' => '<h3>Máy điện giải nước ion kiềm là gì?</h3><p>Máy điện giải nước ion kiềm là thiết bị sử dụng công nghệ điện phân để tách nước thành nước ion kiềm (có độ pH cao) và nước axit. Nước ion kiềm giàu hydrogen có nhiều lợi ích cho sức khỏe.</p><h3>Máy điện giải có tốn điện không?</h3><p>Không. Máy điện giải hoạt động với công suất thấp (khoảng 80-150W), chi phí điện năng không đáng kể.</p><h3>Nên chọn máy điện giải nào?</h3><p>Tùy thuộc vào nhu cầu sử dụng và ngân sách. Hãy liên hệ hotline <strong>0901 234 567</strong> để được tư vấn miễn phí.</p>',
                'meta_title' => 'Câu hỏi thường gặp - Máy Điện Giải Sài Gòn',
                'meta_description' => 'Giải đáp các câu hỏi thường gặp về máy điện giải nước ion kiềm tại Máy Điện Giải Sài Gòn.',
            ],
        ];

        foreach ($pages as $pageData) {
            Page::create($pageData);
        }
    }
}
