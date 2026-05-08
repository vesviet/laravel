<div>
    {{-- Cart Overlay --}}
    <div class="cart-overlay @if($isOpen) active @endif" wire:click="closeCart"></div>

    {{-- Cart Drawer --}}
    <div class="cart-drawer @if($isOpen) open @endif">
        <div class="cart-drawer-header">
            <h3 class="cart-drawer-title">Giỏ hàng ({{ $itemCount }})</h3>
            <button class="cart-drawer-close" wire:click="closeCart" aria-label="Đóng">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="cart-drawer-body">
            @if($isEmpty)
                <div class="cart-empty">
                    <div class="cart-empty-icon">🛒</div>
                    <p>Giỏ hàng trống</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm" style="margin-top:1rem;">Mua sắm ngay</a>
                </div>
            @else
                @foreach($items as $id => $item)
                    <div class="cart-item" wire:key="cart-item-{{ $id }}">
                        <div class="cart-item-image">
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--color-gray-300);">📦</div>
                            @endif
                        </div>
                        <div class="cart-item-info">
                            <div class="cart-item-name">{{ $item['name'] }}</div>
                            <div class="cart-item-price">{{ number_format($item['price'], 0, ',', '.') }}₫</div>
                            <div class="cart-qty">
                                <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})">−</button>
                                <span>{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})">+</button>
                                <button wire:click="removeItem({{ $id }})" style="margin-left:auto;color:var(--color-danger);border-color:transparent;" aria-label="Xóa">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        @if(!$isEmpty)
            <div class="cart-drawer-footer">
                <div class="cart-total">
                    <span>Tạm tính:</span>
                    <span>{{ $total }}</span>
                </div>
                <a href="{{ route('checkout') }}" class="btn btn-primary" style="width:100%;justify-content:center;">
                    Thanh toán →
                </a>
            </div>
        @endif
    </div>
</div>
