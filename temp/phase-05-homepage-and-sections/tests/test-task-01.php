<?php
declare(strict_types=1);
/** Task 5.1: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP05T01Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(file_exists(get_template_directory() . '/front-page.php'));
        $this->assertTrue(count(glob(get_template_directory() . '/template-parts/home/section-*.php')) >= 15);
    }
}
