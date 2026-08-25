@extends("layouts.storefront")

@pushonce("page_title")Hồ Sơ Của Tôi — @endpushonce
@pushonce("meta_description")Quản lý thông tin cá nhân, đổi mật khẩu và cài đặt tài khoản tại {{ config("app.name", "Sober Furniture") }}.@endpushonce

@section("content")
<div class="py-10 md:py-14 bg-[#FAFAFA]">
    <div class="section-wrapper">
        
        {{-- ── Account Header ── --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-[#E5E5E5]">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-light text-[#23232C] tracking-wide uppercase">Hồ Sơ</h1>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 {{ $customer->membership_tier_badge_classes }}">
                        {{ $customer->membership_tier }}
                    </span>
                </div>
                <p class="text-xs text-[#888888] font-light mt-1">
                    Xin chào, <span class="text-[#23232C] font-semibold">{{ $customer->name }}</span> ({{ $customer->email }})
                </p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route("account.wishlist") }}" class="text-xs uppercase tracking-wider text-[#23232C] hover:text-[#E84444] font-medium link-underline">
                    Danh Sách Yêu Thích
                </a>
                <form method="POST" action="{{ route("account.logout") }}">
                    @csrf
                    <button type="submit"
                            class="text-xs tracking-wider uppercase text-[#888888] hover:text-[#E84444] transition-colors cursor-pointer">
                        Đăng Xuất
                    </button>
                </form>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session("success"))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs mb-6">
                {{ session("success") }}
            </div>
        @endif
        @if(session("error"))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs mb-6">
                {{ session("error") }}
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- ── Profile Info & Avatar ── --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm sticky top-24">
                    <div class="text-center mb-6">
                        @if($customer->avatar)
                            <img src="{{ Storage::url($customer->avatar) }}" alt="{{ $customer->name }}" class="w-24 h-24 rounded-full object-cover mx-auto mb-3 border border-[#E5E5E5]">
                        @else
                            <div class="w-24 h-24 rounded-full bg-[#F0F0F0] border border-[#E5E5E5] flex items-center justify-center mx-auto mb-3">
                                <span class="text-3xl font-light text-[#888888]">{{ Str::upper($customer->name[0]) }}</span>
                            </div>
                        @endif
                        <h2 class="text-lg font-medium text-[#23232C]">{{ $customer->name }}</h2>
                        <p class="text-xs text-[#888888] font-light">{{ $customer->email }}</p>
                    </div>

                    {{-- Stats --}}
                    <div class="border-t border-[#E5E5E5] pt-6 space-y-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1">Tổng Chi Tiêu</p>
                            <p class="text-xl font-bold text-[#E84444]">{{ $customer->formatted_total_spent }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1">Điểm Thưởng</p>
                            <p class="text-xl font-bold text-amber-600">{{ number_format($customer->loyalty_points ?? 0) }}đ</p>
                        </div>
                        @if($customer->referral_code)
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1">Mã Giới Thiệu</p>
                            <p class="font-mono text-sm text-[#23232C]">{{ $customer->referral_code }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Forms ── --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Profile Form --}}
                <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm">
                    <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] mb-6 pb-3 border-b border-[#E5E5E5]">Thông Tin Cá Nhân</h3>
                    
                    <form method="POST" action="{{ route("account.profile.update") }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method("PUT")

                        {{-- Avatar --}}
                        <div>
                            <label class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Ảnh Đại Diện</label>
                            <div class="flex items-center gap-4">
                                @if($customer->avatar)
                                    <img src="{{ Storage::url($customer->avatar) }}" alt="Current avatar" class="w-16 h-16 rounded-full object-cover border border-[#E5E5E5]">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-[#F0F0F0] border border-[#E5E5E5] flex items-center justify-center">
                                        <span class="text-2xl font-light text-[#888888]">{{ Str::upper($customer->name[0]) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="input-underline w-full max-w-xs">
                                    @error("avatar")
                                        <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                    <p class="text-[10px] text-[#888888] font-light mt-1">JPEG, PNG, WebP — tối đa 2MB</p>
                                </div>
                            </div>
                        </div>

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Họ và tên <span class="text-[#E84444]">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old("name", $customer->name) }}" required autocomplete="name" class="input-underline w-full @error("name") border-[#E84444] @enderror">
                            @error("name")
                                <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Email <span class="text-[#E84444]">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old("email", $customer->email) }}" required autocomplete="email" class="input-underline w-full @error("email") border-[#E84444] @enderror">
                            @error("email")
                                <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Số điện thoại <span class="text-[#888888] text-[9px] normal-case tracking-normal">(tuỳ chọn)</span></label>
                            <input type="tel" name="phone" id="phone" value="{{ old("phone", $customer->phone) }}" autocomplete="tel" class="input-underline w-full @error("phone") border-[#E84444] @enderror">
                            @error("phone")
                                <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Date of Birth --}}
                        <div>
                            <label for="date_of_birth" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Ngày sinh <span class="text-[#888888] text-[9px] normal-case tracking-normal">(tuỳ chọn)</span></label>
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old("date_of_birth", $customer->date_of_birth?->format("Y-m-d")) }}" class="input-underline w-full @error("date_of_birth") border-[#E84444] @enderror">
                            @error("date_of_birth")
                                <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Gender --}}
                        <div>
                            <label class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Giới tính <span class="text-[#888888] text-[9px] normal-case tracking-normal">(tuỳ chọn)</span></label>
                            <div class="flex gap-6">
                                @foreach(["male" => "Nam", "female" => "Nữ", "other" => "Khác"] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="gender" value="{{ $value }}" {{ old("gender", $customer->gender) === $value ? "checked" : "" }} class="h-4 w-4 accent-[#1a1a1a]">
                                        <span class="text-sm text-[#23232C] font-light">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error("gender")
                                <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn-dark w-full sm:w-auto">Lưu Thay Đổi</button>
                    </form>
                </div>

                {{-- Change Password --}}
                <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm">
                    <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] mb-6 pb-3 border-b border-[#E5E5E5]">Đổi Mật Khẩu</h3>
                    
                    <form method="POST" action="{{ route("account.profile.password") }}" class="space-y-6">
                        @csrf
                        @method("PUT")

                        <div>
                            <label for="current_password" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Mật khẩu hiện tại <span class="text-[#E84444]">*</span></label>
                            <input type="password" name="current_password" id="current_password" required autocomplete="current-password" class="input-underline w-full @error("current_password") border-[#E84444] @enderror">
                            @error("current_password")
                                <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Mật khẩu mới <span class="text-[#E84444]">*</span></label>
                            <input type="password" name="password" id="password" required autocomplete="new-password" class="input-underline w-full @error("password") border-[#E84444] @enderror">
                            @error("password")
                                <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                            <p class="text-[10px] text-[#888888] font-light mt-1">Tối thiểu 8 ký tự, có chữ hoa, chữ thường, số và ký tự đặc biệt</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Xác nhận mật khẩu mới <span class="text-[#E84444]">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" class="input-underline w-full">
                            @error("password_confirmation")
                                <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn-dark w-full sm:w-auto">Đổi Mật Khẩu</button>
                    </form>
                </div>

                {{-- Delete Account --}}
                <div class="bg-rose-50 border border-rose-200 p-6 shadow-sm">
                    <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-rose-800 mb-4 pb-3 border-b border-rose-200">Xóa Tài Khoản</h3>
                    <p class="text-sm text-rose-700 mb-4">Hành động này không thể hoàn tác. Tất cả dữ liệu cá nhân sẽ bị ẩn danh, lịch sử đơn hàng được giữ lại nhưng không liên kết với tài khoản.</p>
                    
                    <form method="POST" action="{{ route("account.profile.delete") }}" onsubmit="return confirm("Bạn có chắc chắn muốn xóa tài khoản? Hành động này không thể hoàn tác.")" class="space-y-4">
                        @csrf
                        @method("DELETE")

                        <div>
                            <label for="password_confirmation" class="block text-[10px] tracking-[0.15em] uppercase text-rose-700 mb-2">Nhập mật khẩu để xác nhận <span class="text-[#E84444]">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="current-password" class="input-underline w-full @error("password_confirmation") border-[#E84444] @enderror">
                            @error("password_confirmation")
                                <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="border border-rose-400 text-rose-700 hover:bg-rose-400 hover:text-white text-xs font-semibold uppercase tracking-wider px-6 py-2 transition-colors cursor-pointer">Xóa Tài Khoản</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
