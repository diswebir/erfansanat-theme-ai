<?php
declare(strict_types=1);
/** Task 7.2: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP07T02Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(file_exists(get_template_directory() . '/single.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/search.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/404.php'));
    }
}
