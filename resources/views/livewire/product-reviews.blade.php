<div class="mt-12 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <h3 class="text-xl font-bold mb-6">Customer Reviews</h3>

    @if (session()->has('message'))
        <div class="bg-green-50 text-green-700 p-4 rounded-md mb-6">
            {{ session('message') }}
        </div>
    @endif

    @auth('customer')
        <div class="mb-8 p-6 bg-gray-50 rounded-md">
            <h4 class="font-medium mb-4">Write a Review</h4>
            <form wire:submit.prevent="submitReview">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <div class="flex items-center space-x-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="$set('rating', {{ $i }})" class="focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 {{ $rating >= $i ? 'text-yellow-400' : 'text-gray-300' }} fill-current" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                    @error('rating') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                    <textarea wire:model="comment" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Tell us what you think about this product..."></textarea>
                    @error('comment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Submit Review
                </button>
            </form>
        </div>
    @else
        <div class="mb-8 p-4 bg-gray-50 rounded-md text-sm text-gray-600">
            Please <a href="{{ route('account.login') }}" class="text-blue-600 font-medium hover:underline">login</a> to write a review.
        </div>
    @endauth

    <div class="space-y-6">
        @forelse($reviews as $review)
            <div class="border-b border-gray-100 pb-6 last:border-0">
                <div class="flex items-center mb-2">
                    <div class="flex mr-3">
                        @for($i = 1; $i <= 5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $review->rating >= $i ? 'text-yellow-400' : 'text-gray-300' }} fill-current" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        @endfor
                    </div>
                    <span class="font-medium text-sm text-gray-900">{{ $review->customer->name ?? 'Anonymous' }}</span>
                    <span class="text-xs text-gray-500 ml-3">{{ $review->created_at->format('M d, Y') }}</span>
                    <span class="ml-2 text-xs font-medium text-green-600 bg-green-100 px-2 py-0.5 rounded-full flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Verified Purchase
                    </span>
                </div>
                @if($review->comment)
                    <p class="text-gray-700 text-sm mt-2">{{ $review->comment }}</p>
                @endif
            </div>
        @empty
            <p class="text-gray-500 italic">No reviews yet for this product.</p>
        @endforelse
    </div>
</div>
