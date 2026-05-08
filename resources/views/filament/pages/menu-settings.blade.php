<x-filament-panels::page>
    <div class="mb-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Kéo thả để thay đổi thứ tự Menu. Bấm nút "Thêm mục menu mới" để cấu hình MegaMenu.
        </p>
    </div>

    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="mt-4 flex gap-4">
            <x-filament::button type="submit" size="lg" color="primary">
                Lưu cấu trúc Menu
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
