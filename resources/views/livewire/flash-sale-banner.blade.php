<div>
@if($flashSale)
    <div class="bg-badge-hot text-white p-3 shadow-md"
         x-data="{ 
            endTime: {{ $endTime }},
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            isExpired: false,
            updateTimer() {
                const now = new Date().getTime();
                const distance = this.endTime - now;
                
                if (distance < 0) {
                    this.isExpired = true;
                    this.days = '00';
                    this.hours = '00';
                    this.minutes = '00';
                    this.seconds = '00';
                    return;
                }
                
                this.days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
            }
         }"
         x-init="
            updateTimer();
            setInterval(() => updateTimer(), 1000);
         "
         x-show="!isExpired"
         x-cloak>
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-3 mb-2 sm:mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="font-bold text-lg uppercase tracking-wider">{{ $flashSale->name }}</span>
            </div>
            
            <div class="flex items-center space-x-2 font-mono text-lg font-bold bg-white text-badge-hot px-4 py-1 rounded-md shadow-inner">
                <span>Kết thúc sau:</span>
                <span x-text="days"></span>d
                <span x-text="hours"></span>h
                <span x-text="minutes"></span>m
                <span x-text="seconds"></span>s
            </div>
        </div>
    </div>
@endif
</div>
