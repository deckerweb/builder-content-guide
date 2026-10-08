<?php
/** Uninstallation retains site-scoped guide explanations and explicit access settings.
 * Originals, roles, other plugins and network data are never removed.
 * Guide, contact and menu data are retained; only known component caches are cleaned.
 * The guide registers no scheduled tasks.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

require_once __DIR__ . '/includes/deckerweb-plugin-library/lifecycle.php';
deckerweb_library_uninstall_v3( __DIR__ . '/builder-content-guide.php' );
delete_site_transient( 'ddw_ghru_' . substr( md5( 'https://github.com/deckerweb/builder-content-guide' ), 0, 24 ) );
// The package is physically shared across networks; remove only its known updater cache.
if ( is_multisite() ) {
	$bcg_offset = 0;
	$bcg_cache = 'ddw_ghru_' . substr( md5( 'https://github.com/deckerweb/builder-content-guide' ), 0, 24 );
	do {
		$bcg_networks = get_networks( array( 'fields' => 'ids', 'number' => 100, 'offset' => $bcg_offset ) );
		foreach ( $bcg_networks as $bcg_network ) {
			delete_network_option( $bcg_network, '_site_transient_' . $bcg_cache );
			delete_network_option( $bcg_network, '_site_transient_timeout_' . $bcg_cache );
		}
		$bcg_offset += count( $bcg_networks );
	} while ( count( $bcg_networks ) === 100 );
}
