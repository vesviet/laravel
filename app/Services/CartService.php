<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function getItems(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function addItem(int $productId, string $name, float $price, int $quantity = 1, ?string $image = null): void
    {
        $cart = $this->getItems();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $image,
            ];
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $cart = $this->getItems();

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] = $quantity;
            }
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function removeItem(int $productId): void
    {
        $cart = $this->getItems();
        unset($cart[$productId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    public function getFormattedTotal(): string
    {
        return number_format($this->getTotal(), 0, ',', '.') . '₫';
    }

    public function getItemCount(): int
    {
        $count = 0;
        foreach ($this->getItems() as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    public function isEmpty(): bool
    {
        return empty($this->getItems());
    }
}
