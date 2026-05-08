<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ThemeSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    
    protected static ?string $navigationGroup = 'Hệ thống';
    
    protected static ?string $title = 'Quản lý Giao diện (Theme)';
    
    protected static ?string $slug = 'theme-settings';
    
    protected static string $view = 'filament.pages.theme-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $activeTheme = Setting::getCached('active_theme', 'elomus');
        $this->form->fill([
            'active_theme' => $activeTheme,
        ]);
    }

    public function form(Form $form): Form
    {
        $themes = config('themes.available', []);
        
        $options = [];
        $descriptions = [];
        
        foreach ($themes as $key => $theme) {
            $options[$key] = $theme['name'];
            $descriptions[$key] = new HtmlString('
                <div style="margin-top: 0.5rem; margin-bottom: 1rem;">
                    <p class="text-sm text-gray-500 mb-2 dark:text-gray-400">' . $theme['description'] . '</p>
                    <img src="' . $theme['preview_image'] . '" style="max-height: 150px; border-radius: 0.5rem; border: 1px solid #e5e7eb;" alt="' . $theme['name'] . '" />
                </div>
            ');
        }

        return $form
            ->schema([
                Section::make('Chọn giao diện hiển thị')
                    ->description('Giao diện sẽ được áp dụng ngay lập tức trên toàn bộ website người dùng.')
                    ->schema([
                        Radio::make('active_theme')
                            ->hiddenLabel()
                            ->options($options)
                            ->descriptions($descriptions)
                            ->required(),
                    ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::updateOrCreate(
            ['key' => 'active_theme'],
            ['value' => $data['active_theme']]
        );

        Notification::make()
            ->success()
            ->title('Thành công')
            ->body('Giao diện website đã được thay đổi. Hãy tải lại trang chủ để xem sự khác biệt.')
            ->send();
    }
}
