@extends('layouts.storefront')

@pushonce('page_title')Giới Thiệu — @endpushonce
@pushonce('meta_description')Về MYSHOP — Thương hiệu nội thất và phong cách sống phong cách Bắc Âu Scandinavian tối giản, thanh lịch.@endpushonce

@section('content')
<div class="py-16 md:py-24">
    <div class="section-wrapper max-w-4xl mx-auto">
        <h1 class="text-3xl md:text-4xl font-light tracking-tight text-primary-dark text-center mb-6">
            Về MYSHOP
        </h1>
        <p class="text-sm md:text-base text-muted-text text-center leading-relaxed font-light mb-12 max-w-2xl mx-auto">
            Chúng tôi tin rằng không gian sống đẹp và tối giản mang lại sự bình yên và nguồn cảm hứng sáng tạo mỗi ngày.
        </p>

        <div class="space-y-12 text-primary-dark font-light leading-relaxed">
            <div class="aspect-[16/9] bg-[#E8E4DF] overflow-hidden">
                <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=1200&auto=format&fit=crop&q=80"
                     alt="MYSHOP Scandinavian Furniture Studio"
                     class="w-full h-full object-cover">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6">
                <div>
                    <h2 class="text-lg font-medium tracking-wide uppercase mb-3 text-primary-dark">Triết Lý Thiết Kế</h2>
                    <p class="text-sm text-muted-text leading-relaxed">
                        Lấy cảm hứng từ tinh thần thẩm mỹ Scandinavian Bắc Âu — nơi sự tối giản kết hợp hài hòa với công năng sử dụng. Mỗi sản phẩm được chọn lọc tỉ mỉ từ chất liệu gỗ tự nhiên, da thật, kim loại sơn tĩnh điện và vải nỉ cao cấp.
                    </p>
                </div>
                <div>
                    <h2 class="text-lg font-medium tracking-wide uppercase mb-3 text-primary-dark">Cam Kết Chất Lượng</h2>
                    <p class="text-sm text-muted-text leading-relaxed">
                        Chất lượng bền vững và độ hoàn thiện tinh xảo luôn là ưu tiên hàng đầu của chúng tôi. Chúng tôi cam kết bảo hành chính hãng, chính sách đổi trả minh bạch trong 30 ngày và giao hàng an toàn trên toàn quốc.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
