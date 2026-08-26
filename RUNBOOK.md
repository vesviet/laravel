# Operational Runbook

On-call guide for common failure scenarios. Last updated: 2026-08-21.

---

## Checkout Failure (Orders Not Being Created)

**Symptoms:** Users report completing checkout but order not created. `/checkout` returns error page or `500`.

**Immediate triage:**

```bash
# 1. Check application logs
docker compose -f docker-compose.prod.yml exec app tail -n 100 storage/logs/laravel.log | grep -i "checkout\|exception"

# 2. Check queue worker status
docker compose -f docker-compose.prod.yml exec app php artisan queue:monitor

# 3. Check DB connectivity
docker compose -f docker-compose.prod.yml exec app php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';"
```

**Common causes + fixes:**

| Symptom in log | Cause | Fix |
|---------------|-------|-----|
| `SQLSTATE[40001]: Deadlock found` | Concurrent checkout for same product | Retry is automatic (Laravel retry mechanism). If persistent: check `lockForUpdate` key ordering in `ProcessCheckoutAction`. |
| `Stock insufficient` | Race condition, stock was 0 | Expected behavior — no action needed. User gets error message. |
| `Coupon not applicable` | Coupon expired mid-session | Expected behavior — user must remove coupon. |
| `DB::transaction() rolled back` | Any exception inside action | Check full stack trace in log. Look for `Checkout failed` log entry. |
| `GoshipService: API error` | Goship API down | See Goship API Unavailability section below. |

---

## Queue Worker Down

**Symptoms:** Order confirmation emails not sent. Abandoned cart reminders not firing. `orders` table has `status=pending` orders stuck.

**Check:**

```bash
# Check if queue worker is running
docker compose -f docker-compose.prod.yml ps

# View queue worker logs
docker compose -f docker-compose.prod.yml logs worker --tail=50

# Check pending jobs
docker compose -f docker-compose.prod.yml exec app php artisan tinker --execute="echo \DB::table('jobs')->count().' pending jobs';"

# Check failed jobs
docker compose -f docker-compose.prod.yml exec app php artisan queue:failed
```

**Fix:**

```bash
# Restart queue worker
docker compose -f docker-compose.prod.yml restart worker

# Retry failed jobs
docker compose -f docker-compose.prod.yml exec app php artisan queue:retry all

# Flush all failed jobs (last resort)
docker compose -f docker-compose.prod.yml exec app php artisan queue:flush
```

---

## Goship API Unavailability

**Symptoms:** Checkout shipping fee shows ₫0 or "Không thể tính phí vận chuyển". Orders still proceed (Goship failure is non-blocking for checkout).

**Behavior by design:** `GoshipService` returns `null` on API failure. `ProcessCheckoutAction` falls back to `shippingFee = 0`. Order is created without waybill.

**Check:**

```bash
# Test Goship API connectivity
curl -s -o /dev/null -w "%{http_code}" https://api.goship.io/api/v2/rates \
  -H "Authorization: Bearer $GOSHIP_TOKEN" \
  -H "Content-Type: application/json"
```

**Operator action:**

1. Identify orders created during outage: orders with `shipping_fee = 0` and `goship_code = null` in the timeframe.
2. From Filament Admin → Orders: manually enter waybill for affected orders.
3. When Goship recovers: retry via Filament → Order → "Tạo Vận Đơn" action.

> ⚠️ **Known gap**: There is no automated retry job for Goship failures. Manual recovery is required. See [ADR backlog] for auto-retry queue worker scope.

---

## Promotion/Discount Not Applying

**Symptoms:** Customer reports expected discount not shown at checkout.

**Check cache first:**

```bash
# Flush promotion rules cache
docker compose -f docker-compose.prod.yml exec app php artisan cache:forget promotion_rules_catalog_active

# Or flush all cache
docker compose -f docker-compose.prod.yml exec app php artisan cache:clear
```

**Check rule status in Admin:**

1. Filament Admin → Promotions → Promotion Rules
2. Verify rule `is_active = true`, date range is valid, usage limit not exceeded
3. For tier-based rules: verify customer `total_spent` meets the tier threshold

**If cache flush doesn't fix:**

```bash
# Check if PromotionRuleObserver is firing on model mutation
docker compose -f docker-compose.prod.yml exec app php artisan tinker
# >>> PromotionRule::first()->touch(); // Should trigger cache invalidation
```

---

## High Response Times / Memory Usage

**Check:**

```bash
# Application memory + CPU in container
docker compose -f docker-compose.prod.yml exec app php artisan tinker --execute="echo memory_get_usage(true)/1024/1024 . 'MB';"

# MySQL slow queries (last 10)
docker compose -f docker-compose.prod.yml exec db mysql -u root -ppassword laravel \
  -e "SELECT query_time, sql_text FROM information_schema.processlist WHERE time > 5;"

# Check N+1 in CustomerResource listing (known issue under load)
# Fix: CustomerResource uses withSum() — ensure getEloquentQuery() override is present
```

---

## Database Backup Verification

```bash
# Create manual backup
docker compose -f docker-compose.prod.yml exec db \
  mysqldump -u root -ppassword laravel | gzip > backup_$(date +%Y%m%d_%H%M%S).sql.gz

# Verify latest backup file
ls -lh backup_*.sql.gz | tail -5
```

---

## Admin Panel Lockout / Granting Panel Access

**Context:** Since the RBAC refactor, `/admin` access requires a DB role (`super_admin` or `panel_user`) — email domain no longer grants access.

**After deploying to an existing environment, provision admins immediately (deploy does NOT do this):**

```bash
# Grant super_admin to an existing user (idempotent — safe to re-run)
docker compose -f docker-compose.prod.yml exec app php artisan admin:grant ops@yourdomain.com

# Verify panel access is GRANTED in the command output
```

**Symptoms:** Admin gets "These credentials do not match our records" or login loops back on `/admin`.

**Triage:**

```bash
# 1. Check which users hold admin roles
docker compose -f docker-compose.prod.yml exec app php artisan tinker --execute="
  \Spatie\Permission\Models\Role::with('users')->get()
    ->each(fn (\$r) => print(\$r->name.': '.\$r->users->pluck('email')->implode(', ').PHP_EOL));"

# 2. Check configured admin roles match seeded role names
grep AUTH_ADMIN_ROLES .env   # must contain e.g. super_admin,panel_user
```

**Common causes + fixes:**

| Symptom | Cause | Fix |
|---------|-------|-----|
| User exists but cannot log into panel | No role assigned after RBAC cutover | `php artisan admin:grant <email>` |
| ALL admins locked out | `AUTH_ADMIN_ROLES` set empty/garbage AND roles renamed | Fix env value; defaults fall back to `super_admin,panel_user` |
| Role exists but permissions missing | `shield:generate` failed during seed/deploy | Re-run `php artisan shield:generate --all --panel=admin`, check logs |

---

## Emergency Contacts / Escalation

| Scenario | Action |
|---------|--------|
| Data breach (PII exposed) | Rotate `APP_KEY`, invalidate all sessions, notify DPO |
| Payment system down | N/A — COD only, no payment gateway |
| DB corruption | Restore from last backup, replay queue jobs |
| Mass checkout failures | Check error log → identify root exception → page oncall developer |
