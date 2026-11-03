<?php
/**
 * Contract every theme module implements.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Contracts;

defined( 'ABSPATH' ) || exit;

/**
 * A module owns its own hooks; the kernel only calls register().
 */
interface ModuleInterface {

	/**
	 * Register the module hooks. Must not produce any output.
	 */
	public function register(): void;
}
