<?php
declare(strict_types=1);
/** Task 7.3: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP07T03Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(file_exists(get_template_directory() . '/archive-services.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/single-services.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/page-templates/recruitment.php'));
    }
}
