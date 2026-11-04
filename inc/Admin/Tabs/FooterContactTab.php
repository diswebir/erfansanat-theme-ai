<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Tabs;
final class FooterContactTab extends BaseTab
{
    public static function id(): string { return 'footer-contact'; }
    public static function fields(): array { return [
        ['key'=>'footer_about','label'=>'معرفی فوتر','type'=>'textarea'],['key'=>'footer_copyright','label'=>'متن کپی‌رایت','type'=>'text'],
        ['key'=>'contact_phone','label'=>'تلفن ثابت','type'=>'text'],['key'=>'contact_mobile','label'=>'موبایل','type'=>'text'],['key'=>'contact_email','label'=>'ایمیل','type'=>'email'],
        ['key'=>'contact_address','label'=>'نشانی','type'=>'textarea'],['key'=>'contact_working_hours','label'=>'ساعات کاری','type'=>'text'],
        ['key'=>'social_instagram','label'=>'اینستاگرام','type'=>'url'],['key'=>'social_telegram','label'=>'تلگرام','type'=>'url'],['key'=>'social_whatsapp','label'=>'واتساپ','type'=>'url'],['key'=>'social_linkedin','label'=>'لینکدین','type'=>'url'],['key'=>'social_aparat','label'=>'آپارات','type'=>'url'],
    ]; }
}
