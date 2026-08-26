<?php

namespace App\Livewire\Seller;

use App\Actions\ProcessSellerQuickOrderAction;
use App\Models\Product;
use Spatie\Multitenancy\Models\Tenant;
use Livewire\Component;

class QuickCheckout extends Component
{
    public $product;
    public $quantity = 1;
    public $customer_name;
    public $phone;
    public $address;
    public $payment_method = 'cod';
    public $notes;
    
    public $orderComplete = false;
    public $qrUrl = null;
    public $orderNumber = null;

    protected $rules = [
        'customer_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'quantity' => 'required|integer|min:1',
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function submit(ProcessSellerQuickOrderAction $action)
    {
        $this->validate();

        $seller = Tenant::current();
        
        $order = $action->execute($seller, [
            'product_id' => $this->product->id,
            'quantity' => $this->quantity,
            'customer_name' => $this->customer_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
        ]);

        $this->orderComplete = true;
        $this->orderNumber = $order->order_number;

        if ($this->payment_method === 'vietqr' && $seller->bank_code && $seller->bank_account_no) {
            $amount = $order->total_amount;
            $info = urlencode("Thanh toan don hang {$this->orderNumber}");
            // Use VietQR Pro/Img API
            $this->qrUrl = "https://img.vietqr.io/image/{$seller->bank_code}-{$seller->bank_account_no}-compact2.png?amount={$amount}&addInfo={$info}&accountName=" . urlencode($seller->bank_account_name);
        }
    }

    public function render()
    {
        return view('livewire.seller.quick-checkout')->layout('layouts.seller-guest');
    }
}
