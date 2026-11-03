<?php
declare(strict_types=1);
/** Task 8.2: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP08T02Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(class_exists('ErfanSanat\Theme\Forms\FormManager'));
        $this->assertTrue(class_exists('ErfanSanat\Theme\Admin\Submissions'));
    }
}
