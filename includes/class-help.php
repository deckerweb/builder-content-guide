<?php
/** Short reader help and optional site-scoped support contact. */
namespace Deckerweb\BuilderContentGuide;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Explains the curated guide and exposes only deliberately enabled support details. */
final class Help {
	const OPTION = 'bcg_support_contact';

	/** Return support fields with safe defaults for existing installations.
	 * @return array Site-scoped support settings; disabled by default.
	 */
	public static function contact(): array {
		$value = get_option( self::OPTION, array() );
		return array_merge( array( 'enabled' => false, 'name' => '', 'note' => '', 'email' => '', 'url' => '', 'phone' => '' ), is_array( $value ) ? $value : array() );
	}

	/** Validate and save optional contact settings; invalid input never overwrites saved settings.
	 * @param array $input Unslashed support form fields.
	 * @return bool|\WP_Error True on success, or a safe permission/validation error.
	 */
	public static function save_contact( array $input ) {
		if ( ! current_user_can( 'manage_options' ) ) { return new \WP_Error( 'bcg_forbidden', __( 'You cannot manage this guide.', 'builder-content-guide' ) ); }
		foreach ( array( 'enabled', 'name', 'note', 'email', 'url', 'phone' ) as $key ) {
			if ( isset( $input[ $key ] ) && ! is_scalar( $input[ $key ] ) ) { return new \WP_Error( 'bcg_contact_invalid', __( 'Please check the contact details.', 'builder-content-guide' ) ); }
		}
		$data = array( 'enabled' => '1' === (string) ( $input['enabled'] ?? '' ) );
		foreach ( array( 'name', 'email', 'url', 'phone' ) as $key ) { $data[ $key ] = trim( (string) ( $input[ $key ] ?? '' ) ); }
		$data['name'] = sanitize_text_field( $data['name'] );
		$data['phone'] = sanitize_text_field( $data['phone'] );
		$data['note'] = sanitize_textarea_field( $input['note'] ?? '' );
		if ( $data['email'] && ! is_email( $data['email'] ) ) { return new \WP_Error( 'bcg_contact_email', __( 'Please enter a valid email address.', 'builder-content-guide' ) ); }
		$data['email'] = sanitize_email( $data['email'] );
		if ( $data['url'] ) {
			$parts = wp_parse_url( $data['url'] );
			if ( ! is_array( $parts ) || ! in_array( $parts['scheme'] ?? '', array( 'http', 'https' ), true ) || empty( $parts['host'] ) || isset( $parts['user'] ) || isset( $parts['pass'] ) || preg_match( '/[\x00-\x20\x7f]/', $data['url'] ) ) { return new \WP_Error( 'bcg_contact_url', __( 'Please enter a full HTTP or HTTPS support address without embedded credentials.', 'builder-content-guide' ) ); }
			$data['url'] = esc_url_raw( $data['url'], array( 'http', 'https' ) );
		}
		update_option( self::OPTION, $data, false );
		return true;
	}

	/** Determine whether enabled, non-empty support information is available.
	 * @return bool Whether a support box can be displayed.
	 */
	public static function has_contact(): bool {
		$data = self::contact();
		return ! empty( $data['enabled'] ) && (bool) array_filter( array_intersect_key( $data, array_flip( array( 'name', 'note', 'email', 'url', 'phone' ) ) ) );
	}

	/** Render an optional support box only for readers and only after explicit administrator enablement.
	 * @return void
	 */
	public static function contact_box(): void {
		if ( ! current_user_can( Plugin::READ_CAP ) ) { return; }
		$data = self::contact();
		if ( ! self::has_contact() ) { return; }
		echo '<aside class="bcg-panel bcg-contact" aria-labelledby="bcg-contact-title"><h2 id="bcg-contact-title" tabindex="-1">' . esc_html__( 'Need support?', 'builder-content-guide' ) . '</h2>';
		if ( $data['name'] ) { echo '<p><strong>' . esc_html( $data['name'] ) . '</strong></p>'; }
		if ( $data['note'] ) { echo '<p>' . nl2br( esc_html( $data['note'] ) ) . '</p>'; }
		echo '<ul class="bcg-contact-links">';
		if ( $data['email'] ) { echo '<li><span>' . esc_html__( 'Email', 'builder-content-guide' ) . ': </span><a href="' . esc_url( 'mailto:' . $data['email'] ) . '">' . esc_html( $data['email'] ) . '</a></li>'; }
		if ( $data['url'] ) { echo '<li><a href="' . esc_url( $data['url'], array( 'http', 'https' ) ) . '">' . esc_html__( 'Get support', 'builder-content-guide' ) . '</a></li>'; }
		if ( $data['phone'] ) { echo '<li><span>' . esc_html__( 'Phone', 'builder-content-guide' ) . ': </span>' . esc_html( $data['phone'] ) . '</li>'; }
		echo '</ul></aside>';
	}

