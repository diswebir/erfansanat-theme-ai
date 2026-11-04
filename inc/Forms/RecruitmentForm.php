<?php
/** Secure recruitment submission and private resume handling. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Forms;

use ErfanSanat\Theme\Contracts\ServiceInterface;
use WP_Error;

final class RecruitmentForm implements ServiceInterface
{
    private const MAX_FILE_SIZE = 5242880;
    private const ROUTE = 'recruitment_submission';

    public function register(): void
    {
        add_filter('erfan_sanat_ajax_routes', [$this, 'registerRoute']);
    }

    /** @param mixed $routes @return array<string,callable> */
    public function registerRoute(mixed $routes): array
    {
        $routes = is_array($routes) ? $routes : [];
        $routes[self::ROUTE] = [$this, 'handle'];
        return $routes;
    }

    /** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
    public function handle(array $input): array|WP_Error
    {
        $files = isset($input['_files']) && is_array($input['_files']) ? $input['_files'] : [];
        return $this->submit($input, $files);
    }

    /** @param array<string,mixed> $input @param array<string,mixed> $files @return array<string,mixed>|WP_Error */
    public function submit(array $input, array $files): array|WP_Error
    {
        $resume = isset($files['resume']) && is_array($files['resume']) ? $files['resume'] : [];
        $validation = $this->validateResume($resume);
        if (is_wp_error($validation)) return $validation;
        $fields = (new FormManager())->sanitizeFields($input);
        $name = trim($fields['name'] ?? '');
        $phoneInput = strtr($fields['phone'] ?? '', ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9']);
        $phone = preg_replace('/[^0-9+]/', '', $phoneInput) ?: '';
        $message = trim($fields['message'] ?? '');
        if (strlen($name) < 2) return new WP_Error('recruitment_name_invalid', 'نام را به‌درستی وارد کنید.');
        if (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) return new WP_Error('recruitment_phone_invalid', 'شماره تماس معتبر وارد کنید.');
        if ($message === '') return new WP_Error('recruitment_message_invalid', 'شرح درخواست را وارد کنید.');
        $fields['name'] = $name;
        $fields['phone'] = $phone;
        $fields['message'] = $message;
        $fields['email'] = sanitize_email($fields['email'] ?? '');

        $id = (new FormManager())->saveSubmission('recruitment', $fields, $input);
        if (is_wp_error($id)) return $id;
        $path = $this->storeResume($resume);
        if (is_wp_error($path)) {
            wp_delete_post($id, true);
            return $path;
        }
        update_post_meta($id, 'es_resume_path', $path);
        update_post_meta($id, 'es_resume_name', sanitize_file_name((string) ($resume['name'] ?? 'resume')));

        $to = sanitize_email((string) get_option('admin_email'));
        if ($to !== '') {
            $body = "نام: {$name}\nتلفن: {$phone}\nایمیل: {$fields['email']}\n\n{$message}";
            wp_mail($to, 'درخواست همکاری از وب‌سایت عرفان صنعت', $body, [], [$path]);
        }
        return ['data' => ['submission_id' => $id, 'message' => 'درخواست همکاری شما با موفقیت ثبت شد.'], 'html' => '', 'count' => 1];
    }

    /** Validate server-provided upload metadata and inspect the actual file MIME. @param array<string,mixed> $file @return true|WP_Error */
    public function validateResume(array $file): true|WP_Error
    {
        if (!isset($file['error']) || (int) $file['error'] !== UPLOAD_ERR_OK) return new WP_Error('resume_upload_failed', 'بارگذاری رزومه انجام نشد.');
        $name = isset($file['name']) && is_scalar($file['name']) ? sanitize_file_name((string) $file['name']) : '';
        $tmp = isset($file['tmp_name']) && is_string($file['tmp_name']) ? $file['tmp_name'] : '';
        $size = isset($file['size']) && is_numeric($file['size']) ? (int) $file['size'] : 0;
        if ($name === '' || $tmp === '' || !is_file($tmp) || $size < 1 || $size > self::MAX_FILE_SIZE) return new WP_Error('resume_size_invalid', 'رزومه باید حداکثر ۵ مگابایت باشد.');
        $extension = strtolower((string) pathinfo($name, PATHINFO_EXTENSION));
        $mimes = [
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword', 'application/x-ole-storage', 'application/vnd.ms-office'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        ];
        if (!isset($mimes[$extension])) return new WP_Error('resume_type_invalid', 'فقط فایل PDF یا Word پذیرفته می‌شود.');
        if (!function_exists('finfo_open')) return new WP_Error('resume_mime_unavailable', 'بررسی نوع فایل در سرور در دسترس نیست.');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $tmp) : false;
        // finfo is an object in supported PHP versions and is released automatically;
        // finfo_close() is deprecated as of PHP 8.5.
        unset($finfo);
        if (!is_string($mime) || !in_array($mime, $mimes[$extension], true)) return new WP_Error('resume_mime_invalid', 'محتوای فایل با پسوند آن سازگار نیست.');
        return true;
    }

    /** @param array<string,mixed> $file @return string|WP_Error */
    private function storeResume(array $file): string|WP_Error
    {
        $tmp = is_string($file['tmp_name'] ?? null) ? $file['tmp_name'] : '';
        if ($tmp === '' || !is_uploaded_file($tmp)) return new WP_Error('resume_not_uploaded', 'فایل رزومه بارگذاری معتبر ندارد.');
        $upload = wp_upload_dir();
        if (!empty($upload['error'])) return new WP_Error('resume_storage_unavailable', 'ذخیره‌سازی رزومه در دسترس نیست.');
        $directory = trailingslashit($upload['basedir']) . 'es-private-resumes';
        if (!wp_mkdir_p($directory)) return new WP_Error('resume_storage_unavailable', 'ذخیره‌سازی رزومه در دسترس نیست.');
        @chmod($directory, 0750);
        if (!is_file($directory . '/.htaccess')) file_put_contents($directory . '/.htaccess', "Require all denied\nDeny from all\n");
        if (!is_file($directory . '/index.php')) file_put_contents($directory . '/index.php', "<?php\n// Silence is golden.\n");
        $name = sanitize_file_name((string) ($file['name'] ?? 'resume.pdf'));
        $destination = trailingslashit($directory) . wp_unique_filename($directory, $name);
        if (!move_uploaded_file($tmp, $destination)) return new WP_Error('resume_storage_failed', 'ذخیره رزومه انجام نشد.');
        @chmod($destination, 0640);
        return $destination;
    }
}
