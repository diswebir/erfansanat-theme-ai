<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin;
final class Defaults
{
    /** @return array<string,mixed> */
    public static function all(): array {
        return [
            'site_logo'=>'','brand_name'=>'عرفان صنعت اصفهان','brand_tagline'=>'نورپردازی شهری و المان‌های نوری',
            'announcement_enabled'=>true,'announcement_text'=>'','announcement_url'=>'',
            'header_phone'=>'031-91091011','header_cta_text'=>'مشاوره رایگان','header_cta_url'=>'','header_show_search'=>true,'header_show_cart'=>true,
            'footer_about'=>'','footer_copyright'=>'','contact_phone'=>'031-91091011','contact_mobile'=>'09137976915','contact_email'=>'','contact_address'=>'','contact_working_hours'=>'',
            'social_instagram'=>'','social_telegram'=>'','social_whatsapp'=>'','social_linkedin'=>'','social_aparat'=>'',
            'brand_primary_color'=>'#0e7a5f','custom_head_code'=>'','custom_footer_code'=>'','favicon_id'=>0,
            'shop_per_page'=>12,'projects_per_page'=>12,'blog_per_page'=>10,
            'enabled_filters'=> \ErfanSanat\Theme\Filters\FilterRegistry::defaultEnabled(),
        ];
    }
    /** @return array<string,string> key => type */
    public static function types(): array {
        return [
            'site_logo'=>'url','favicon_id'=>'int','brand_name'=>'text','brand_tagline'=>'text','announcement_enabled'=>'bool','announcement_text'=>'text','announcement_url'=>'url',
            'header_phone'=>'text','header_cta_text'=>'text','header_cta_url'=>'url','header_show_search'=>'bool','header_show_cart'=>'bool',
            'footer_about'=>'textarea','footer_copyright'=>'text','contact_phone'=>'text','contact_mobile'=>'text','contact_email'=>'email','contact_address'=>'textarea','contact_working_hours'=>'text',
            'social_instagram'=>'url','social_telegram'=>'url','social_whatsapp'=>'url','social_linkedin'=>'url','social_aparat'=>'url',
            'brand_primary_color'=>'color','custom_head_code'=>'raw','custom_footer_code'=>'raw','shop_per_page'=>'int','projects_per_page'=>'int','blog_per_page'=>'int','enabled_filters'=>'filter_map',
        ];
    }
}
