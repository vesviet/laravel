# Architecture Decision Records

This file is the authoritative source for all ADRs in this project.
ADRs are numbered by domain prefix: **S** (Structural), **B** (Business Domain).

---

## ADR-S2 — Transaction Boundary Standard

> Adopted: 2026-08-16 | Status: **Active** | Enforcement: CI (`.github/workflows/ci-cd.yml`)

### Rule

**Only Action classes own `DB::transaction()` boundaries.**

Service classes (`OrderService`, `InventoryService`, etc.) and Model methods MUST NOT open their own `DB::transaction()`. They assume they are already called within an active transaction provided by the caller.

### Rationale

Nested `DB::transaction()` calls in Laravel are silently allowed via MySQL savepoints, but they create two problems:

1. **Logical split across multiple transactions** — before this ADR, `InventoryService` and `OrderService` each had their own `DB::transaction()`. This means stock deduction and order creation could commit independently — a P0 data consistency risk.
2. **False sense of atomicity** — code looked transactional but wasn't end-to-end atomic.

### Pattern

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

### MUST / MUST NOT

| Layer | `DB::transaction()` | Notes |
|-------|---------------------|-------|
| **Action** | ✅ MUST own | One transaction per Action |
| **Service** | ❌ MUST NOT | Document with `@throws` + IMPORTANT docblock |
| **Model** | ❌ MUST NOT | Except in model-specific utility methods that are never called from Actions |
| **Livewire component** | ❌ MUST NOT | Delegate to Actions |
| **Controller** | ❌ MUST NOT | Delegate to Actions |

### Enforcement

**CI-enforced (ADR-S4)**: The CI fitness function blocks merges where `DB::transaction()` appears outside `app/Actions/`. See `.github/workflows/ci-cd.yml` → `architectural-fitness` job.

Exception annotation: `// ADR-S2: exception allowed` suppresses the CI check for legitimate edge cases.

Services that require a caller transaction MUST include this docblock:

```php
/**
 * IMPORTANT: This method MUST be called within an active DB::transaction().
 * Transaction ownership belongs to the calling Action.
 */
```

### Migration History

- **R1 (2026-08-16):** Removed `DB::transaction()` from `LandingOrderForm` → moved to `ProcessLandingOrderAction`.
- **R2 (2026-08-16):** Removed `DB::transaction()` from `InventoryService::deductStock()` and `restoreStock()`.
- **R3 (2026-08-16):** Removed `DB::transaction()` from `OrderService::createOrder()`.
- **R4 (2026-08-16):** `ProcessCheckoutAction` coupon lock moved inside its `DB::transaction()`.

---

## ADR-S3 — Structural Trust Zone Map

> Adopted: 2026-08-21 | Status: **Active** | Enforcement: CI (`.github/workflows/ci-cd.yml`)

### Context

B-01/B-03 findings revealed `.env` with real `APP_KEY` and `database.sqlite` (569KB, containing real PII/business data) were committed to the repository. This ADR defines explicit structural trust zones to prevent recurrence.

### Structural Trust Zones

| Zone | Files / Paths | Rule |
|------|--------------|------|
| **Restricted** | `.env`, `database/*.sqlite`, `config/auth.php` (production overrides), payment-adjacent code in `app/Actions/ProcessCheckout*` | Never commit real credential values. CI scanner blocks commits. Requires human architectural review before AI-assisted modification. |
| **Standard** | `app/Actions/`, `app/Services/`, `database/migrations/`, `app/Http/Controllers/` | PR review required. AI-generated code allowed with standard review gate. |
| **Low-risk** | `resources/views/`, `tests/`, `public/`, `lang/`, `database/seeders/` | Standard review. AI-generated code allowed. |

### Enforcement

CI fitness function (`ADR-S3 Secret Scanner` step in `architectural-fitness` job):
- Fails if `.env` is tracked by git
- Fails if `database/*.sqlite` is tracked by git
- Fails if `APP_KEY=base64:` appears in any tracked non-example file
- Fails if hardcoded API tokens appear in PHP source files

