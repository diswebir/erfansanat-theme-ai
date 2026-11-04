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
        echo '<p><button type="button" class="button button-primary" data-es-save-options>'.esc_html__('ذخیره تنظیمات','erfansanat').'</button></p></form>';
    }
    protected function extraFields(): void {}
}
