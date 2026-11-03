<?php
declare(strict_types=1);
/** Task 1.3: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP01T03Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(class_exists('ErfanSanat\Theme\AssetManager'));
        $this->assertTrue(is_dir(get_template_directory() . '/assets/fonts'));
        $this->assertTrue(file_exists(get_template_directory() . '/assets/css/styles.css'));
    }
}
