<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'active_theme'],
            [
                'value' => 'elomus',
                'type' => 'string',
                'label' => 'Giao diện đang kích hoạt',
                'description' => 'Mã của theme đang được sử dụng trên frontend.',
            ]
        );
    }
}
