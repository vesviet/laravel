<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\FlashSale;
use Carbon\Carbon;

class FlashSaleBanner extends Component
{
    public $flashSale;
    public $endTime;

    public function mount()
    {
        $this->flashSale = FlashSale::where('status', 'active')
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->latest()
            ->first();

        if ($this->flashSale) {
            $this->endTime = Carbon::parse($this->flashSale->end_time)->timestamp * 1000;
        }
    }

    public function render()
    {
        return view('livewire.flash-sale-banner');
    }
}
