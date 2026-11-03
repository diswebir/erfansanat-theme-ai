<?php
declare(strict_types=1);
/** Task 3.1: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP03T01Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(post_type_exists('projects'));
        $this->assertTrue(post_type_exists('services'));
        $this->assertTrue(taxonomy_exists('project_type'));
        $this->assertTrue(taxonomy_exists('city'));
        $this->assertTrue(taxonomy_exists('product_cat'));
    }
}
