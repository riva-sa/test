<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('sites.site_name', 'Riva - ريفا');
        $this->migrator->add('sites.site_description', 'شركة ريفا العقارية');
        $this->migrator->add('sites.site_keywords', 'عقارات, شقق, فيلات, مكاتب, مصانع, اراضي, مشاريع, تمليك');
        $this->migrator->add('sites.site_profile', '');
        $this->migrator->add('sites.site_logo', '');
        $this->migrator->add('sites.site_address', '');
        $this->migrator->add('sites.site_favicon', '');
        $this->migrator->add('sites.site_language', 'ar');
        $this->migrator->add('sites.site_author', 'طلال الحربي');
        $this->migrator->add('sites.site_email', 'info@riva.sa');
        $this->migrator->add('sites.site_phone', '');
        $this->migrator->add('sites.site_social', []);
    }
};
