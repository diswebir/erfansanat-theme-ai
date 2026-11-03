<?php
declare(strict_types=1);
/** Task 8.1: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP08T01Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(class_exists('ErfanSanat\Theme\Ajax\Search'));
        $this->assertTrue(class_exists('ErfanSanat\Theme\Ajax\Filters'));
    }
}
