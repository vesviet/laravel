@extends('layouts.storefront')

@pushonce('page_title')Liên Hệ — @endpushonce
@pushonce('meta_description')Liên hệ với đội ngũ MYSHOP để được tư vấn thiết kế nội thất và giải đáp thắc mắc dịch vụ.@endpushonce

@section('content')
<div class="py-16 md:py-24">
    <div class="section-wrapper max-w-4xl mx-auto">
        <h1 class="text-3xl md:text-4xl font-light tracking-tight text-primary-dark text-center mb-6">
            Liên Hệ Với Chúng Tôi
        </h1>
        <p class="text-sm md:text-base text-muted-text text-center leading-relaxed font-light mb-12 max-w-xl mx-auto">
            Đội ngũ tư vấn thiết kế và chăm sóc khách hàng của MYSHOP luôn sẵn sàng đồng hành cùng bạn.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-6">
            {{-- Contact info --}}
            <div class="space-y-6 text-primary-dark">
                <div>
                    <h2 class="text-xs font-semibold tracking-[0.2em] uppercase text-muted-text mb-2">Showroom Trưng Bày</h2>
                    <p class="text-sm font-light leading-relaxed">9606 North MoPac Expressway Suite 700, Austin, TX 78759</p>
                </div>

                <div>
                    <h2 class="text-xs font-semibold tracking-[0.2em] uppercase text-muted-text mb-2">Hotline Hỗ Trợ</h2>
                    <p class="text-sm font-light"><a href="tel:+841900123456" class="hover:underline">+84 (0) 1900 123 456</a> (8:30 - 21:30 hàng ngày)</p>
                </div>

                <div>
                    <h2 class="text-xs font-semibold tracking-[0.2em] uppercase text-muted-text mb-2">Email Hỗ Trợ</h2>
                    <p class="text-sm font-light"><a href="mailto:support@myshop.vn" class="hover:underline">support@myshop.vn</a></p>
                </div>

                <div>
                    <h2 class="text-xs font-semibold tracking-[0.2em] uppercase text-muted-text mb-2">Giờ Làm Việc</h2>
                    <p class="text-sm font-light">Thứ Hai – Chủ Nhật: 09:00 – 21:00</p>
                </div>
            </div>

            {{-- Contact form mockup --}}
            <div class="bg-white p-8 border border-border-subtle">
                <h3 class="text-sm font-semibold tracking-[0.15em] uppercase text-primary-dark mb-6">Gửi Tin Nhắn Cho Chúng Tôi</h3>
                <form class="space-y-4">
                    <div>
                        <label for="contact-name" class="block text-[10px] tracking-[0.15em] uppercase text-muted-text mb-1">Họ và tên</label>
                        <input type="text" id="contact-name" name="name" class="input-underline w-full" placeholder="Nguyễn Văn A">
                    </div>
                    <div>
                        <label for="contact-email" class="block text-[10px] tracking-[0.15em] uppercase text-muted-text mb-1">Email</label>
                        <input type="email" id="contact-email" name="email" class="input-underline w-full" placeholder="email@domain.com">
                    </div>
                    <div>
                        <label for="contact-message" class="block text-[10px] tracking-[0.15em] uppercase text-muted-text mb-1">Nội dung</label>
                        <textarea rows="4" id="contact-message" name="message" class="input-underline w-full resize-none" placeholder="Lời nhắn của bạn..."></textarea>
                    </div>
                    <button type="button" class="btn-dark w-full text-xs">Gửi Tin Nhắn</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
