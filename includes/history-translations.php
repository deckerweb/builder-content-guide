<?php
/** Translate maintained changelog entries through the host domain. */
namespace Deckerweb\BuilderContentGuide;
if ( ! defined( 'ABSPATH' ) ) { exit; }
/** Resolve a changelog message at render time.
 * @param string $message English source message.
 * @return string Localized changelog entry.
 */
function history_message( string $message ): string {
 switch ( $message ) {
 case 'Updated plugin catalog and GitHub update support.': return __( 'Updated plugin catalog and GitHub update support.', 'builder-content-guide' );
 case 'The changelog remains closed until requested and keeps a documentation fallback.': return __( 'The changelog remains closed until requested and keeps a documentation fallback.', 'builder-content-guide' );
 case 'Requires PHP 8.1 or later; WordPress 7.0 remains the minimum.': return __( 'Requires PHP 8.1 or later; WordPress 7.0 remains the minimum.', 'builder-content-guide' );
 case 'Curated content guide for WordPress patterns, Bricks templates and GeneratePress elements.': return __( 'Curated content guide for WordPress patterns, Bricks templates and GeneratePress elements.', 'builder-content-guide' );
 case 'Reader help with supported content types and an optional support contact.': return __( 'Reader help with supported content types and an optional support contact.', 'builder-content-guide' );
 case 'WordPress pages, posts, template parts, block navigation, classic menus and active Elementor Free/Pro document types.': return __( 'WordPress pages, posts, template parts, block navigation, classic menus and active Elementor Free/Pro document types.', 'builder-content-guide' );
 case 'Searchable original selection, related guide links, example pages, ordered editing steps, copyable direct links and duplication as invisible drafts.': return __( 'Searchable original selection, related guide links, example pages, ordered editing steps, copyable direct links and duplication as invisible drafts.', 'builder-content-guide' );
 case 'Customizable site admin menu name and bundled WordPress icon.': return __( 'Customizable site admin menu name and bundled WordPress icon.', 'builder-content-guide' );
 case 'Direct admin submenus for finding content, managing the guide and adding entries.': return __( 'Direct admin submenus for finding content, managing the guide and adding entries.', 'builder-content-guide' );
 case 'Additional search terms, help after unsuccessful searches and reminders before editing shared building blocks.': return __( 'Additional search terms, help after unsuccessful searches and reminders before editing shared building blocks.', 'builder-content-guide' );
 default: return $message;
 }
}
