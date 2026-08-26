<div class="flex justify-center p-4">
    <!-- Phone Mockup Frame -->
    <div class="relative mx-auto border-gray-800 dark:border-gray-800 bg-gray-800 border-[14px] rounded-[2.5rem] h-[600px] w-[300px] shadow-xl">
        <div class="w-[148px] h-[18px] bg-gray-800 top-0 rounded-b-[1rem] left-1/2 -translate-x-1/2 absolute"></div>
        <div class="h-[46px] w-[3px] bg-gray-800 absolute -left-[17px] top-[124px] rounded-l-lg"></div>
        <div class="h-[46px] w-[3px] bg-gray-800 absolute -left-[17px] top-[178px] rounded-l-lg"></div>
        <div class="h-[64px] w-[3px] bg-gray-800 absolute -right-[17px] top-[142px] rounded-r-lg"></div>
        
        <div class="rounded-[2rem] overflow-hidden w-[272px] h-[572px] bg-white dark:bg-gray-900 relative">
            <!-- Simulated Livewire Preview Iframe or Component -->
            <iframe 
                src="/seller/preview/page" 
                class="w-full h-full border-0"
                id="seller-preview-frame"
            ></iframe>
        </div>
    </div>
</div>

<script>
    // Refresh iframe when form state is saved or debounced (simplified approach)
    document.addEventListener('livewire:initialized', () => {
        Livewire.hook('commit', ({ component, succeed }) => {
            succeed(() => {
                if (component.name === 'filament.seller.resources.seller-page-resource.pages.edit-seller-page') {
                    // Refresh iframe on any changes (or specifically when 'data' updates)
                    const iframe = document.getElementById('seller-preview-frame');
                    if (iframe) {
                        // Reload iframe to fetch latest draft data from cache/session if needed
                        // iframe.src = iframe.src;
                    }
                }
            })
        })
    })
</script>
