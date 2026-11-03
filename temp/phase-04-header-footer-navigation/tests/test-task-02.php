<?php
declare(strict_types=1);
/** Task 4.2: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP04T02Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(has_nav_menu('main-mega-menu') || array_key_exists('main-mega-menu', get_registered_nav_menus()));
        $this->assertTrue(array_key_exists('mobile-menu', get_registered_nav_menus()));
        $this->assertTrue(array_key_exists('tolidat-sherkat', get_registered_nav_menus()));
        $this->assertTrue(array_key_exists('landing', get_registered_nav_menus()));
    }
}
