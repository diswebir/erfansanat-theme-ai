<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ToolingSmokeTest extends TestCase
{
    public function testPhp84Runtime(): void
    {
        $this->assertSame('8.4', PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION);
    }

    public function testExtensionsRequiredForLaterWordPressTests(): void
    {
        foreach (['dom', 'json', 'mbstring', 'mysqli', 'Phar', 'xml', 'zip'] as $extension) {
            $this->assertTrue(extension_loaded($extension), $extension);
        }
    }
}
