<?php
/** Original references and conservative, live permission checks. */
namespace Deckerweb\BuilderContentGuide;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Resolves supported originals without copying their content or changing permissions. */
final class Sources {
	/** Return source labels in the current locale.
	 * @return array<string,string> Source keys and labels.
	 */
	public static function labels(): array {
		return array(
			'page' => __( 'WordPress page', 'builder-content-guide' ),
			'post' => __( 'WordPress post', 'builder-content-guide' ),
			'template_part' => __( 'WordPress template part', 'builder-content-guide' ),
			'navigation' => __( 'WordPress block navigation', 'builder-content-guide' ),
			'menu' => __( 'WordPress classic menu', 'builder-content-guide' ),
			'pattern' => __( 'WordPress pattern', 'builder-content-guide' ),
			'registered_pattern' => __( 'Registered pattern (read-only)', 'builder-content-guide' ),
			'elementor' => __( 'Elementor template', 'builder-content-guide' ),
			'bricks' => __( 'Bricks template', 'builder-content-guide' ),
			'generatepress' => __( 'GeneratePress element', 'builder-content-guide' ),
		);
	}

	/** Return the post type used by a post-backed source.
	 * @param string $source Source key.
	 * @return string Post type or an empty string for non-post sources.
	 */
	public static function post_type( string $source ): string {
		return array( 'page' => 'page', 'post' => 'post', 'navigation' => 'wp_navigation', 'template_part' => 'wp_template_part', 'elementor' => 'elementor_library', 'pattern' => 'wp_block', 'bricks' => 'bricks_template', 'generatepress' => 'gp_elements' )[ $source ] ?? '';
	}

	/** Get a supported Elementor document through its active runtime; fail closed for absent Pro types.
	 * @param int $id Original post ID.
	 * @return object|null Elementor document, or null when unsupported/unavailable.
	 */
	public static function elementor_document( int $id ): ?object {
		if ( ! is_callable( array( '\Elementor\Plugin', 'instance' ) ) ) { return null; }
		try {
			$manager = \Elementor\Plugin::instance()->documents;
			if ( ! is_object( $manager ) || ! is_callable( array( $manager, 'get' ) ) ) { return null; }
			$document = $manager->get( $id );
			if ( ! is_object( $document ) || ! is_callable( array( $document, 'get_name' ) ) ) { return null; }
			$type = (string) get_post_meta( $id, '_elementor_template_type', true );
			if ( 'elementor_library' === get_post_type( $id ) && ( ! $type || 'kit' === $type || $type !== $document->get_name() ) ) { return null; }
			return $document;
		} catch ( \Throwable $error ) {
			// An unavailable or incompatible provider must not break guide reading.
			return null;
		}
	}

	/** Return a source-specific title for list filters, including supported Elementor document types.
	 * @param string $source Source key.
	 * @param string $original Stored original identifier.
	 * @return string Localized content type label.
	 */
	public static function type_label( string $source, string $original ): string {
		if ( 'elementor' === $source ) {
			$document = self::elementor_document( absint( $original ) );
			if ( $document && is_callable( array( $document, 'get_title' ) ) ) { return wp_strip_all_tags( $document->get_title() ); }
		}
		if ( in_array( $source, array( 'page', 'post' ), true ) && 'builder' === get_post_meta( absint( $original ), '_elementor_edit_mode', true ) ) { return 'page' === $source ? __( 'Elementor page', 'builder-content-guide' ) : __( 'Elementor post', 'builder-content-guide' ); }
		return self::labels()[ $source ] ?? '';
	}

