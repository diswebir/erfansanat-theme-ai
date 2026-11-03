<?php
declare(strict_types=1);
/** Task 7.1: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP07T01Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(file_exists(get_template_directory() . '/single-projects.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/archive-projects.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/taxonomy-city.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/taxonomy-project_type.php'));
    }
}
