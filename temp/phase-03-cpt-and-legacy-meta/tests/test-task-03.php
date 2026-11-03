<?php
declare(strict_types=1);
/** Task 3.3: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP03T03Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(class_exists('ErfanSanat\Theme\Admin\ProjectMetaboxes'));
        $this->assertTrue(class_exists('ErfanSanat\Theme\Admin\TermMetaboxes'));
    }
}
