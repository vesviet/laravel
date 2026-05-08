@extends('layouts.app')
@section('title', 'Giới thiệu - Máy Điện Giải Sài Gòn')
@section('meta_description', 'Máy Điện Giải Sài Gòn - Đơn vị phân phối máy điện giải nước ion kiềm chính hãng hàng đầu Việt Nam.')
@section('content')
<div style="height: var(--header-height);"></div>
<section class="section">
    <div class="container" style="max-width:800px;">
        <div class="section-header">
            <div class="section-badge">Về chúng tôi</div>
            <h1 class="section-title">Máy Điện Giải Sài Gòn</h1>
            <p class="section-desc">Đơn vị phân phối máy điện giải nước ion kiềm chính hãng hàng đầu tại TP.HCM</p>
        </div>

        <div style="line-height:1.8;color:var(--color-gray-700);">
            <p style="margin-bottom:1.5rem;">
                <strong>Máy Điện Giải Sài Gòn</strong> tự hào là đơn vị tiên phong trong lĩnh vực phân phối các dòng máy điện giải nước ion kiềm 
                chính hãng tại thị trường Việt Nam. Với sứ mệnh mang đến nguồn nước sạch, an toàn và tốt cho sức khỏe, 
                chúng tôi cam kết cung cấp những sản phẩm chất lượng cao nhất từ các thương hiệu uy tín hàng đầu thế giới.
            </p>
            <p style="margin-bottom:1.5rem;">
                Đội ngũ kỹ thuật viên được đào tạo chuyên nghiệp, sẵn sàng tư vấn và lắp đặt miễn phí tận nhà. 
                Chúng tôi tin rằng mỗi gia đình đều xứng đáng được sử dụng nguồn nước tốt nhất cho sức khỏe.
            </p>
        </div>

        <div class="features-grid" style="margin-top:3rem;">
            <div class="feature-card">
                <div class="feature-icon">🏆</div>
                <h3 class="feature-title">10+ Năm</h3>
                <p class="feature-desc">Kinh nghiệm trong ngành máy điện giải</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👨‍👩‍👧‍👦</div>
                <h3 class="feature-title">5,000+</h3>
                <p class="feature-desc">Gia đình đã tin dùng sản phẩm</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⭐</div>
                <h3 class="feature-title">4.9/5</h3>
                <p class="feature-desc">Đánh giá từ khách hàng</p>
            </div>
        </div>
    </div>
</section>
@endsection
