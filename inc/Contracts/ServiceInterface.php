<?php
/**
 * Service contract for modular OOP theme services.
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Contracts;

interface ServiceInterface
{
    /**
     * Register WordPress actions and filters for this service.
     */
    public function register(): void;
}
