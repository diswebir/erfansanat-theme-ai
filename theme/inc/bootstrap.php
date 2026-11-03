<?php
/**
 * Single entry point required from functions.php.
 *
 * @package ErfanSanat\Theme
 */

defined( 'ABSPATH' ) || exit;

/** 1. Constants. */
require_once __DIR__ . '/Constants.php';

/** 2. Autoloader (classes below are resolved lazily). */
require_once __DIR__ . '/Autoloader.php';

/** 3. Kernel. */
\ErfanSanat\Theme\Theme::instance()->boot();
