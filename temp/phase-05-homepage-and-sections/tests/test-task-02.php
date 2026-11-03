<?php
declare(strict_types=1);
/** Task 5.2: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP05T02Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(file_exists(get_template_directory() . '/template-parts/cards/card-product.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/template-parts/cards/card-project.php'));
        $this->assertTrue(file_exists(get_template_directory() . '/template-parts/cards/card-post.php'));
    }
}
