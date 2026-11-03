<?php
declare(strict_types=1);
/** Task 6.1: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP06T01Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(function_exists('wc_get_product'));
        $this->assertTrue(file_exists(get_template_directory() . '/single-product.php'));
    }
}
