<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class SettingsTransfer implements ServiceInterface
{
    public function register(): void {
        add_action('wp_ajax_erfansanat_reset_options', [$this,'handleReset']);
        add_action('wp_ajax_erfansanat_export_options', [$this,'handleExport']);
        add_action('wp_ajax_erfansanat_import_options', [$this,'handleImport']);
    }
    private function guard(): void { if(!current_user_can('manage_options')) wp_send_json_error(['message'=>'دسترسی غیرمجاز.'],403); check_ajax_referer('erfansanat_save_options'); }
    public function handleReset(): void { $this->guard(); $this->processReset(); wp_send_json_success(['message'=>'تنظیمات بازنشانی شد.']); }
    public function handleExport(): void { $this->guard(); wp_send_json_success(['options'=>$this->processExport()]); }
    public function handleImport(): void { $this->guard(); $json=isset($_POST['import_json'])?(string)wp_unslash($_POST['import_json']):''; $ok=$this->processImport($json); $ok?wp_send_json_success(['message'=>'تنظیمات وارد شد.']):wp_send_json_error(['message'=>'JSON نامعتبر است.'],400); }
    public function processReset(): bool { return OptionsStore::reset(); }
    /** @return array<string,mixed> */
    public function processExport(): array { return OptionsStore::all(); }
    public function processImport(string $json): bool {
        $data=json_decode($json,true); if(!is_array($data)) return false;
        (new AjaxSaveOptions())->processSave($data); return true;
    }
}
