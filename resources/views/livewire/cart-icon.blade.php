<div>
    <button wire:click="openCart" style="position:relative;background:none;border:none;cursor:pointer;padding:0.5rem;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--color-navy);">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
        </svg>
        @if($count > 0)
            <span style="position:absolute;top:0;right:0;background:var(--color-teal);color:white;font-size:0.625rem;font-weight:700;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                {{ $count }}
            </span>
        @endif
    </button>
</div>
