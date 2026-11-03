<?php
declare(strict_types=1);
/** Task 6.3: executable acceptance contract; PENDING until implementation. */
require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

final class TaskP06T03Test extends TestCase
{
    public function testAcceptanceContract(): void
    {
        $this->assertTrue(function_exists('WC'));
        $this->assertTrue(wc_get_page_id('cart') > 0);
        $this->assertTrue(wc_get_page_id('checkout') > 0);
        $this->assertTrue(wc_get_page_id('myaccount') > 0);
    }
}
