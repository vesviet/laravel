@extends('layouts.app')
@section('title', 'Thanh toán - Máy Điện Giải Sài Gòn')
@section('content')
<div style="height: var(--header-height);"></div>
<section class="section">
    <div class="container" style="max-width:800px;">
        <h1 class="section-title" style="margin-bottom:2rem;">Thanh Toán</h1>

        @if(session('error'))
            <div style="padding:1rem;background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-md);color:var(--color-danger);margin-bottom:1.5rem;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Họ tên *</label>
                    <input type="text" name="customer_name" class="form-input" value="{{ old('customer_name') }}" required>
                    @error('customer_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Số điện thoại *</label>
                    <input type="tel" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="09xx xxx xxx" required>
                    @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}">
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Địa chỉ giao hàng *</label>
                <textarea name="address" class="form-textarea" rows="2" required>{{ old('address') }}</textarea>
                @error('address') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Tỉnh/Thành phố</label>
                    <input type="text" name="city" class="form-input" value="{{ old('city') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Quận/Huyện</label>
                    <input type="text" name="district" class="form-input" value="{{ old('district') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Phường/Xã</label>
                    <input type="text" name="ward" class="form-input" value="{{ old('ward') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Phương thức thanh toán *</label>
                <div style="display:flex;gap:1rem;margin-top:0.5rem;">
                    <label style="flex:1;display:flex;align-items:center;gap:0.75rem;padding:1rem;border:2px solid var(--color-gray-200);border-radius:var(--radius-md);cursor:pointer;transition:all var(--transition-fast);">
                        <input type="radio" name="payment_method" value="cod" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                        <div>
                            <div style="font-weight:600;">💵 COD</div>
                            <div style="font-size:0.75rem;color:var(--color-gray-500);">Thanh toán khi nhận hàng</div>
                        </div>
                    </label>
                    <label style="flex:1;display:flex;align-items:center;gap:0.75rem;padding:1rem;border:2px solid var(--color-gray-200);border-radius:var(--radius-md);cursor:pointer;transition:all var(--transition-fast);">
                        <input type="radio" name="payment_method" value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}>
                        <div>
                            <div style="font-weight:600;">🏦 Chuyển khoản</div>
                            <div style="font-size:0.75rem;color:var(--color-gray-500);">Chuyển khoản ngân hàng</div>
                        </div>
                    </label>
                </div>
                @error('payment_method') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Ghi chú</label>
                <textarea name="notes" class="form-textarea" rows="2" placeholder="Ghi chú thêm (không bắt buộc)">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-top:1rem;">
                Đặt hàng →
            </button>
        </form>
    </div>
</section>
@endsection
