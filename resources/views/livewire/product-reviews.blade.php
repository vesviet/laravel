<div class="mt-12 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <h3 class="text-xl font-bold mb-6">Đánh giá từ khách hàng</h3>

    @if (session()->has('message'))
        <div class="bg-green-50 text-green-700 p-4 rounded-md mb-6" role="status" aria-live="polite">
            {{ session('message') }}
        </div>
    @endif

    {{-- Rating Summary — only show when there is at least 1 approved review --}}
    @if($reviews->count() > 0)
        @php
            $avgRating  = round($reviews->avg('rating'), 1);
            $totalCount = $reviews->count();
            $distribution = collect(range(5, 1))->mapWithKeys(fn($i) => [
                $i => $reviews->where('rating', $i)->count()
            ]);
        @endphp
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-100">
            <div class="flex items-center gap-8 flex-wrap">
                {{-- Average score --}}
                <div class="text-center">
                    <p class="text-5xl font-bold text-gray-900">{{ $avgRating }}</p>
                    <div class="flex justify-center mt-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5 {{ $avgRating >= $i ? 'text-yellow-400' : ($avgRating >= $i - 0.5 ? 'text-yellow-300' : 'text-gray-300') }} fill-current"
                                 viewBox="0 0 24 24">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $totalCount }} đánh giá</p>
                </div>

                {{-- Distribution bars --}}
                <div class="flex-1 min-w-[160px]">
                    @foreach($distribution as $stars => $count)
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs text-gray-600 w-4 text-right">{{ $stars }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-yellow-400 fill-current" viewBox="0 0 24 24">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div
                                    class="bg-yellow-400 h-2 rounded-full transition-all duration-300"
                                    style="width: {{ $totalCount > 0 ? round($count / $totalCount * 100) : 0 }}%"
                                ></div>
                            </div>
                            <span class="text-xs text-gray-500 w-4">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Write Review Form --}}
    @auth('customer')
        <div class="mb-8 p-6 bg-gray-50 rounded-md">
            <h4 class="font-medium mb-4">Viết đánh giá</h4>
            <form wire:submit.prevent="submitReview">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số sao</label>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="$set('rating', {{ $i }})" class="focus:outline-none focus:ring-2 focus:ring-yellow-400 rounded" aria-label="{{ $i }} sao">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-8 w-8 {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }} fill-current transition-colors"
                                     viewBox="0 0 24 24">
                                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    @error('rating') <span class="text-red-500 text-xs" role="alert">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="review-comment" class="block text-sm font-medium text-gray-700 mb-2">Nhận xét</label>
                    <textarea id="review-comment" wire:model="comment" rows="4"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 border px-3 py-2"
                              placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."></textarea>
                    @error('comment') <span class="text-red-500 text-xs" role="alert">{{ $message }}</span> @enderror
                </div>

                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Gửi đánh giá
                </button>
            </form>
        </div>
    @else
        <div class="mb-8 p-4 bg-gray-50 rounded-md text-sm text-gray-600">
            Vui lòng <a href="{{ route('account.login') }}" class="text-blue-600 font-medium hover:underline">đăng nhập</a> để viết đánh giá.
        </div>
    @endauth

    {{-- Review List --}}
    <div class="space-y-6">
        @forelse($reviews as $review)
            <div class="border-b border-gray-100 pb-6 last:border-0">
                <div class="flex items-center flex-wrap gap-2 mb-2">
                    {{-- Star rating --}}
                    <div class="flex mr-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-4 w-4 {{ $review->rating >= $i ? 'text-yellow-400' : 'text-gray-300' }} fill-current"
                                 viewBox="0 0 24 24">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="font-medium text-sm text-gray-900">{{ $review->customer->name ?? 'Ẩn danh' }}</span>
                    <span class="text-xs text-gray-500">{{ $review->created_at->format('d/m/Y') }}</span>
                    <span class="ml-1 text-xs font-medium text-green-600 bg-green-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Đã xác minh
                    </span>
                </div>
                @if($review->comment)
                    <p class="text-gray-700 text-sm mt-2">{{ $review->comment }}</p>
                @endif
            </div>
        @empty
            <p class="text-gray-500 italic">Chưa có đánh giá nào cho sản phẩm này.</p>
        @endforelse
    </div>
</div>
