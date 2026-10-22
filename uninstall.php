<?php
/** Uninstallation retains site-scoped guide explanations and explicit access settings.
 * Originals, roles, other plugins and network data are never removed.
 * Guide, contact and menu data are retained; only known component caches are cleaned.
 * The guide registers no scheduled tasks.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

require_once __DIR__ . '/includes/deckerweb-plugin-library/lifecycle.php';
deckerweb_library_uninstall_v3( __DIR__ . '/builder-content-guide.php' );
// Cleanup is bounded to this host's legacy and installed updater metadata keys.
$builder_content_guide_caches = array();
foreach ( array( 'https://github.com/deckerweb/builder-content-guide', 'https://github.com/deckerweb/builder-content-guide|2.2.1' ) as $builder_content_guide_identity ) {
 $builder_content_guide_key = 'ddw_ghru_' . substr( md5( $builder_content_guide_identity ), 0, 24 );
 $builder_content_guide_caches[] = $builder_content_guide_key;
 $builder_content_guide_caches[] = $builder_content_guide_key . '_private';
}
foreach ( $builder_content_guide_caches as $builder_content_guide_cache ) { delete_site_transient( $builder_content_guide_cache ); }
if ( is_multisite() ) {
 $builder_content_guide_offset = 0;
 do {
  $builder_content_guide_networks = get_networks( array( 'fields' => 'ids', 'number' => 100, 'offset' => $builder_content_guide_offset ) );
  foreach ( $builder_content_guide_networks as $builder_content_guide_network ) {
   foreach ( $builder_content_guide_caches as $builder_content_guide_cache ) {
    delete_network_option( $builder_content_guide_network, '_site_transient_' . $builder_content_guide_cache );
    delete_network_option( $builder_content_guide_network, '_site_transient_timeout_' . $builder_content_guide_cache );
   }
  }
  $builder_content_guide_offset += count( $builder_content_guide_networks );
 } while ( count( $builder_content_guide_networks ) === 100 );
}
