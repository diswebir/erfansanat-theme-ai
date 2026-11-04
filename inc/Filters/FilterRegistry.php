<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Filters;
final class FilterRegistry
{
    /** @return array<string,string> filter key => Persian label */
    public static function all(): array {
        $filters=['search'=>'جستجو','product_cat'=>'دسته‌بندی محصولات','price'=>'بازه قیمت','true_sale'=>'وضعیت خرید آنلاین','sort'=>'مرتب‌سازی','project_type'=>'نوع پروژه','city'=>'شهر پروژه'];
        foreach (self::attributes() as $slug=>$label) $filters['attr_'.$slug]=$label;
        return $filters;
    }
    /** @return array<string,string> 18 WooCommerce pa_* attributes */
    public static function attributes(): array {
        return ['pa_after-sales-support'=>'خدمات پس از فروش','pa_beam-angle'=>'زاویه تابش','pa_body-material'=>'جنس بدنه','pa_brand'=>'برند','pa_bulb-type'=>'جنس حباب','pa_cable-material'=>'جنس سیم','pa_certifications'=>'گواهینامه‌ها','pa_color'=>'رنگ نور','pa_diameter'=>'قطر','pa_diffuser-color'=>'رنگ حباب','pa_input-voltage'=>'ولتاژ ورودی','pa_intensity'=>'شدت نور','pa_ip-rating'=>'درجه حفاظت IP','pa_led-chip-type'=>'نوع چیپ LED','pa_lifespan'=>'طول عمر مفید','pa_size'=>'طول / تراکم','pa_warranty'=>'گارانتی','pa_wattage'=>'توان مصرفی'];
    }
    /** @return array<string,bool> */
    public static function defaultEnabled(): array { return array_fill_keys(array_keys(self::all()), true); }
    public static function isEnabled(string $key): bool {
        $map = \ErfanSanat\Theme\Admin\OptionsStore::get('enabled_filters', []);
        return is_array($map) ? (bool)($map[$key] ?? true) : true;
    }
}
