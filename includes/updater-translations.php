<?php
/** Copy into the host adapter and replace the builder-content-guide textdomain with its literal domain. */
defined( 'ABSPATH' ) || exit;
/** Translate an updater message at output time.
 * @param string $message English source message.
 * @return string Host-localized message, or unchanged source for unknown keys.
 */
return static function ( string $message ): string {
    // Literal calls let the host's normal translation extractor collect every source string.
    switch ( $message ) {
        case 'Private mode must be boolean.':
            return __( 'Private mode must be boolean.', 'builder-content-guide' );
        case 'Invalid authentication provider.':
            return __( 'Invalid authentication provider.', 'builder-content-guide' );
        case 'The plugin must be installed in a stable slug directory.':
            return __( 'The plugin must be installed in a stable slug directory.', 'builder-content-guide' );
        case 'Invalid GitHub repository URL.':
            return __( 'Invalid GitHub repository URL.', 'builder-content-guide' );
        case 'The private update could not be authorized. Check the repository credentials and refresh updates.':
            return __( 'The private update could not be authorized. Check the repository credentials and refresh updates.', 'builder-content-guide' );
        case 'Could not create the update download file.':
            return __( 'Could not create the update download file.', 'builder-content-guide' );
        case 'The private update download failed. Check credentials and try again.':
            return __( 'The private update download failed. Check credentials and try again.', 'builder-content-guide' );
        case 'Could not access the update filesystem.':
            return __( 'Could not access the update filesystem.', 'builder-content-guide' );
        case 'GitHub release does not contain the plugin main file.':
            return __( 'GitHub release does not contain the plugin main file.', 'builder-content-guide' );
        case 'Could not prepare the GitHub release package.':
            return __( 'Could not prepare the GitHub release package.', 'builder-content-guide' );
        case 'See the release on GitHub.':
            return __( 'See the release on GitHub.', 'builder-content-guide' );
        default:
            return $message;
    }
};
