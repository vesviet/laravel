# Transaction Boundary Standard

> **ADR-S2** — Adopted 2026-08-16

## Rule

**Only Action classes own `DB::transaction()` boundaries.**

Service classes (`OrderService`, `InventoryService`, etc.) and Model methods MUST NOT open their own `DB::transaction()`. They assume they are already called within an active transaction provided by the caller.

## Rationale

Nested `DB::transaction()` calls in Laravel are silently allowed via MySQL savepoints, but they create two problems:

1. **Logical split across multiple transactions** — before this ADR, `InventoryService` and `OrderService` each had their own `DB::transaction()`. This means stock deduction and order creation could commit independently — a P0 data consistency risk.
2. **False sense of atomicity** — code looked transactional but wasn't end-to-end atomic.

## Pattern

```
CheckoutController
  └─ ProcessCheckoutAction::execute()
       └─ DB::transaction() ← single transaction boundary, owned here
            ├─ Coupon::lockForUpdate()       ← inside
            ├─ OrderService::createOrder()   ← inside (no own transaction)
            │    └─ OrderItem::create()      ← inside
            └─ InventoryService::deductStock() ← inside (no own transaction)
                 └─ Product::lockForUpdate() ← inside
```

## MUST / MUST NOT

| Layer | `DB::transaction()` | Notes |
|-------|---------------------|-------|
| **Action** | ✅ MUST own | One transaction per Action |
| **Service** | ❌ MUST NOT | Document with `@throws` + IMPORTANT docblock |
| **Model** | ❌ MUST NOT | Except in model-specific utility methods that are never called from Actions |
| **Livewire component** | ❌ MUST NOT | Delegate to Actions |
| **Controller** | ❌ MUST NOT | Delegate to Actions |

## Enforcement

When reviewing a PR:
- Any `DB::transaction()` outside of `app/Actions/` is a **P0 blocking issue**.
- Services that require a caller transaction MUST include this docblock:

```php
/**
 * IMPORTANT: This method MUST be called within an active DB::transaction().
 * Transaction ownership belongs to the calling Action.
 */
```

## Migration History

- **R2 (2026-08-16):** Removed `DB::transaction()` from `InventoryService::deductStock()` and `restoreStock()`.
- **R3 (2026-08-16):** Removed `DB::transaction()` from `OrderService::createOrder()`.
- **R1 (2026-08-16):** Removed `DB::transaction()` from `LandingOrderForm` → moved to `ProcessLandingOrderAction`.
- **R4 (2026-08-16):** `ProcessCheckoutAction` coupon lock moved inside its `DB::transaction()`.


https://demo.uix.store/sober-furniture/home-v12/?header_layout=v2&header_wrapper=wrapped&footer_widgets_layout=2-columns&footer_content_enable=1&footer_instagram=1&utm_source=landing