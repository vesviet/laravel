<div>
    @if($showQuantity)
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
            <div class="cart-qty">
                <button wire:click="decrementQuantity">−</button>
                <span>{{ $quantity }}</span>
                <button wire:click="incrementQuantity">+</button>
            </div>
        </div>
    @endif

    <button wire:click="addToCart" class="btn btn-primary @if($size === 'sm') btn-sm @elseif($size === 'lg') btn-lg @endif" style="width:100%;justify-content:center;">
        🛒 Thêm vào giỏ
    </button>
</div>
