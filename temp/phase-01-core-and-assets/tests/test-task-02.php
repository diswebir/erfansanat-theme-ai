<?php
declare(strict_types=1);
/** Task 1.2: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP01T02Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(wp_style_is('wp-block-library', 'done') === false);
        $this->assertTrue(wp_style_is('global-styles', 'done') === false);
        $this->assertTrue(!has_action('wp_head', 'print_emoji_detection_script'));
    }
}
