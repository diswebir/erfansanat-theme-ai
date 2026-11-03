<?php
declare(strict_types=1);
/** Task 6.2: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP06T02Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(file_exists(get_template_directory() . '/archive-product.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/taxonomy-product_cat.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/taxonomy-product_tag.php'));
    }
}
