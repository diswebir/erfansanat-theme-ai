<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin\SectionManager;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class SectionMetabox implements ServiceInterface
{
    public function register(): void {
        add_action('add_meta_boxes_page', [$this,'addMetabox']);
        add_action('save_post_page', [$this,'save'], 10, 2);
        add_action('admin_enqueue_scripts', [$this,'enqueue']);
    }
    public function addMetabox(\WP_Post $post): void { add_meta_box('erfansanat-sections','سکشن‌های صفحه',[$this,'render'],'page','normal','default'); }
    public function enqueue(string $hook): void { if(in_array($hook,['post.php','post-new.php'],true)){ wp_enqueue_script('jquery-ui-sortable'); wp_enqueue_script('erfansanat-section-manager',ERFANSANAT_THEME_URI.'/assets/admin/js/section-manager.js',['jquery','jquery-ui-sortable'],(string)filemtime(ERFANSANAT_THEME_DIR.'/assets/admin/js/section-manager.js'),true); } }
    public function render(\WP_Post $post): void {
        wp_nonce_field('erfansanat_save_sections','es_sections_nonce');
        $sections=SectionStorage::get((int)$post->ID);
        if($sections===[]){ echo '<p>'.esc_html__('هنوز سکشنی ثبت نشده است. سکشن‌ها از طریق کد یا ایمپورت صفحه اضافه می‌شوند و در اینجا قابل مرتب‌سازی و فعال/غیرفعال‌سازی هستند.','erfansanat').'</p>'; return; }
        echo '<div data-es-sections-list>';
        foreach($sections as $i=>$s){
            echo '<div class="es-section-item" data-es-section="'.esc_attr($s['type']).'">';
            echo '<div data-es-section-toggle-head><span data-es-section-handle>⠿</span> <strong>'.esc_html($s['type']).'</strong> ';
            echo '<label><input type="checkbox" name="es_sections['.$i.'][enabled]" value="1"'.checked($s['enabled'],true,false).'> '.esc_html__('فعال','erfansanat').'</label></div>';
            echo '<input type="hidden" name="es_sections['.$i.'][id]" value="'.esc_attr($s['id']).'"><input type="hidden" name="es_sections['.$i.'][type]" value="'.esc_attr($s['type']).'"><input type="hidden" data-es-section-order name="es_sections['.$i.'][order]" value="'.esc_attr((string)$i).'">';
            echo '<div data-es-section-body>';
            foreach($s['fields'] as $fk=>$fv){ if(is_scalar($fv)) echo '<p><label>'.esc_html((string)$fk).'<input type="text" name="es_sections['.$i.'][fields]['.esc_attr((string)$fk).']" value="'.esc_attr((string)$fv).'"></label></p>'; }
            echo '</div></div>';
        }
        echo '</div>';
    }
    public function save(int $postId, \WP_Post $post): void {
        if(!isset($_POST['es_sections_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['es_sections_nonce'])),'erfansanat_save_sections')) return;
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if(!current_user_can('edit_page',$postId)) return;
        $raw=isset($_POST['es_sections']) && is_array($_POST['es_sections']) ? wp_unslash($_POST['es_sections']) : [];
        SectionStorage::save($postId,$raw);
    }
}
