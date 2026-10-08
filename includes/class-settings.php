<?php
/** Site-scoped integration settings for the native admin menu. */
namespace Deckerweb\BuilderContentGuide;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Stores a menu label and an allowlisted local WordPress icon without changing plugin identity. */
final class Settings {
	const MENU_OPTION = 'bcg_menu_settings';

	/** Return a compact selection of bundled WordPress icons.
	 * @return array<string,string> Dashicon classes and localized descriptions.
	 */
	public static function icons(): array {
		return array(
			'dashicons-location-alt' => __( 'Location', 'builder-content-guide' ),
			'dashicons-book-alt' => __( 'Open book', 'builder-content-guide' ),
			'dashicons-book' => __( 'Book', 'builder-content-guide' ),
			'dashicons-editor-help' => __( 'Help', 'builder-content-guide' ),
			'dashicons-info-outline' => __( 'Information', 'builder-content-guide' ),
			'dashicons-welcome-learn-more' => __( 'Learn more', 'builder-content-guide' ),
			'dashicons-lightbulb' => __( 'Lightbulb', 'builder-content-guide' ),
			'dashicons-clipboard' => __( 'Clipboard', 'builder-content-guide' ),
			'dashicons-list-view' => __( 'List', 'builder-content-guide' ),
			'dashicons-admin-tools' => __( 'Tools', 'builder-content-guide' ),
		);
	}

	/** Return validated menu settings, keeping the default label localized when no custom name is set.
	 * @return array Menu label and icon for the current website.
	 */
	public static function menu(): array {
		$saved = get_option( self::MENU_OPTION, array() );
		$saved = is_array( $saved ) ? $saved : array();
		$label = isset( $saved['label'] ) && is_string( $saved['label'] ) ? sanitize_text_field( $saved['label'] ) : '';
		$icon = isset( $saved['icon'] ) && is_string( $saved['icon'] ) && isset( self::icons()[ $saved['icon'] ] ) ? $saved['icon'] : 'dashicons-location-alt';
		return array( 'label' => $label, 'icon' => $icon );
	}

	/** Save authorized menu preferences after validating scalar fields and the icon allowlist.
	 * @param array $input Unslashed menu label and icon.
	 * @return bool|\WP_Error True after saving, or a permission/validation error.
	 */
	public static function save_menu( array $input ) {
		if ( ! current_user_can( 'manage_options' ) ) { return new \WP_Error( 'bcg_forbidden', __( 'You cannot manage this guide.', 'builder-content-guide' ) ); }
		if ( ! isset( $input['label'], $input['icon'] ) || ! is_string( $input['label'] ) || ! is_string( $input['icon'] ) || ! isset( self::icons()[ $input['icon'] ] ) ) { return new \WP_Error( 'bcg_menu_invalid', __( 'Enter a menu name and choose an available WordPress icon.', 'builder-content-guide' ) ); }
		$label = sanitize_text_field( $input['label'] );
		if ( strlen( $label ) > 160 ) { return new \WP_Error( 'bcg_menu_invalid', __( 'Please use a shorter menu name.', 'builder-content-guide' ) ); }
		update_option( self::MENU_OPTION, array( 'label' => $label, 'icon' => $input['icon'] ), false );
		return true;
	}

	/** Render the integration settings only for website administrators.
	 * @return void
	 */
	public static function menu_form(): void {
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		$data = self::menu();
		echo '<section class="bcg-panel bcg-menu-settings"><h2>' . esc_html__( 'Admin menu', 'builder-content-guide' ) . '</h2><p>' . esc_html__( 'Adapt the menu to this website, for example Instructions or Website help. Plugin identity and guide permissions stay the same.', 'builder-content-guide' ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="bcg_menu">';
		wp_nonce_field( 'bcg_menu' );
		echo '<label for="bcg-menu-label">' . esc_html__( 'Menu name', 'builder-content-guide' ) . '</label><input id="bcg-menu-label" name="label" value="' . esc_attr( $data['label'] ) . '" placeholder="' . esc_attr__( 'Find content', 'builder-content-guide' ) . '"><p class="description">' . esc_html__( 'Leave empty to use the translated default name. A custom name applies to this website for all readers.', 'builder-content-guide' ) . '</p><label for="bcg-menu-icon">' . esc_html__( 'Menu icon', 'builder-content-guide' ) . '</label><select id="bcg-menu-icon" name="icon">';
		foreach ( self::icons() as $icon => $label ) { echo '<option value="' . esc_attr( $icon ) . '" ' . selected( $data['icon'], $icon, false ) . '>' . esc_html( $label ) . '</option>'; }
		echo '</select><p><span aria-hidden="true" data-bcg-menu-icon class="dashicons ' . esc_attr( $data['icon'] ) . '"></span> ' . esc_html__( 'Icon preview', 'builder-content-guide' ) . '</p>';
		submit_button( __( 'Save menu appearance', 'builder-content-guide' ), 'primary', 'bcg-menu-submit' );
		echo '</form></section>';
	}
}
