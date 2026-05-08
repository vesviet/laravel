<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Get;

class MenuSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    
    protected static ?string $navigationGroup = 'Hệ thống';
    
    protected static ?string $title = 'Quản lý MegaMenu';
    
    protected static ?string $slug = 'menu-settings';
    
    protected static string $view = 'filament.pages.menu-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $mainMenu = Setting::getCached('main_menu');
        
        // Deserialize JSON to array if it's a string
        $menuData = is_string($mainMenu) ? json_decode($mainMenu, true) : $mainMenu;
        
        // Provide empty array fallback
        if (!is_array($menuData)) {
            $menuData = [];
        }

        $this->form->fill([
            'main_menu' => $menuData,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('main_menu')
                    ->label('Cấu trúc Menu Chính')
                    ->collapsible()
                    ->collapsed() // UI/UX Requirement
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Mục Menu Mới') // UI/UX Requirement
                    ->reorderable()
                    ->schema([
                        TextInput::make('label')
                            ->label('Tên hiển thị')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('url')
                            ->label('Đường dẫn (URL)')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_mega')
                            ->label('Là MegaMenu (Có menu thả xuống nhiều cột)')
                            ->live(), // Trigger reactive updates

                        // Cấp 2: Các cột trong MegaMenu
                        Repeater::make('columns')
                            ->label('Các cột MegaMenu')
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Cột Mới')
                            ->maxItems(5) // QA Requirement
                            ->visible(fn (Get $get) => $get('is_mega') === true)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Tiêu đề cột')
                                    ->required(),
                                Select::make('type')
                                    ->label('Loại cột')
                                    ->options([
                                        'links' => 'Danh sách Link (Text)',
                                        'promo_banner' => 'Banner Quảng cáo (Hình ảnh)',
                                    ])
                                    ->required()
                                    ->live(),

                                // Cấp 3 (Dạng Link)
                                Repeater::make('links')
                                    ->label('Danh sách đường dẫn con')
                                    ->collapsible()
                                    ->collapsed()
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Link Mới')
                                    ->visible(fn (Get $get) => $get('type') === 'links')
                                    ->schema([
                                        TextInput::make('label')
                                            ->label('Tên Link')
                                            ->required(),
                                        TextInput::make('url')
                                            ->label('Đường dẫn')
                                            ->required(),
                                    ]),

                                // Cấp 3 (Dạng Banner)
                                FileUpload::make('image_path')
                                    ->label('Hình ảnh quảng cáo')
                                    ->image()
                                    ->directory('menu-banners')
                                    ->required(fn (Get $get) => $get('type') === 'promo_banner') // QA Requirement
                                    ->visible(fn (Get $get) => $get('type') === 'promo_banner'),
                                TextInput::make('promo_url')
                                    ->label('Link khi bấm vào Banner')
                                    ->required(fn (Get $get) => $get('type') === 'promo_banner') // QA Requirement
                                    ->visible(fn (Get $get) => $get('type') === 'promo_banner'),
                                TextInput::make('promo_text')
                                    ->label('Dòng text mô tả ngắn')
                                    ->visible(fn (Get $get) => $get('type') === 'promo_banner'),
                            ])
                    ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // DEV Solution: Serialize back to JSON before saving
        $jsonData = json_encode($data['main_menu'], JSON_UNESCAPED_UNICODE);

        // SRE Requirement: SRE Rollback mechanism (save backup before updating)
        $oldMenu = Setting::where('key', 'main_menu')->first();
        if ($oldMenu && $oldMenu->value) {
            Setting::updateOrCreate(
                ['key' => 'main_menu_backup'],
                ['value' => $oldMenu->value, 'type' => 'json']
            );
        }

        Setting::updateOrCreate(
            ['key' => 'main_menu'],
            ['value' => $jsonData, 'type' => 'json']
        );

        Notification::make()
            ->success()
            ->title('Đã lưu MegaMenu')
            ->body('Cấu trúc menu mới đã được lưu và tự động cập nhật Cache.')
            ->send();
    }
}
