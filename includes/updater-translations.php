<?php
/** Copy into the host adapter and replace the builder-content-guide textdomain with its literal domain. */
defined( 'ABSPATH' ) || exit;
/**
 * Resolve an updater source string in the host textdomain.
 * @param string $message English updater message.
 * @return string Translated host message, or original source text.
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
        case 'Git Updater is active. If a deckerweb installation or update fails, temporarily deactivate Git Updater and retry. Reactivate it afterwards if other plugins or themes need it. Only leave it disabled when those extensions have another reliable update provider.':
            return __( 'Git Updater is active. If a deckerweb installation or update fails, temporarily deactivate Git Updater and retry. Reactivate it afterwards if other plugins or themes need it. Only leave it disabled when those extensions have another reliable update provider.', 'builder-content-guide' );
        case 'Manage installed plugins':
            return __( 'Manage installed plugins', 'builder-content-guide' );
        case 'Dismiss this notice':
            return __( 'Dismiss this notice', 'builder-content-guide' );
        case 'Permission denied.':
            return __( 'Permission denied.', 'builder-content-guide' );
        case 'Public catalog callbacks require both providers and cannot be used in private mode.':
            return __( 'Public catalog callbacks require both providers and cannot be used in private mode.', 'builder-content-guide' );
        case 'The approved release could not be verified.':
            return __( 'The approved release could not be verified.', 'builder-content-guide' );
        case 'The approved release changed. Refresh updates first.':
            return __( 'The approved release changed. Refresh updates first.', 'builder-content-guide' );
        case 'Catalog diagnostics require a public catalog provider.':
            return __( 'Catalog diagnostics require a public catalog provider.', 'builder-content-guide' );
        case 'Catalog approval':
            return __( 'Catalog approval', 'builder-content-guide' );
        case 'Package download':
            return __( 'Package download', 'builder-content-guide' );
        case 'Checksum verification':
            return __( 'Checksum verification', 'builder-content-guide' );
        case 'Archive verification':
            return __( 'Archive verification', 'builder-content-guide' );
        case 'The update server could not be reached. Try again later.':
            return __( 'The update server could not be reached. Try again later.', 'builder-content-guide' );
        case 'The update request timed out. Try again later.':
            return __( 'The update request timed out. Try again later.', 'builder-content-guide' );
        case 'The update server address could not be resolved.':
            return __( 'The update server address could not be resolved.', 'builder-content-guide' );
        case 'The secure connection to the update server failed.':
            return __( 'The secure connection to the update server failed.', 'builder-content-guide' );
        case 'The update server returned an unsuccessful HTTP response.':
            return __( 'The update server returned an unsuccessful HTTP response.', 'builder-content-guide' );
        case 'The update provider did not return valid update data.':
            return __( 'The update provider did not return valid update data.', 'builder-content-guide' );
        case 'The package does not match the approved checksum. Installation was stopped.':
            return __( 'The package does not match the approved checksum. Installation was stopped.', 'builder-content-guide' );
        case 'The archive or plugin identity could not be verified. Installation was stopped.':
            return __( 'The archive or plugin identity could not be verified. Installation was stopped.', 'builder-content-guide' );
        case 'Update stopped at %1$s: %2$s':
            /* translators: 1: update stage, 2: fixed explanation. */
            return __( 'Update stopped at %1$s: %2$s', 'builder-content-guide' );
        case 'HTTP status: %d.':
            /* translators: %d: HTTP error status. */
            return __( 'HTTP status: %d.', 'builder-content-guide' );
        case 'Invalid private download limits.':
            return __( 'Invalid private download limits.', 'builder-content-guide' );
        case 'Invalid package cleanup provider.':
            return __( 'Invalid package cleanup provider.', 'builder-content-guide' );
        case 'Safe package cleanup is unavailable for this update provider.':
            return __( 'Safe package cleanup is unavailable for this update provider.', 'builder-content-guide' );
        case 'The update package identity, version or requirements could not be verified.':
            return __( 'The update package identity, version or requirements could not be verified.', 'builder-content-guide' );
        default:
            return $message;
    }
};
