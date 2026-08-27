<?php

namespace App\Livewire\Seller;

use App\Actions\ProcessSellerQuickOrderAction;
use App\Exceptions\SellerActionException;
use App\Models\Product;
use Filament\Notifications\Notification;
use Livewire\Component;
use Spatie\Multitenancy\Models\Tenant;

class QuickCheckout extends Component
{
    public Product $product;

    public int $quantity = 1;

    public string $customer_name = '';

    public string $phone = '';

    public string $address = '';

    public string $payment_method = 'cod';

    public ?string $notes = null;

    public bool $orderComplete = false;

    public ?string $qrUrl = null;

    public ?string $orderNumber = null;

    public function mount(Product $product)
    {
        $this->guardCrossTenantAccess($product);
        $this->product = $product;
    }

    public function submit(ProcessSellerQuickOrderAction $action)
    {
        $rules = [
            'quantity' => ['required', 'integer', 'min:1'],
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'payment_method' => ['nullable', 'in:cod,vietqr'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        $validated = $this->validate($rules);
        $data = array_merge($validated, ['product_id' => $this->product->id]);

        $seller = Tenant::current();

        if (! $seller) {
            $this->dispatch('notify', type: 'danger', message: 'Không xác định được gian hàng.');

            return;
        }

        try {
            $order = $action->execute($seller, $data);
        } catch (SellerActionException $e) {
            Notification::make()
                ->title('Đặt hàng thất bại')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        $this->orderComplete = true;
        $this->orderNumber = $order->order_number;

        if ($this->payment_method === 'vietqr' && $seller->hasCompleteBankInfo()) {
            $info = urlencode("Thanh toan don hang {$this->orderNumber}");
            $this->qrUrl = sprintf(
                'https://img.vietqr.io/image/%s-%s-compact2.png?amount=%d&addInfo=%s&accountName=%s',
                $seller->bank_code,
                $seller->bank_account_no,
                $order->total_amount,
                $info,
                urlencode($seller->bank_account_name),
            );
        } elseif ($this->payment_method === 'vietqr' && ! $seller->hasCompleteBankInfo()) {
            Notification::make()
                ->title('Thông tin ngân hàng chưa đầy đủ')
                ->body('Vui lòng liên hệ shop để được hỗ trợ thanh toán chuyển khoản.')
                ->warning()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.seller.quick-checkout')->layout('layouts.seller-guest');
    }

    private function guardCrossTenantAccess(Product $product): void
    {
        $currentTenantId = Tenant::current()?->id;

        if ($currentTenantId && $product->seller_id !== $currentTenantId) {
            abort(403, 'Sản phẩm không thuộc gian hàng hiện tại.');
        }
    }
}
