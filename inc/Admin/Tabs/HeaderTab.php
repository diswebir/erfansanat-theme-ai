<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Tabs;
final class HeaderTab extends BaseTab
{
    public static function id(): string { return 'header'; }
    public static function fields(): array { return [
        ['key'=>'site_logo','label'=>'آدرس لوگو','type'=>'url'],['key'=>'brand_name','label'=>'نام برند','type'=>'text'],['key'=>'brand_tagline','label'=>'شعار برند','type'=>'text'],
        ['key'=>'announcement_enabled','label'=>'نمایش نوار اعلان','type'=>'toggle'],['key'=>'announcement_text','label'=>'متن نوار اعلان','type'=>'text'],['key'=>'announcement_url','label'=>'لینک نوار اعلان','type'=>'url'],
        ['key'=>'header_phone','label'=>'تلفن هدر','type'=>'text'],['key'=>'header_cta_text','label'=>'متن دکمه هدر','type'=>'text'],['key'=>'header_cta_url','label'=>'لینک دکمه هدر','type'=>'url'],
        ['key'=>'header_show_search','label'=>'نمایش جستجو در هدر','type'=>'toggle'],['key'=>'header_show_cart','label'=>'نمایش سبد در هدر','type'=>'toggle'],
    ]; }
}
