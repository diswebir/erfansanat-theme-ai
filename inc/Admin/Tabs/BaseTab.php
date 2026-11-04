<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\Tabs;
use ErfanSanat\Theme\Admin\Fields\FieldRenderer;
use ErfanSanat\Theme\Admin\OptionsStore;
use ErfanSanat\Theme\Contracts\ServiceInterface;
abstract class BaseTab implements ServiceInterface
{
    abstract public static function id(): string;
    /** @return array<int,array<string,mixed>> */
    abstract public static function fields(): array;
    public function register(): void { add_action('erfansanat_admin_tab_content_'.static::id(), [$this,'render']); }
    public function render(): void {
        echo '<form method="post" data-es-options-form>';
        wp_nonce_field('erfansanat_save_options');
        echo FieldRenderer::renderFields(static::fields(), OptionsStore::all()); // phpcs:ignore WordPress.Security.EscapeOutput
        $this->extraFields();
        echo '<div class="es-admin-status" data-es-admin-status role="status" aria-live="polite" aria-atomic="true"></div>';
        echo '<div class="es-settings-actions"><button type="button" class="button button-primary es-admin-primary-button" data-es-save-options>'.esc_html__('ذخیره تنظیمات','erfansanat').'</button><span class="es-settings-hint">'.esc_html__('تغییرات پس از ذخیره در سایت اعمال می‌شوند.','erfansanat').'</span></div></form>';
    }
    protected function extraFields(): void {}
}
