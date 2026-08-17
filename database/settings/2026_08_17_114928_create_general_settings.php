<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Sober Furniture');
        $this->migrator->add('general.site_description', 'Quality furniture for your home');
        $this->migrator->add('general.contact_email', 'contact@example.com');
        $this->migrator->add('general.contact_phone', '0123456789');
        $this->migrator->add('general.facebook_link', 'https://facebook.com');
        $this->migrator->add('general.zalo_link', 'https://zalo.me');
    }
};