	/** Resolve an original and its authorized editor. Missing references and titles are retained.
	 * @param array $data Stored guide fields.
	 * @return array Availability, live title, status, editor and public website URL.
	 */
	public static function resolve( array $data ): array {
		$source = $data['source'] ?? '';
		$original = (string) ( $data['original'] ?? '' );
		$result = array( 'available' => false, 'title' => $data['original_title'] ?? '', 'edit_url' => '', 'website_url' => '', 'status' => __( 'Original unavailable. Ask the website administrator to check this entry.', 'builder-content-guide' ) );
		if ( 'registered_pattern' === $source ) {
			$registry = \WP_Block_Patterns_Registry::get_instance();
			if ( $registry->is_registered( $original ) ) {
				$pattern = $registry->get_registered( $original );
				$result['available'] = true;
				$result['title'] = wp_strip_all_tags( $pattern['title'] );
				$result['status'] = __( 'This registered pattern is read-only. Contact the website administrator.', 'builder-content-guide' );
			}
			return $result;
		}
		if ( 'menu' === $source ) {
			$menu = ctype_digit( $original ) ? wp_get_nav_menu_object( (int) $original ) : false;
			if ( ! $menu || is_wp_error( $menu ) ) { return $result; }
			$result['available'] = true;
			$result['title'] = $menu->name;
			if ( current_user_can( 'edit_theme_options' ) && ( current_theme_supports( 'menus' ) || current_theme_supports( 'widgets' ) ) ) { $result['edit_url'] = add_query_arg( 'menu', $menu->term_id, admin_url( 'nav-menus.php' ) ); }
		} elseif ( 'template_part' === $source ) {
			if ( ! current_theme_supports( 'block-template-parts' ) && ! wp_is_block_theme() ) { return $result; }
			$parts = explode( '//', $original, 2 );
			if ( 2 !== count( $parts ) || ! in_array( $parts[0], array( get_stylesheet(), get_template() ), true ) ) { return $result; }
			$template = get_block_template( $original, 'wp_template_part' );
			if ( ! $template || 'trash' === $template->status || 'auto-draft' === $template->status ) { return $result; }
			$result['available'] = true;
			$result['title'] = wp_strip_all_tags( $template->title );
			if ( current_user_can( 'edit_theme_options' ) && ( ! $template->wp_id || current_user_can( 'edit_post', $template->wp_id ) ) ) {
				$result['edit_url'] = add_query_arg( 'p', '/wp_template_part/' . $template->id, admin_url( 'site-editor.php' ) );
			}
		} else {
			$type = self::post_type( $source );
			if ( ! $type || ! post_type_exists( $type ) || ! ctype_digit( $original ) ) { return $result; }
			if ( 'bricks' === $source && ! is_callable( array( '\Bricks\Capabilities', 'current_user_can_use_builder' ) ) ) { return $result; }
			$post = get_post( (int) $original );
			if ( ! $post || $type !== $post->post_type || in_array( $post->post_status, array( 'trash', 'auto-draft' ), true ) ) { return $result; }
			$elementor = 'elementor' === $source || ( in_array( $source, array( 'page', 'post' ), true ) && 'builder' === get_post_meta( $post->ID, '_elementor_edit_mode', true ) );
			$document = $elementor ? self::elementor_document( $post->ID ) : null;
			if ( 'elementor' === $source && ! $document ) { return $result; }
			$result['available'] = true;
			$result['title'] = wp_strip_all_tags( $post->post_title );
			if ( in_array( $source, array( 'page', 'post' ), true ) && is_post_publicly_viewable( $post ) ) { $result['website_url'] = get_permalink( $post ); }
			$allowed = current_user_can( 'edit_post', $post->ID );
			if ( 'navigation' === $source ) { $allowed = $allowed && current_user_can( 'edit_theme_options' ) && wp_is_block_theme(); }
			if ( 'bricks' === $source ) { $allowed = $allowed && \Bricks\Capabilities::current_user_can_use_builder( $post->ID ); }
			if ( $allowed ) {
				$url = get_edit_post_link( $post->ID, 'raw' );
				if ( 'bricks' === $source ) { $url = is_callable( array( '\Bricks\Helpers', 'get_builder_edit_link' ) ) ? \Bricks\Helpers::get_builder_edit_link( $post->ID ) : ''; }
				if ( $elementor ) {
					$url = '';
					if ( $document && is_callable( array( $document, 'is_editable_by_current_user' ) ) && $document->is_editable_by_current_user() && is_callable( array( $document, 'get_edit_url' ) ) ) { $url = $document->get_edit_url(); }
				}
				$result['edit_url'] = $url ?: '';
			}
		}
		$result['status'] = $result['edit_url'] ? __( 'Open the original to make your change.', 'builder-content-guide' ) : __( 'Editing requires permission for the original. Contact the website administrator.', 'builder-content-guide' );
		if ( ( 'navigation' === $source && ! wp_is_block_theme() ) || ( 'menu' === $source && ! current_theme_supports( 'menus' ) && ! current_theme_supports( 'widgets' ) ) ) { $result['status'] = __( 'The active theme does not support this menu editor. Ask the website administrator to check the theme and menu type.', 'builder-content-guide' ); }
		if ( ! empty( $data['example_url'] ) ) { $result['website_url'] = $data['example_url']; }
		return $result;
	}

