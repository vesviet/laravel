param (
    [switch]$SkipNpmBuild = $false,
    [switch]$SkipComposer = $false
)

$ServerIP = "192.168.1.114"
$User = "tuananh"
$RemoteDir = "/home/tuananh/laravel/maydiengiaisaigon"
$LocalDir = $PSScriptRoot

Write-Host "🚀 Bắt đầu deploy code lên $ServerIP..." -ForegroundColor Cyan

# 1. Đồng bộ code bằng SCP
Write-Host "`n[1/4] Đang chuyển code (scp) lên server..." -ForegroundColor Yellow
# Loại bỏ các thư mục không cần thiết: vendor, node_modules, .git
# Lưu ý: SCP mặc định không có exclude, rsync qua WSL sẽ tốt hơn nhưng để đơn giản trên Windows ta dùng ssh/scp
# Ở đây ta sẽ dùng rsync qua WSL cho tối ưu tốc độ nếu có thể, hoặc yêu cầu cài git bash.
# Tạm thời dùng SCP thủ công (Lưu ý: sẽ copy toàn bộ, nên cẩn thận với vendor/node_modules lớn)
Write-Host "Đang copy thư mục app, resources, public, config, routes, database..."
scp -r -o StrictHostKeyChecking=no "$LocalDir\app" "$LocalDir\resources" "$LocalDir\public" "$LocalDir\config" "$LocalDir\routes" "$LocalDir\database" "$LocalDir\tests" "$User@${ServerIP}:$RemoteDir/"
scp -o StrictHostKeyChecking=no "$LocalDir\composer.json" "$LocalDir\package.json" "$LocalDir\vite.config.js" "$LocalDir\phpunit.xml" "$LocalDir\.env.testing" "$User@${ServerIP}:$RemoteDir/"

# 2. Build Vite Assets (Node.js) trên Server
if (-not $SkipNpmBuild) {
    Write-Host "`n[2/4] Đang build Vite Assets (npm run build) qua Docker..." -ForegroundColor Yellow
    ssh -o StrictHostKeyChecking=no $User@$ServerIP "docker run --rm -v ${RemoteDir}:/app -w /app node:18-alpine sh -c 'npm install && npm run build'"
} else {
    Write-Host "`n[2/4] Bỏ qua build Vite Assets (-SkipNpmBuild)" -ForegroundColor Gray
}

# 3. Cập nhật PHP Vendor (Composer)
if (-not $SkipComposer) {
    Write-Host "`n[3/4] Đang cập nhật package PHP (composer install)..." -ForegroundColor Yellow
    ssh -o StrictHostKeyChecking=no $User@$ServerIP "docker exec -w /app maydiengiai_app composer install --optimize-autoloader"
} else {
    Write-Host "`n[3/4] Bỏ qua cập nhật Composer (-SkipComposer)" -ForegroundColor Gray
}

# 4. Sửa quyền (chown) và Migrate
Write-Host "`n[4/4] Đang tối ưu hóa Laravel và chạy Migration..." -ForegroundColor Yellow
# Ghi chú: Nếu lệnh chown thất bại do cần password, bạn hãy chạy lệnh này thủ công trên server một lần:
# sudo chown -R tuananh:tuananh /home/tuananh/laravel/maydiengiaisaigon
ssh -o StrictHostKeyChecking=no $User@$ServerIP "docker exec -w /app maydiengiai_app bash -c 'php artisan migrate --force && php artisan optimize:clear'"

Write-Host "`n✅ DEPLOY HOÀN TẤT! Truy cập: http://$ServerIP:8000" -ForegroundColor Green
