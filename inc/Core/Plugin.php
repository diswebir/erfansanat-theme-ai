<?php
/**
 * Central Theme Kernel / Service Container.
 *
 * @package ErfanSanat\Theme\Core
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Core;

use ErfanSanat\Theme\Contracts\ServiceInterface;

final class Plugin
{
    private static ?self $instance = null;

    private bool $booted = false;

    /**
     * @var array<class-string<ServiceInterface>, ServiceInterface>
     */
    private array $services = [];

    private function __construct()
    {
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Boot and register all core theme services once.
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        foreach ($this->getServiceClasses() as $serviceClass) {
            if (class_exists($serviceClass)) {
                /** @var ServiceInterface $service */
                $service = new $serviceClass();
                $service->register();
                $this->services[$serviceClass] = $service;
            }
        }

        $this->booted = true;
    }

    /**
     * Retrieve a registered service instance by class name.
     *
     * @template T of ServiceInterface
     * @param class-string<T> $serviceClass
     * @return T|null
     */
    public function getService(string $serviceClass): ?ServiceInterface
    {
        /** @var T|null $service */
        $service = $this->services[$serviceClass] ?? null;

        return $service;
    }

    /**
     * Ordered list of theme services across phases.
     *
     * @return array<int, class-string<ServiceInterface>>
     */
    private function getServiceClasses(): array
    {
        return [
            ThemeSetup::class,
            \ErfanSanat\Theme\Ajax\AjaxController::class,
            \ErfanSanat\Theme\Ajax\LiveSearchProducts::class,
            \ErfanSanat\Theme\Ajax\LiveSearchProjectsPosts::class,
            \ErfanSanat\Theme\Ajax\ProductFilterEndpoint::class,
            DeBloat::class,
            AssetManager::class,
            \ErfanSanat\Theme\Admin\ThemeOptions::class,
            \ErfanSanat\Theme\Admin\AdminAssets::class,
            \ErfanSanat\Theme\Admin\AjaxSaveOptions::class,
            \ErfanSanat\Theme\Admin\SettingsTransfer::class,
            \ErfanSanat\Theme\Admin\Tabs\HeaderTab::class,
            \ErfanSanat\Theme\Admin\Tabs\FooterContactTab::class,
            \ErfanSanat\Theme\Admin\Tabs\AppearanceTab::class,
            \ErfanSanat\Theme\Admin\Tabs\FiltersTab::class,
            \ErfanSanat\Theme\Admin\SectionManager\SectionMetabox::class,
            \ErfanSanat\Theme\PostTypes\ProjectPostType::class,
            \ErfanSanat\Theme\Taxonomies\ProjectTaxonomies::class,
            \ErfanSanat\Theme\Taxonomies\ProductBrandTaxonomy::class,
            \ErfanSanat\Theme\Taxonomies\WooTaxonomyGuard::class,
            \ErfanSanat\Theme\Core\ProjectRedirect::class,
            \ErfanSanat\Theme\Admin\Columns\ProjectColumns::class,
            \ErfanSanat\Theme\Admin\Metaboxes\ProductMetabox::class,
            \ErfanSanat\Theme\Admin\Metaboxes\ProductIdentityFields::class,
            \ErfanSanat\Theme\Admin\Metaboxes\ProjectMetabox::class,
            \ErfanSanat\Theme\Admin\Metaboxes\ProductCatTermFields::class,
            \ErfanSanat\Theme\Admin\Metaboxes\HreflangMetabox::class,
            \ErfanSanat\Theme\Admin\Metaboxes\HreflangTermFields::class,
            \ErfanSanat\Theme\Seo\HreflangTags::class,
            \ErfanSanat\Theme\Home\DefaultSections::class,
            \ErfanSanat\Theme\WooCommerce\WooSetup::class,
            \ErfanSanat\Theme\WooCommerce\MiniCart::class,
        ];
    }
}