### Immediate Actions (from B-01/B-03)

```bash
# Remove committed secrets from git tracking
git rm --cached .env database/database.sqlite
git commit -m "fix(security): remove .env and sqlite from git tracking [B-01/B-03]"

# Purge from history (run once, coordinate with all team members)
git filter-repo --path .env --path database/database.sqlite --invert-paths

# Rotate APP_KEY
php artisan key:generate
```

---

## ADR-S4 — ADR-S2 Fitness Function (CI Enforcement)

> Adopted: 2026-08-21 | Status: **Active** | References ADR-S2

ADR-S2 was documented-only (advisory). ADR-S4 makes it executable: the `architectural-fitness` CI job fails any PR where `DB::transaction()` appears outside `app/Actions/`.

This converts ADR-S2 from governance theater into enforced constraint.

---

## ADR-S5 — Vite Manifest Coverage (CI Enforcement)

> Adopted: 2026-08-27 | Status: **Active** | Trigger: `/seller/register` 500/403 incident

### Context

On 2026-08-27, `GET /seller/register` returned 500 (surfaced as 403 by reverse proxy) because `SellerPanelProvider::viteTheme('resources/css/filament/seller/theme.css')` referenced a CSS file that was never registered in `vite.config.js` `input` array. The file was also a single-line comment, so even if registered Vite would strip it from the bundle.

### Original Rule (reverted on 2026-08-27)

**Every CSS path passed to `viteTheme()` MUST be registered in `vite.config.js` `input` array, and the file MUST contain real content (no single-comment placeholders).**

This was the rule we initially adopted, but the dependency on Vite manifest correctness for runtime Filament rendering was deemed too fragile: a self-hosted runner with stale `node_modules` or `package-lock.json` could produce a manifest missing the entry and the error would only surface at runtime as 500/403 in production.

### Revised Rule (current)

**Do NOT use `viteTheme()` for Filament seller themes.** Use a FilamentView render hook pointing to a pre-built, committed static CSS file in `public/css/`:

```php
FilamentView::registerRenderHook(
    PanelsRenderHook::HEAD_END,
    fn (): string => '<link rel="stylesheet" href="/css/seller-theme.css" />',
);
```

The static file:
- Is committed to `public/css/`
- Is served directly by nginx (no Vite, no manifest lookup, no build step)
- Eliminates the runtime/manifest coupling entirely

The original `resources/css/filament/seller/theme.css` is retained as a Tailwind source-of-truth for future regenerations but is NOT used at runtime.

### Enforcement (CI)

- **ADR-S5 fitness function** still in CI: greps for any new `viteTheme(` call and fails the build if found. Migration to direct static-CSS rendering is the only approved approach.
- **No `npm run build` step required** in the deploy job (was previously added but reverted when this ADR was updated).

---

## ADR-B1 — Customer Tier Domain Ownership

> Adopted: 2026-08-21 | Status: **Active**

### Context (I-04)

`PromotionRule::matchesCustomerTier()` was computing customer tier directly:
- Calling `Customer::getAttributes()` for raw DB values
- Calling `Customer::membership_tier` accessor for computed value
- Using `str_contains($customerTier, 'vip')` — allows 'vip_bronze' to match VIP Gold tier (abuse vector)
- `max($rawSpent, $computedSpent)` — inconsistency between DB and computed values

This is a **domain boundary violation**: the Promotion domain was computing Customer domain data.

### Decision

**Customer Tier computation belongs exclusively to the Customer domain.**

- `App\Enums\CustomerTier` — value object (enum) for tier identity
- `App\Services\CustomerTierResolver` — single source of truth for tier resolution
- `App\Models\PromotionRule::matchesCustomerTier()` — consumes `CustomerTier` enum via `CustomerTierResolver`, never computes tier directly

### Boundary

