<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Tabs;
use ErfanSanat\Theme\Admin\OptionsStore;
use ErfanSanat\Theme\Filters\FilterRegistry;
final class FiltersTab extends BaseTab
{
    public static function id(): string { return 'archives'; }
    public static function fields(): array { return [
        ['key'=>'shop_per_page','label'=>'محصول در هر صفحه فروشگاه','type'=>'number'],['key'=>'projects_per_page','label'=>'پروژه در هر صفحه','type'=>'number'],['key'=>'blog_per_page','label'=>'مقاله در هر صفحه','type'=>'number'],
    ]; }
    protected function extraFields(): void {
        $enabled=(array)OptionsStore::get('enabled_filters',[]);
        echo '<h2>'.esc_html__('فعال‌سازی فیلترها','erfansanat').'</h2>';
        foreach(FilterRegistry::all() as $key=>$label){
            $checked=!empty($enabled[$key]);
            echo '<div class="es-field"><label><input type="hidden" name="options[enabled_filters]['.esc_attr($key).']" value="0"><input type="checkbox" name="options[enabled_filters]['.esc_attr($key).']" value="1"'.checked($checked,true,false).'> '.esc_html($label.' ('.$key.')').'</label></div>';
        }
    }
}
