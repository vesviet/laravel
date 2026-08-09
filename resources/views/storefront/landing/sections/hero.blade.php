<div class="bg-white">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
            {{ $data['title'] ?? 'Welcome' }}
        </h1>
        <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
            {{ $data['subtitle'] ?? '' }}
        </p>
        @if(!empty($data['button_text']) && !empty($data['button_link']))
        <div class="mt-8 flex justify-center">
            <div class="inline-flex rounded-md shadow">
                <a href="{{ $data['button_link'] }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    {{ $data['button_text'] }}
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
