<?php
/**
 * Plugin Name: Builder Content Guide
 * Plugin URI: https://github.com/deckerweb/builder-content-guide
 * Description: Find the right website building block, understand the effect of a change, and open its original editor.
 * Version: 1.0.0
 * Requires at least: 7.0
 * Requires PHP: 8.0
 * Author: David Decker – DECKERWEB
 * Author URI: https://github.com/deckerweb
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: builder-content-guide
 * Domain Path: /languages/
 * Update URI: https://github.com/deckerweb/builder-content-guide
 * GitHub Plugin URI: https://github.com/deckerweb/builder-content-guide
 *
 * Copyright © 2026 David Decker – DECKERWEB.
 * SPDX-License-Identifier: GPL-2.0-or-later
 */
namespace Deckerweb\BuilderContentGuide;

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'BCG_FILE', __FILE__ );
foreach ( array( 'sources', 'store', 'settings', 'help', 'admin', 'components', 'plugin' ) as $bcg_component ) {
	require_once __DIR__ . '/includes/class-' . $bcg_component . '.php';
}
add_action( 'plugins_loaded', array( Plugin::class, 'boot' ) );

require_once __DIR__ . '/includes/deckerweb-plugin-library/bootstrap.php';
deckerweb_library_register_v2( __FILE__, array(), __DIR__ . '/includes/deckerweb-plugin-library' );