	/** List supported originals with metadata for deliberate administrator selection, without importing content.
	 * @return array<string,array> Reference mapped to title, source, type and publication status.
	 */
	public static function catalog(): array {
		if ( ! current_user_can( 'manage_options' ) ) { return array(); }
		$catalog = array();
		foreach ( array( 'page', 'post', 'navigation', 'pattern', 'elementor', 'bricks', 'generatepress' ) as $source ) {
			$type = self::post_type( $source );
			if ( ! post_type_exists( $type ) ) { continue; }
			if ( 'elementor' === $source && ! is_callable( array( '\Elementor\Plugin', 'instance' ) ) ) { continue; }
			if ( 'bricks' === $source && ! is_callable( array( '\Bricks\Capabilities', 'current_user_can_use_builder' ) ) ) { continue; }
			foreach ( get_posts( array( 'post_type' => $type, 'post_status' => array( 'publish', 'private', 'draft', 'pending', 'future' ), 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) ) as $post ) {
				if ( ! current_user_can( 'read_post', $post->ID ) || ( 'elementor' === $source && ! self::elementor_document( $post->ID ) ) ) { continue; }
				$catalog[ $source . ':' . $post->ID ] = array( 'title' => wp_strip_all_tags( $post->post_title ), 'source' => $source, 'type' => self::type_label( $source, (string) $post->ID ), 'status' => get_post_status_object( $post->post_status )->label );
			}
		}
		if ( wp_is_block_theme() || current_theme_supports( 'block-template-parts' ) ) {
			foreach ( get_block_templates( array(), 'wp_template_part' ) as $template ) {
				if ( in_array( $template->status, array( 'trash', 'auto-draft' ), true ) ) { continue; }
				$status = get_post_status_object( $template->status );
				$catalog[ 'template_part:' . $template->id ] = array( 'title' => wp_strip_all_tags( $template->title ), 'source' => 'template_part', 'type' => self::labels()['template_part'], 'status' => $status ? $status->label : $template->status );
			}
		}
		foreach ( wp_get_nav_menus() as $menu ) { $catalog[ 'menu:' . $menu->term_id ] = array( 'title' => $menu->name, 'source' => 'menu', 'type' => self::labels()['menu'], 'status' => __( 'Existing menu', 'builder-content-guide' ) ); }
		foreach ( \WP_Block_Patterns_Registry::get_instance()->get_all_registered() as $pattern ) {
			$catalog[ 'registered_pattern:' . $pattern['name'] ] = array( 'title' => wp_strip_all_tags( $pattern['title'] ), 'source' => 'registered_pattern', 'type' => self::labels()['registered_pattern'], 'status' => __( 'Read-only', 'builder-content-guide' ) );
		}
		foreach ( $catalog as $reference => &$item ) {
			$item['provider'] = in_array( $item['source'], array( 'elementor', 'bricks', 'generatepress' ), true ) ? $item['source'] : 'wordpress';
			if ( in_array( $item['source'], array( 'page', 'post' ), true ) && 'builder' === get_post_meta( absint( explode( ':', $reference, 2 )[1] ), '_elementor_edit_mode', true ) ) { $item['provider'] = 'elementor'; }
		}
		unset( $item );
		return $catalog;
	}

	/** Validate one selectable original directly instead of loading the entire catalog on each save.
	 * @param string $reference Canonical source and original identifier separated by a colon.
	 * @return bool Whether this administrator may deliberately select the existing original.
	 */
	public static function selectable( string $reference ): bool {
		if ( ! current_user_can( 'manage_options' ) || ! str_contains( $reference, ':' ) ) { return false; }
		list( $source, $original ) = explode( ':', $reference, 2 );
		if ( ! isset( self::labels()[ $source ] ) ) { return false; }
		if ( ! in_array( $source, array( 'registered_pattern', 'template_part' ), true ) && ( ! ctype_digit( $original ) || (string) absint( $original ) !== $original ) ) { return false; }
		if ( ! self::resolve( array( 'source' => $source, 'original' => $original ) )['available'] ) { return false; }
		if ( in_array( $source, array( 'registered_pattern', 'template_part', 'menu' ), true ) ) { return true; }
		return current_user_can( 'read_post', (int) $original ) && in_array( get_post_status( (int) $original ), array( 'publish', 'private', 'draft', 'pending', 'future' ), true );
	}

	/** List original reference labels for validation and non-JavaScript selection.
	 * @param array|null $catalog Optional catalog already loaded for this form.
	 * @return array<string,string> Reference values mapped to readable labels.
	 */
	public static function choices( ?array $catalog = null ): array {
		$choices = array();
		foreach ( $catalog ?? self::catalog() as $reference => $item ) { $choices[ $reference ] = $item['title'] . ' · ' . array( 'wordpress' => 'WordPress', 'elementor' => 'Elementor', 'bricks' => 'Bricks', 'generatepress' => 'GeneratePress' )[ $item['provider'] ] . ' · ' . $item['type'] . ' · ' . $item['status'] . ' (' . $reference . ')'; }
		return $choices;
	}

	/** Find the canonical reference for a supported post, including theme-based template identifiers.
	 * @param \WP_Post $post Original post.
	 * @return string Reference or an empty string for unsupported originals.
	 */
	public static function reference_for_post( \WP_Post $post ): string {
		if ( 'wp_template_part' === $post->post_type ) {
			foreach ( get_block_templates( array(), 'wp_template_part' ) as $template ) { if ( (int) $template->wp_id === $post->ID ) { return 'template_part:' . $template->id; } }
			return '';
		}
		foreach ( self::labels() as $source => $label ) { if ( self::post_type( $source ) === $post->post_type ) { return $source . ':' . $post->ID; } }
		return '';
	}
}
