<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->seo_title ?: $page->title }}</title>
    @if($page->seo_description)
        <meta name="description" content="{{ $page->seo_description }}">
    @endif
    <meta property="og:title" content="{{ $page->seo_title ?: $page->title }}">
    @if($page->seo_description)
        <meta property="og:description" content="{{ $page->seo_description }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    @if($page->product?->image_path)
        <meta property="og:image" content="{{ Storage::url($page->product->image_path) }}">
    @endif

    {{-- Facebook Pixel --}}
    @if($page->facebook_pixel_id)
        @php $fbId = preg_replace('/[^A-Za-z0-9._-]/', '', $page->facebook_pixel_id); @endphp
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}
            (window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $fbId }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $fbId }}&ev=PageView&noscript=1"/></noscript>
    @endif

    {{-- TikTok Pixel --}}
    @if($page->tiktok_pixel_id)
        @php $ttId = preg_replace('/[^A-Za-z0-9._-]/', '', $page->tiktok_pixel_id); @endphp
        <script>
            !function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];
            ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];
            ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
            for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
            ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";
            ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};
            var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;
            var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
            ttq.load('{{ $ttId }}');ttq.page();}(window,document,'ttq');
        </script>
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #fff; color: #111827; }
        img { max-width: 100%; height: auto; display: block; }
        input, textarea, select, button { font-family: inherit; }
    </style>

    @livewireStyles
</head>
<body>

{{-- ── Sticky Header ────────────────────────────────────────────────────────── --}}
@if($page->header_logo_url || $page->header_cta_text)
    <header style="position:sticky;top:0;z-index:50;background:rgba(255,255,255,0.95);backdrop-filter:blur(5px);display:flex;justify-content:space-between;align-items:center;padding:10px 20px;border-bottom:1px solid #e5e7eb;">
        <div style="flex:1;">
            @if($page->header_logo_url)
                <img src="{{ $page->header_logo_url }}" alt="{{ $page->title }}" style="max-height:40px;object-fit:contain;">
            @else
                <span style="font-weight:bold;font-size:1.2rem;color:#111827;">{{ $page->title }}</span>
            @endif
        </div>
        @if($page->header_cta_text)
            <button
                onclick="document.getElementById('checkout-form').scrollIntoView({behavior:'smooth'})"
                style="padding:8px 16px;background:#ea580c;color:#fff;font-weight:bold;border:none;border-radius:4px;cursor:pointer;white-space:nowrap;">
                {{ $page->header_cta_text }}
            </button>
        @endif
    </header>
@endif

{{-- ── Main Content ─────────────────────────────────────────────────────────── --}}
<main style="max-width:600px;margin:0 auto;padding:40px 20px;">

    {{-- Fake Viewers Badge --}}
    @if($page->urgency_fake_views > 0)
        <div style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:#fdf2f8;color:#be123c;font-weight:bold;font-size:1.1rem;margin-bottom:15px;border-radius:8px;">
            👁️ Đang có <span style="color:#e11d48;font-size:1.2rem;">{{ $page->urgency_fake_views }}</span> người xem sản phẩm này
        </div>
    @endif

    {{-- Product Image --}}
    @if($page->product?->image_path)
        <div style="margin-bottom:20px;border-radius:8px;overflow:hidden;">
            <img
                src="{{ Storage::url($page->product->image_path) }}"
                alt="{{ $page->seo_title ?: $page->title }}"
                style="width:100%;aspect-ratio:4/5;object-fit:cover;border-radius:8px;">
        </div>
    @endif

    {{-- Title --}}
    <h1 style="font-size:1.4rem;font-weight:900;text-align:center;text-transform:uppercase;margin-bottom:15px;line-height:1.4;">
        {{ $page->seo_title ?: $page->title }}
    </h1>

    {{-- Price Section --}}
    @if($page->product)
        @php
            $price = (float) $page->product->price;
        @endphp
        <div style="display:flex;flex-direction:column;align-items:center;margin-bottom:25px;gap:6px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:1.2rem;color:#4b5563;">Giá bán:</span>
                <span style="font-size:2.2rem;font-weight:900;color:#ea580c;">
                    {{ number_format($price, 0, ',', '.') }}₫
                </span>
            </div>
        </div>
    @endif

    {{-- Feature Bullet Points --}}
    @php $features = $page->featuresList(); @endphp
    @if(count($features) > 0)
        <div style="display:flex;flex-direction:column;gap:12px;padding:0 10px;font-size:1.05rem;color:#374151;margin-bottom:24px;">
            @foreach($features as $feature)
                <div style="display:flex;align-items:flex-start;gap:10px;">
                    <span style="color:#ea580c;font-size:1.1rem;flex-shrink:0;">✓</span>
                    <span>{{ $feature }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Urgency Countdown Timer --}}
    @if($page->hasActiveUrgency())
        <div
            x-data="{
                end: {{ $page->urgency_end_time->timestamp * 1000 }},
                hours: '00', mins: '00', secs: '00', expired: false,
                init() {
                    const tick = () => {
                        const diff = this.end - Date.now();
                        if (diff <= 0) { this.expired = true; return; }
                        const h = Math.floor(diff / 3600000);
                        const m = Math.floor((diff % 3600000) / 60000);
                        const s = Math.floor((diff % 60000) / 1000);
                        this.hours = String(h).padStart(2, '0');
                        this.mins  = String(m).padStart(2, '0');
                        this.secs  = String(s).padStart(2, '0');
                    };
                    tick();
                    setInterval(tick, 1000);
                }
            }"
            x-show="!expired"
            style="text-align:center;margin-bottom:20px;padding:12px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;">
            <p style="color:#991b1b;font-weight:bold;font-size:1rem;margin:0 0 8px;">⏳ Ưu đãi kết thúc sau:</p>
            <div style="display:flex;justify-content:center;gap:8px;font-size:2rem;font-weight:900;color:#dc2626;font-variant-numeric:tabular-nums;">
                <span x-text="hours"></span><span>:</span><span x-text="mins"></span><span>:</span><span x-text="secs"></span>
            </div>
        </div>
    @endif

    {{-- Livewire Order Form --}}
    <div style="background:#f9fafb;padding:30px;border-radius:12px;box-shadow:0 4px 6px rgba(0,0,0,0.05);">
        @livewire('landing-order-form', ['landingPage' => $page])
    </div>

    {{-- Footer Content (Markdown) --}}
    @if($page->footer_content)
        <footer style="margin-top:40px;padding-top:20px;border-top:1px solid #e5e7eb;font-size:0.9rem;color:#4b5563;line-height:1.6;">
            {!! \Illuminate\Support\Str::markdown($page->footer_content) !!}
        </footer>
    @endif
</main>

{{-- Pixel Purchase Fire (dispatched by Livewire) --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('order-placed', (payload) => {
            const data = Array.isArray(payload) ? payload[0] : payload;
            if (window.fbq && data.facebook_pixel_id) {
                fbq('track', 'Purchase', { value: data.value, currency: data.currency || 'VND' });
            }
            if (window.ttq && data.tiktok_pixel_id) {
                ttq.track('CompletePayment', { value: data.value, currency: data.currency || 'VND' });
            }
        });
    });
</script>

@livewireScripts

{{-- Alpine.js for countdown --}}
@if($page->hasActiveUrgency())
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endif

</body>
</html>
