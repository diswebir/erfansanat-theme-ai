<?php
declare(strict_types=1);
/** Task 2.1: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP02T01Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(class_exists('ErfanSanat\Theme\Admin\ThemeOptions'));
        $this->assertTrue(function_exists('ErfanSanat\Theme\es_option') || function_exists('es_option'));
    }
}
