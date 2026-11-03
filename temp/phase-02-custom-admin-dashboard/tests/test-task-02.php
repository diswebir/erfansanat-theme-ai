<?php
declare(strict_types=1);
/** Task 2.2: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP02T02Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(is_array(get_option('erfansanat_theme_options', null)));
        $this->assertTrue(post_type_exists('page'));
    }
}
