<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Tabs;
use ErfanSanat\Theme\Admin\Fields\MediaField;
use ErfanSanat\Theme\Admin\OptionsStore;
final class AppearanceTab extends BaseTab
{
    public static function id(): string { return 'appearance'; }
    public static function fields(): array { return [
        ['key'=>'brand_primary_color','label'=>'رنگ اصلی برند','type'=>'color'],
        ['key'=>'custom_head_code','label'=>'کد سفارشی Head','type'=>'textarea','description'=>'فقط مدیران ارشد؛ بدون sanitize محتوایی ذخیره می‌شود.'],
        ['key'=>'custom_footer_code','label'=>'کد سفارشی Footer','type'=>'textarea'],
    ]; }
    protected function extraFields(): void { echo MediaField::render('favicon_id','فاوآیکون',(int)OptionsStore::get('favicon_id',0)); }
}
