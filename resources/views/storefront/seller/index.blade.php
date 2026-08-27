<!DOCTYPE html>
<html lang="vi" class="{{ data_get($page->theme_config, 'mode', 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant->shop_name }}</title>
    
    <!-- Fonts -->
    @php
        $font = data_get($page->theme_config, 'font', 'Inter');
        $fontFamily = str_replace(' ', '+', $font);
    @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ $fontFamily }}:wght@400;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: {{ data_get($page->theme_config, 'primary_color', '#3b82f6') }};
        }
        body {
            font-family: '{{ $font }}', sans-serif;
            background-color: {{ data_get($page->theme_config, 'mode', 'light') === 'dark' ? '#111827' : '#f9fafb' }};
            color: {{ data_get($page->theme_config, 'mode', 'light') === 'dark' ? '#f9fafb' : '#111827' }};
        }
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
    </style>
</head>
<body class="antialiased min-h-screen">
    
    <!-- Top Nav / Header -->
    <header class="w-full py-4 shadow-sm bg-white dark:bg-gray-800 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 flex justify-between items-center">
            <h1 class="font-bold text-xl">{{ $tenant->shop_name }}</h1>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8 space-y-12">
        
        @if(is_array($page->blocks))
            @foreach($page->blocks as $block)
                
                @if($block['type'] === 'hero')
                    <section class="relative rounded-2xl overflow-hidden bg-gray-900 text-white min-h-[300px] flex items-center justify-center text-center p-8">
                        @if(data_get($block, 'data.background_image'))
                            <div class="absolute inset-0">
                                <img src="/storage/{{ $block['data']['background_image'] }}" class="w-full h-full object-cover opacity-50" alt="Banner">
                            </div>
                        @endif
                        <div class="relative z-10 space-y-4">
                            <h2 class="text-4xl md:text-5xl font-bold">{{ data_get($block, 'data.title') }}</h2>
                            @if(data_get($block, 'data.subtitle'))
                                <p class="text-xl opacity-90">{{ data_get($block, 'data.subtitle') }}</p>
                            @endif
                        </div>
                    </section>
                @endif
                
                @if($block['type'] === 'products')
                    <section>
                        <h3 class="text-2xl font-bold mb-6 border-b-2 border-primary inline-block pb-2">{{ data_get($block, 'data.title', 'Sản phẩm nổi bật') }}</h3>
                        
                        <!-- Fetch products for this tenant -->
                        @php
                            $limit = data_get($block, 'data.limit', 8);
                            $products = \App\Models\Product::where('seller_id', $tenant->id)
                                ->where('is_visible', true)
                                ->where('status', 'published')
                                ->limit($limit)
                                ->get();
                        @endphp
                        
                        @if($products->isEmpty())
                            <p class="text-gray-500 italic">Chưa có sản phẩm nào.</p>
                        @else
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($products as $product)
                                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                                        @if($product->image_path)
                                            <img src="/storage/{{ $product->image_path }}" class="w-full aspect-square object-cover" alt="{{ $product->name }}">
                                        @else
                                            <div class="w-full aspect-square bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400">No Image</div>
                                        @endif
                                        <div class="p-4">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $product->name }}</h4>
                                            <p class="text-primary font-bold mt-2">{{ number_format($product->price) }} đ</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif
                
                @if($block['type'] === 'media')
                    <section class="rounded-2xl overflow-hidden shadow-sm">
                        @if(data_get($block, 'data.youtube_url'))
                            <!-- Minimal youtube embed placeholder, ideally parse to embed URL -->
                            <div class="aspect-video bg-gray-800 text-white flex items-center justify-center">
                                <a href="{{ data_get($block, 'data.youtube_url') }}" target="_blank" class="flex items-center gap-2 hover:text-primary transition-colors">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                                    Xem Video
                                </a>
                            </div>
                        @elseif(data_get($block, 'data.image'))
                            <img src="/storage/{{ $block['data']['image'] }}" class="w-full h-auto" alt="Media">
                        @endif
                    </section>
                @endif
                
                @if($block['type'] === 'faq' && is_array(data_get($block, 'data.questions')))
                    <section>
                        <h3 class="text-2xl font-bold mb-6">Câu hỏi thường gặp</h3>
                        <div class="space-y-4">
                            @foreach($block['data']['questions'] as $q)
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                                    <h4 class="font-bold text-lg mb-2">{{ $q['q'] }}</h4>
                                    <p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $q['a'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
                
                @if($block['type'] === 'socials')
                    <section class="flex justify-center gap-6 pt-8 border-t border-gray-200 dark:border-gray-700">
                        @if(data_get($block, 'data.facebook'))
                            <a href="{{ $block['data']['facebook'] }}" target="_blank" class="text-gray-400 hover:text-blue-600 transition-colors">Facebook</a>
                        @endif
                        @if(data_get($block, 'data.instagram'))
                            <a href="{{ $block['data']['instagram'] }}" target="_blank" class="text-gray-400 hover:text-pink-600 transition-colors">Instagram</a>
                        @endif
                        @if(data_get($block, 'data.tiktok'))
                            <a href="{{ $block['data']['tiktok'] }}" target="_blank" class="text-gray-400 hover:text-black dark:hover:text-white transition-colors">TikTok</a>
                        @endif
                    </section>
                @endif
                
            @endforeach
        @endif
        
    </main>
    
    <footer class="py-8 text-center text-gray-500 text-sm">
        <p>&copy; {{ date('Y') }} {{ $tenant->shop_name }}. All rights reserved.</p>
    </footer>
    
</body>
</html>
