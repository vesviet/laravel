# Release Notes — 2026-08-27

## Seller Center (Refactor + Bug Fixes)

### Fixed
- **P0**: `/seller/register` returned 500 (surfaced as 403 by reverse proxy) because `SellerPanelProvider::viteTheme()` referenced a CSS file not registered in `vite.config.js` and the file was a single comment. CI now builds Vite assets + enforces manifest coverage via **ADR-S5**.
- **P0**: Queued listeners (`SendOrderConfirmationEmail`, `SendSellerTelegramNotification`) marked `NotTenantAware` to prevent `CurrentTenantCouldNotBeDeterminedInTenantAwareJob` errors in sync-queue test environments.
- **P0**: `User::canAccessPanel` now correctly differentiates Seller vs Admin panels (with `safePanelId` fallback for unbound panel instances).
- **P0**: `SimpleProductResource` no longer uses `withoutGlobalScopes()` — tenant isolation is preserved (prevents cross-tenant data leak).

### Added
- `App\Services\SellerOrderService` extracted from `ProcessSellerQuickOrderAction` (ADR-S2 boundary).
- `App\Exceptions\SellerActionException` with named constructors for typed errors.
- `App\Actions\CreateSellerProductAction` for seller-side product creation.
- `App\Http\Requests\StoreSellerQuickOrderRequest` for QuickCheckout validation.
- `App\Filament\Seller\Resources\SellerOrderResource` (and 3 Pages) — fills the missing import in `SellerPanelProvider`.
- `SellerProfile::hasCompleteBankInfo()` — single source of truth for VietQR readiness.
- `SellerProfile::generateUniqueSubdomain()` with `RESERVED_SUBDOMAINS` constant.
- `SellerPage::CACHE_KEY_PREFIX` + `cacheKeyFor()` helper.
- i18n: `lang/vi/seller.php` and `lang/en/seller.php` (Telegram notification strings).

### Changed
- `RegisterSellerAction`, `PublishSellerPageAction`, `ProcessSellerQuickOrderAction` now throw `SellerActionException`.
- `PublishSellerPageAction` cache invalidation runs in `finally` (cache always cleared, even on rollback).
- `ProcessSellerQuickOrderAction` dispatches `SellerOrderPlaced` AFTER transaction commit.
- `SimpleProductResource` form surfaces `is_visible`, `status`, `is_purchasable`, `is_featured` toggles.
- `QuickCheckout` Livewire guards cross-tenant product access in `mount()`.
- `ListSellerPages::mount()` uses `$this->redirect()` correctly.

### Architecture
- **ADR-S5** added to `ARCHITECTURE.md` — Vite Manifest Coverage enforcement.
- `User::sellerProfile()` relation added (formal FK).
- `SellerProfile` and `SellerPage` switched to `$fillable` from `$guarded`.

### CI/CD
- **ADR-S5 fitness function** added to `architectural-fitness` job (validates `viteTheme()` references vs `vite.config.js`).
- **Build Vite Assets** step added to `test`, `build`, and `deploy` jobs.
- **Pre-flight manifest check** added to deploy job.

## Test Results

| Suite | Result |
|-------|--------|
| Full Pest suite | **856/856 pass** (4590 assertions) |
| SellerCenterTest | 8/8 pass (21 assertions) |
| AdminRbacTest | 20/20 pass (35 assertions) |
| CheckoutFlowTest | 17/17 pass (37 assertions) |
| AdversarialPromotionConcurrencyStressTest | 7/7 pass (90 assertions) |
| Architectural Fitness Functions (ADR-S2/S3/S5) | All PASS |

## Verification

- Local: `GET http://localhost/seller/register` → 200 OK
- Production (post-deploy): `GET https://demo.tanhdev.com/seller/register` → 200 OK expected
- All sub-tasks ST-01 through ST-21 completed

## Rollback

If issues arise after deploy:
```bash
git revert e8aecd8..7223e4a
# or
git reset --hard fd57732
```

## Next-Sprint Follow-ups (P1)

1. Persist `industry` field (currently collected at registration but not stored) — schema gap.
2. Add `throttle:5,1` to `POST /seller/register` to prevent spam.
3. Add `SellerRegistered` event for audit trail.
