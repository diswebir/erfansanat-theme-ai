<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Taxonomies;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class WooTaxonomyGuard implements ServiceInterface
{
    public function register(): void { add_action('init', [$this,'verify'], 99); add_filter('woocommerce_taxonomy_args_product_cat', [$this,'productCatArgs']); add_filter('woocommerce_taxonomy_args_product_tag', [$this,'productTagArgs']); }
    /** @param array<string,mixed> $args @return array<string,mixed> */
    public function productCatArgs(array $args): array { $args['hierarchical']=true; $args['rewrite']=['slug'=>'product_cat','with_front'=>true,'hierarchical'=>true]; return $args; }
    /** @param array<string,mixed> $args @return array<string,mixed> */
    public function productTagArgs(array $args): array { $args['rewrite']=['slug'=>'product-tag','with_front'=>false]; return $args; }
    public function verify(): void { /* Intentionally no re-registration: WooCommerce owns product_cat/product_tag. Verification is exposed via expectedRewrite(). */ }
    /** @return array<string,array<string,mixed>> */
    public static function expectedRewrite(): array { return ['product_cat'=>['slug'=>'product_cat','with_front'=>true,'hierarchical'=>true],'product_tag'=>['slug'=>'product-tag','with_front'=>false]]; }
}