	/** Render the administrator support settings form without performing writes.
	 * @return void
	 */
	public static function settings_form(): void {
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		$data = self::contact();
		echo '<section class="bcg-panel bcg-support-settings"><h2>' . esc_html__( 'Support contact', 'builder-content-guide' ) . '</h2><p>' . esc_html__( 'These details are visible to everyone with guide read access. Leave them empty or disable the box to hide them.', 'builder-content-guide' ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="bcg_contact">';
		wp_nonce_field( 'bcg_contact' );
		echo '<label class="bcg-check"><input type="checkbox" name="enabled" value="1" ' . checked( $data['enabled'], true, false ) . '> ' . esc_html__( 'Show support contact to readers', 'builder-content-guide' ) . '</label>';
		foreach ( array( 'name' => __( 'Name or team', 'builder-content-guide' ), 'note' => __( 'Short support note', 'builder-content-guide' ), 'email' => __( 'Email', 'builder-content-guide' ), 'url' => __( 'Support link', 'builder-content-guide' ), 'phone' => __( 'Phone (optional)', 'builder-content-guide' ) ) as $key => $label ) {
			$id = 'bcg-contact-' . $key;
			echo '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
			if ( 'note' === $key ) { echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" rows="2">' . esc_textarea( $data[ $key ] ) . '</textarea>'; }
			else { echo '<input id="' . esc_attr( $id ) . '" type="' . esc_attr( array( 'email' => 'email', 'url' => 'url', 'phone' => 'tel' )[ $key ] ?? 'text' ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $data[ $key ] ) . '">'; }
		}
		submit_button( __( 'Save support contact', 'builder-content-guide' ), 'primary', 'bcg-contact-submit' );
		echo '</form></section>';
	}

	/** Return a concise localized FAQ shared by reader help and generated documentation.
	 * @return array<int,array<int,string>> Question and answer pairs in the current locale.
	 */
	public static function faq(): array {
		return array(
			array( __( 'Why can I not find a content item?', 'builder-content-guide' ), __( 'The guide is a selected overview. The item may not have been added or made visible yet. Try a task or everyday term, then ask your website support contact.', 'builder-content-guide' ) ),
			array( __( 'Do I edit content inside the guide?', 'builder-content-guide' ), __( 'The guide explains the building block. Edit original opens its actual editor, provided you already have permission.', 'builder-content-guide' ) ),
			array( __( 'Why is Edit original missing?', 'builder-content-guide' ), __( 'You may lack editing permission, the original may be unavailable, or the building block may be read-only. Use the support details below if they are provided.', 'builder-content-guide' ) ),
			array( __( 'What does Check usage mean?', 'builder-content-guide' ), __( 'The effect of a change has not been clearly described yet. Ask your website support contact before making the change.', 'builder-content-guide' ) ),
			array( __( 'Does the guide appear in the post editor?', 'builder-content-guide' ), __( 'Instructions are under Find content. Administrators can also open related guides from the Content guide panel in supported WordPress editors. Inserting a pattern into a post does not show an additional guide notice for the inserted copy.', 'builder-content-guide' ) ),
		);
	}

	/** Return supported source descriptions and source availability, never guide or usage counts.
	 * Availability describes the source integration, not reader editing permission or curated entries.
	 * @return array Source label, description and source availability rows.
	 */
	public static function types(): array {
		return array(
			array( 'label' => __( 'WordPress pages and posts', 'builder-content-guide' ), 'description' => __( 'Individual website pages and articles. Elementor-built pages open in Elementor when its runtime and your editing permissions are available.', 'builder-content-guide' ), 'available' => post_type_exists( 'page' ) && post_type_exists( 'post' ) ),
			array( 'label' => __( 'WordPress template parts', 'builder-content-guide' ), 'description' => __( 'Shared areas such as headers and footers, including parts supplied by the active theme. Editing takes place in the Site Editor and can affect multiple pages.', 'builder-content-guide' ), 'available' => wp_is_block_theme() || current_theme_supports( 'block-template-parts' ) ),
			array( 'label' => __( 'WordPress navigation and menus', 'builder-content-guide' ), 'description' => __( 'Block navigation opens in the Site Editor; classic menus open in the menu editor. Changing a menu label does not change the linked page title or content. Check all places where the menu is used. Block navigation editing requires a block theme; classic menu editing requires theme menu or widget support.', 'builder-content-guide' ), 'available' => post_type_exists( 'wp_navigation' ) || taxonomy_exists( 'nav_menu' ) ),
			array( 'label' => __( 'Elementor Free and Pro', 'builder-content-guide' ), 'description' => __( 'Saved templates and Elementor pages or posts. Active Pro document types also support Theme Builder templates, global widgets, popups and loop items. Inserted copies and shared template embeddings behave differently; read the usage notes. Dynamic content may need to be edited in its underlying post.', 'builder-content-guide' ), 'available' => is_callable( array( '\Elementor\Plugin', 'instance' ) ) ),
			array( 'label' => __( 'WordPress patterns', 'builder-content-guide' ), 'description' => __( 'Reusable building blocks for the block editor. User-created patterns can have their own editor; registered theme or plugin patterns may be read-only. Non-synced copies in posts are edited separately.', 'builder-content-guide' ), 'available' => post_type_exists( 'wp_block' ) ),
			array( 'label' => __( 'Bricks templates', 'builder-content-guide' ), 'description' => __( 'Templates for areas such as the header, footer or page content.', 'builder-content-guide' ), 'available' => post_type_exists( 'bricks_template' ) && is_callable( array( '\Bricks\Capabilities', 'current_user_can_use_builder' ) ) ),
			array( 'label' => __( 'GeneratePress Elements', 'builder-content-guide' ), 'description' => __( 'Additional website building blocks, such as a notice below articles. Requires an active Elements integration.', 'builder-content-guide' ), 'available' => post_type_exists( 'gp_elements' ) ),
		);
	}

	/** Render brief help for authorized occasional readers without administration controls.
	 * @return void
	 */
	public static function page(): void {
		if ( ! current_user_can( Plugin::READ_CAP ) ) { return; }
		echo '<section class="bcg-panel bcg-help"><h2>' . esc_html__( 'How to find content', 'builder-content-guide' ) . '</h2><h3>' . esc_html__( 'Three simple steps', 'builder-content-guide' ) . '</h3><ol>';
		foreach ( array( __( 'Search in Find content for your task, such as changing contact details.', 'builder-content-guide' ), __( 'Read where the building block is used and what your change affects.', 'builder-content-guide' ), __( 'Choose Edit original if you have editing permission. You will work in the original editor.', 'builder-content-guide' ) ) as $step ) { echo '<li>' . esc_html( $step ) . '</li>'; }
		echo '</ol><p><a class="button button-primary" href="' . esc_url( Admin::url() ) . '">' . esc_html__( 'Find content', 'builder-content-guide' ) . '</a></p><h3>' . esc_html__( 'What kinds of content can you find here?', 'builder-content-guide' ) . '</h3><p>' . esc_html__( 'Only items added to the guide and made visible by your website administrator appear here.', 'builder-content-guide' ) . '</p><p>' . esc_html__( 'You do not need a guide for every content item. Start with frequent maintenance tasks, common stumbling blocks and shared templates whose changes affect several places.', 'builder-content-guide' ) . '</p><div class="bcg-source-list">';
		foreach ( self::types() as $type ) {
			echo '<article><h4>' . esc_html( $type['label'] ) . '</h4><p>' . esc_html( $type['description'] ) . '</p><p class="bcg-source-status">' . esc_html( $type['available'] ? __( 'Source available on this website', 'builder-content-guide' ) : __( 'Source not available on this website', 'builder-content-guide' ) ) . '</p></article>';
		}
		echo '</div><p class="description">' . esc_html__( 'An available source does not mean a guide entry already exists or that you can edit its originals.', 'builder-content-guide' ) . '</p><h3>' . esc_html__( 'Common questions', 'builder-content-guide' ) . '</h3>';
		foreach ( self::faq() as $pair ) { echo '<details class="bcg-faq"><summary>' . esc_html( $pair[0] ) . '</summary><p>' . esc_html( $pair[1] ) . '</p></details>'; }
		echo '</section>';
		self::contact_box();
	}
}
