<?php
declare(strict_types=1);
/** Task 1.1: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP01T01Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(wp_get_theme()->get('Name') !== '');
        $this->assertTrue(current_theme_supports('title-tag'));
        $this->assertTrue(current_theme_supports('post-thumbnails'));
        $this->assertTrue(current_theme_supports('woocommerce'));
        $this->assertTrue(current_theme_supports('html5'));
    }
}
