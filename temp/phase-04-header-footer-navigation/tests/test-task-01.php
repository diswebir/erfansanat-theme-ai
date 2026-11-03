<?php
declare(strict_types=1);
/** Task 4.1: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP04T01Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(file_exists(get_template_directory() . '/header.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/footer.php'));
    }
}
