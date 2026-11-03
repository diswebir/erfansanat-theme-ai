<?php
declare(strict_types=1);
/** Task 8.3: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP08T03Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(file_exists(ABSPATH . 'wp-content/themes/' . get_template() . '/functions.php'));
        $this->assertTrue(defined('WP_DEBUG') && WP_DEBUG);
    }
}
