<div>
    <button wire:click="toggleWishlist" type="button"
            class="p-2 rounded-full hover:bg-surface-light transition-colors focus:outline-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary-dark"
            aria-label="{{ $isWishlisted ? 'Xóa khỏi danh sách yêu thích' : 'Thêm vào danh sách yêu thích' }}"
            aria-pressed="{{ $isWishlisted ? 'true' : 'false' }}">
        @if($isWishlisted)
            <x-icons.heart stroke="2" class="h-6 w-6 text-badge-hot fill-current" />
        @else
            <x-icons.heart stroke="2" class="h-6 w-6 text-muted-text hover:text-badge-hot transition-colors" />
        @endif
    </button>
</div>