```
Promotion Domain             Customer Domain
PromotionRule                CustomerTierResolver
  matchesCustomerTier()  →   resolve(Customer) → CustomerTier enum
                             isFirstTime(Customer, email) → bool
```

### Tier Thresholds (VND)

| Tier | Min Lifetime Spend | Label (VI) |
|------|--------------------|-----------|
| Bronze | 0 | Thành Viên Mới |
| Silver | 5,000,000 | Thành Viên Thân Thiết |
| Gold | 20,000,000 | VIP Diamond |
| Platinum | 50,000,000 | Platinum |

---

## ADR-B2 — Legacy Combo Discount Migration

> Adopted: 2026-08-21 | Status: **In Progress** (Phase 1)

### Context (I-05)

A 5% combo discount (2+ non-flash-sale items) was hardcoded as magic number `0.05` in `App\Services\PromotionEngine::calculateDiscount()`. No origin, no auditability, no configurability.

### Decision

Migrate the rule to a `PromotionRule` DB record. Three-phase plan:

**Phase 1 (current):** Seed `[LEGACY] Combo 2+ Sản Phẩm Giảm 5%` rule with `is_active = false`. Legacy code path still active in `PromotionEngine`.

**Phase 2 (next sprint):** Activate the seeded rule. Run checkout tests to verify parity with legacy behavior.

**Phase 3 (after Phase 2):** Remove hardcoded `0.05` from `PromotionEngine::calculateDiscount()` (lines ~72-73 and ~105-106).

### Source of Truth

After migration: the `PromotionRule` DB record named `[LEGACY] Combo 2+ Sản Phẩm Giảm 5%` is the authoritative definition of this promotion.

---

## Intentional Design Decisions (not ADRs)

### Session-backed Cart (not a bug)

Cart state is stored in PHP Session, not a database table or Redis. This is intentional for the current single-server cPanel deployment model. Horizontal scaling would require Redis-backed sessions — see `config/session.php`. If scaling plans change, create a formal ADR before migrating.

### COD-Only Checkout (no payment gateway)

`composer.json` has no Stripe/PayPal package. All orders use Cash on Delivery (`PAYMENT_METHOD=cod`). The `stripe_customer_id` field on `Customer` is a placeholder for a future integration — it is intentionally excluded from `$fillable` until an integration ADR is approved.

### Seller Center: Shared-Database Multi-Tenancy (not `UsesTenantConnection`)

The `SellerProfile` model extends `Spatie\Multitenancy\Models\Tenant` to participate in Spatie's Multitenancy package lifecycle (tenant resolution, `makeCurrent()`, `SubdomainTenantFinder`). However, **`UsesTenantConnection` is intentionally NOT used**.

**Why:** `UsesTenantConnection` switches the DB connection to a per-tenant database or schema. This project uses **shared-database multi-tenancy** — all sellers share the same tables, isolated by `seller_id` foreign keys and a `BelongsToSeller` global scope trait.

**Pattern used:**

```
SellerProfile (extends Tenant) — resolves via SubdomainTenantFinder
  → makeCurrent() sets the "active tenant" context in memory
  → BelongsToSeller trait applies a global scope: WHERE seller_id = $activeTenantId
  → Filament::getTenant() is the authoritative accessor for the active tenant in UI code
```

**Consequence for code authors:**
- Any `Product`, `Order`, `SellerPage` query inside the Seller Panel is automatically scoped by `seller_id` via the `BelongsToSeller` global scope — no manual `where('seller_id', ...)` needed.
- For cross-tenant queries (e.g. admin panel, background jobs), use `->withoutGlobalScopes()` explicitly to bypass the scope.
- Never rely on `auth()->user()->sellerProfile` for write operations — use `Filament::getTenant()`. The Eloquent relation traversal does not guarantee it matches the active Filament tenant (P0-01).

**If you see `UsesTenantConnection`** in a future PR: that is a **P0 architectural violation** requiring explicit ADR approval before merging.