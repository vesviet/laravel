@extends("layouts.storefront")

@pushonce("page_title")Quyền Riêng Tư & Dữ Liệu — @endpushonce
@pushonce("meta_description")Quản lý quyền riêng tư, xuất dữ liệu cá nhân và xóa tài khoản theo GDPR.@endpushonce

@section("content")
<div class="py-10 md:py-14 bg-[#FAFAFA]">
    <div class="section-wrapper">
        <div class="max-w-2xl mx-auto">
            {{-- ── Header ── --}}
            <div class="mb-8">
                <a href="{{ route("account.profile") }}" class="text-xs uppercase tracking-wider text-[#888888] hover:text-[#23232C] font-medium link-underline inline-block mb-4">
                    ← Quay lại hồ sơ
                </a>
                <h1 class="text-2xl font-light text-[#23232C] tracking-wide uppercase">Quyền Riêng Tư & Dữ Liệu</h1>
                <p class="text-sm text-[#888888] font-light mt-2">
                    Kiểm soát dữ liệu cá nhân của bạn theo quy định GDPR và luật bảo vệ dữ liệu Việt Nam.
                </p>
            </div>

            {{-- Flash Messages --}}
            @if(session("success"))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs mb-6">
                    {{ session("success") }}
                </div>
            @endif

            {{-- Data Export --}}
            <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-[#23232C] mb-1">Xuất Dữ Liệu Cá Nhân</h3>
                        <p class="text-sm text-[#888888]">Tải xuống tất cả dữ liệu chúng tôi lưu về bạn (JSON)</p>
                    </div>
                    <button type="button" onclick="exportData()" class="btn-dark whitespace-nowrap">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Xuất Dữ Liệu (JSON)
                    </button>
                </div>
                <div class="text-xs text-[#888888] space-y-1">
                    <p>• Thông tin cá nhân, địa chỉ, đơn hàng, yêu thích, đánh giá</p>
                    <p>• Cài đặt bảo mật, 2FA, thông báo, đồng ý quyền riêng tư</p>
                    <p>• Lịch sử hoạt động (audit log), thông tin giới thiệu</p>
                </div>
            </div>

            {{-- Privacy Settings --}}
            <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm mb-8">
                <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] mb-4 pb-3 border-b border-[#E5E5E5]">Cài Đặt Quyền Riêng Tư</h3>

                <div class="space-y-4">
                    @if($customer->privacy_consent)
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach($customer->privacy_consent as $key => $value)
                                <div class="bg-[#F9F9F9] border border-[#E5E5E5] p-4 rounded-lg">
                                    <p class="text-xs uppercase tracking-wider text-[#888888] font-medium mb-1">{{ Str::ucfirst(str_replace("_", " ", $key)) }}</p>
                                    <p class="text-sm font-medium text-[#23232C]">{{ is_bool($value) ? ($value ? "Đã đồng ý" : "Chưa đồng ý") : $value }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-[#888888]">Chưa có bản ghi đồng ý quyền riêng tư.</p>
                    @endif
                </div>
            </div>

            {{-- Audit Log --}}
            <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm mb-8">
                <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] mb-4 pb-3 border-b border-[#E5E5E5]">Lịch Sử Hoạt Động (Audit Log)</h3>

                @if($auditLogs->count() > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($auditLogs as $log)
                            <div class="flex flex-col sm:flex-row gap-3 p-3 bg-[#F9F9F9] border border-[#E5E5E5] rounded-lg">
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded">
                                        {{ Str::ucfirst(str_replace("_", " ", $log->action)) }}
                                    </span>
                                    @if($log->description)
                                        <span class="text-xs text-[#888888]">{{ $log->description }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-[#888888] font-light">
                                    {{ $log->created_at->format("d/m/Y H:i") }}
                                    @if($log->ip_address)
                                        &middot; IP: {{ $log->ip_address }}
                                    @endif
                                </div>
                                @if($log->old_values || $log->new_values)
                                    <details class="text-xs text-[#888888]">
                                        <summary class="cursor-pointer text-blue-600 hover:underline">Xem chi tiết thay đổi</summary>
                                        <pre class="mt-2 p-2 bg-white border border-[#E5E5E5] rounded text-[10px] overflow-x-auto">{{ json_encode(array_filter(["old" => $log->old_values, "new" => $log->new_values]), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </details>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($auditLogs->hasPages())
                        <div class="mt-4 flex justify-center">
                            {{ $auditLogs->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <p class="text-sm text-[#888888]">Chưa có hoạt động nào được ghi nhận.</p>
                    </div>
                @endif
            </div>

            {{-- Danger Zone: Delete Account --}}
            <div class="bg-rose-50 border border-rose-200 p-6 shadow-sm rounded-lg">
                <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-rose-800 mb-4 pb-3 border-b border-rose-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Vùng Nguy Hiểm: Xóa Tài Khoản
                </h3>
                <p class="text-sm text-rose-700 mb-4">
                    <strong>Hành động này không thể hoàn tác.</strong> Tài khoản sẽ bị ẩn danh: tên, email, điện thoại, địa chỉ, lịch sử đơn hàng được giữ nhưng không liên kết với bạn. Dữ liệu ẩn danh được lưu để tuân thủ pháp lý.
                </p>

                <form method="POST" action="{{ route("account.privacy.delete") }}" onsubmit="return confirm(\"Bạn có chắc chắn muốn xóa tài khoản? Hành động này KHÔNG THỂ HOÀN TÁC.\")" class="space-y-4">
                    @csrf
                    @method("DELETE")

                    <div>
                        <label for="password_confirmation" class="block text-[10px] tracking-[0.15em] uppercase text-rose-700 mb-2">Nhập mật khẩu để xác nhận <span class="text-[#E84444]">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="current-password" class="input-underline w-full @error("password_confirmation") border-[#E84444] @enderror">
                        @error("password_confirmation")
                            <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="confirmation" id="confirmation" value="1" required class="h-4 w-4 accent-rose-600 cursor-pointer">
                        <label for="confirmation" class="text-sm text-rose-700 cursor-pointer">Tôi hiểu rằng tài khoản sẽ bị xóa vĩnh viễn và không thể khôi phục</label>
                    </div>
                    @error("confirmation")
                        <span class="text-[#E84444] text-xs block">{{ $message }}</span>
                    @enderror

                    <button type="submit" class="border border-rose-400 text-rose-700 hover:bg-rose-400 hover:text-white text-xs font-semibold uppercase tracking-wider px-6 py-2.5 transition-colors w-full sm:w-auto">Xóa Tài Khoản</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script>
    function exportData() {
        const btn = event.target.closest("button");
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang chuẩn bị...`;

        fetch("{{ route("account.privacy.export") }}", {
            method: "GET",
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
            }
        })
        .then(response => {
            if (!response.ok) throw new Error("Export failed");
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "gdpr-export-{{ $customer->id }}-{{ now()->format("Y-m-d") }}.json";
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            alert("Không thể xuất dữ liệu: " + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
</script>
@endpush
