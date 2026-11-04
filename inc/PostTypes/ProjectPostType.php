<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\PostTypes;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class ProjectPostType implements ServiceInterface
{
    public const POST_TYPE='projects';
    public function register(): void { add_action('init', [$this,'registerPostType']); }
    public function registerPostType(): void {
        if (post_type_exists(self::POST_TYPE)) return;
        register_post_type(self::POST_TYPE, [
            'labels'=>['name'=>'پروژه‌ها','singular_name'=>'پروژه','add_new_item'=>'افزودن پروژه','edit_item'=>'ویرایش پروژه','menu_name'=>'پروژه‌ها'],
            'public'=>true,'has_archive'=>true,'hierarchical'=>false,'menu_icon'=>'dashicons-screenoptions','menu_position'=>20,
            'supports'=>['title','editor','thumbnail','comments','custom-fields','revisions'],
            'show_in_rest'=>true,'rewrite'=>['slug'=>'projects','with_front'=>false],
        ]);
    }
}
