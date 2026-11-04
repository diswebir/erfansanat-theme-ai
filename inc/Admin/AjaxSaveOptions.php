<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class AjaxSaveOptions implements ServiceInterface
{
    private const PAGE_SIZE_KEYS = ['shop_per_page', 'projects_per_page', 'blog_per_page'];
    private const PAGE_SIZE_MIN = 1;
    private const PAGE_SIZE_MAX = 100;
    public function register(): void { add_action('wp_ajax_erfansanat_save_options', [$this,'handle']); }
    public function handle(): void {
        if (!current_user_can('manage_options')) wp_send_json_error(['message'=>'دسترسی غیرمجاز.'],403);
        check_ajax_referer('erfansanat_save_options');
        $raw = isset($_POST['options']) && is_array($_POST['options']) ? wp_unslash($_POST['options']) : []; // phpcs:ignore
        wp_send_json_success(['message'=>'تنظیمات ذخیره شد.','saved'=>$this->processSave($raw)]);
    }
    /** @param array<string,mixed> $raw @return array<string,mixed> sanitized saved values */
    public function processSave(array $raw): array {
        $types=Defaults::types(); $clean=[];
        foreach ($raw as $key=>$value) {
            if (!isset($types[$key])) continue;
            $sanitized = self::sanitize($value, $types[$key]);
            if (in_array($key, self::PAGE_SIZE_KEYS, true)) {
                $sanitized = max(self::PAGE_SIZE_MIN, min(self::PAGE_SIZE_MAX, (int) $sanitized));
            }
            $clean[$key] = $sanitized;
        }
        OptionsStore::update($clean); return $clean;
    }
    public static function sanitize(mixed $value, string $type): mixed {
        return match($type){
            'text'=>sanitize_text_field(is_scalar($value)?(string)$value:''),
            'textarea'=>sanitize_textarea_field(is_scalar($value)?(string)$value:''),
            'url'=>esc_url_raw(is_scalar($value)?(string)$value:''),
            'email'=>sanitize_email(is_scalar($value)?(string)$value:''),
            'color'=>sanitize_hex_color(is_scalar($value)?(string)$value:'') ?: '',
            'bool'=> (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'int'=>absint(is_scalar($value)?$value:0),
            'raw'=> is_scalar($value)?(string)$value:'',
            'filter_map'=> self::sanitizeFilterMap(is_array($value)?$value:[]),
            default=>sanitize_text_field(is_scalar($value)?(string)$value:''),
        };
    }
    /** @param array<string,mixed> $value @return array<string,bool> */
    private static function sanitizeFilterMap(array $value): array {
        $out=[]; foreach(\ErfanSanat\Theme\Filters\FilterRegistry::all() as $k=>$label) $out[$k]=(bool)filter_var($value[$k]??false,FILTER_VALIDATE_BOOLEAN); return $out;
    }
}
