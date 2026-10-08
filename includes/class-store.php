<?php
/** Site-scoped guide storage; originals are never changed. */
namespace Deckerweb\BuilderContentGuide;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Stores private, curated explanations and constructs safe reader projections. */
final class Store {
	const TYPE = 'bcg_entry';
	const META = '_bcg_data';

	/** Register private storage with administrator-only object capabilities.
	 * @return void
	 */
	public static function register(): void {
		register_post_type( self::TYPE, array(
			'label' => 'Builder Content Guide', 'public' => false, 'publicly_queryable' => false,
			'show_ui' => false, 'show_in_rest' => false, 'rewrite' => false, 'query_var' => false,
			'supports' => array( 'title' ), 'map_meta_cap' => false,
			'capabilities' => array_fill_keys( array( 'edit_post', 'read_post', 'delete_post', 'edit_posts', 'edit_others_posts', 'publish_posts', 'read_private_posts', 'delete_posts', 'create_posts' ), 'manage_options' ),
		) );
	}

	/** Return effect labels; no usage is inferred from source type.
	 * @return array<string,string> Effect keys and localized labels.
	 */
	public static function effects(): array {
		return array( 'multiple' => __( 'Change affects multiple places', 'builder-content-guide' ), 'local' => __( 'Change affects only this place', 'builder-content-guide' ), 'unknown' => __( 'Check usage', 'builder-content-guide' ) );
	}

	/** Return stored fields with conservative defaults.
	 * @param int $id Guide entry ID.
	 * @return array Stored fields.
	 */
	public static function data( int $id ): array {
		$data = get_post_meta( $id, self::META, true );
		return array_merge( array( 'source' => '', 'original' => '', 'original_title' => '', 'purpose' => '', 'area' => '', 'effect' => 'unknown', 'impact' => '', 'usage' => '', 'note' => '', 'keywords' => '', 'steps' => '', 'example_url' => '', 'visible' => false ), is_array( $data ) ? $data : array() );
	}

	/** Save a guide only after authorization and validation. Existing unavailable references may be retained.
	 * @param int $id Existing guide ID, or zero.
	 * @param array $input Unslashed form fields.
	 * @return int|\WP_Error Saved ID or validation error.
	 */
	public static function save( int $id, array $input ) {
		if ( ! current_user_can( 'manage_options' ) ) { return new \WP_Error( 'bcg_forbidden', __( 'You cannot manage this guide.', 'builder-content-guide' ) ); }
		if ( $id && get_post_type( $id ) !== self::TYPE ) { return new \WP_Error( 'bcg_invalid', __( 'Guide entry not found.', 'builder-content-guide' ) ); }
		foreach ( array( 'name', 'reference', 'purpose', 'area', 'effect', 'impact', 'usage', 'note', 'keywords', 'steps', 'example_url', 'visible' ) as $key ) {
			if ( isset( $input[ $key ] ) && ! is_scalar( $input[ $key ] ) ) { return new \WP_Error( 'bcg_invalid', __( 'Please check the guide fields.', 'builder-content-guide' ) ); }
		}
		$example = trim( (string) ( $input['example_url'] ?? '' ) );
		if ( $example ) {
			$parts = wp_parse_url( $example );
			if ( ! is_array( $parts ) || ! in_array( $parts['scheme'] ?? '', array( 'http', 'https' ), true ) || empty( $parts['host'] ) || isset( $parts['user'] ) || isset( $parts['pass'] ) || preg_match( '/[\x00-\x20\x7f]/', $example ) ) { return new \WP_Error( 'bcg_example_url', __( 'Enter a full HTTP or HTTPS example address without embedded credentials.', 'builder-content-guide' ) ); }
		}
		$name = sanitize_text_field( $input['name'] ?? '' );
		$reference = (string) ( $input['reference'] ?? '' );
		$old = $id ? self::data( $id ) : array();
		$retained = $id && $reference === ( $old['source'] . ':' . $old['original'] );
		if ( ! $name || ! sanitize_textarea_field( $input['purpose'] ?? '' ) || ! sanitize_text_field( $input['area'] ?? '' ) || ( ! $retained && ! Sources::selectable( $reference ) ) ) {
			return new \WP_Error( 'bcg_invalid', __( 'Choose an original and provide a name, purpose and website area.', 'builder-content-guide' ) );
		}
		list( $source, $original ) = explode( ':', $reference, 2 );
		$data = array( 'source' => $source, 'original' => $original, 'visible' => isset( $input['visible'] ) && '1' === (string) $input['visible'], 'effect' => isset( self::effects()[ $input['effect'] ?? '' ] ) ? $input['effect'] : 'unknown' );
		foreach ( array( 'purpose', 'area', 'impact', 'usage', 'note', 'keywords', 'steps' ) as $key ) {
			$data[ $key ] = sanitize_textarea_field( $input[ $key ] ?? '' );
		}
		$data['example_url'] = esc_url_raw( $example, array( 'http', 'https' ) );
		$data['area'] = sanitize_text_field( $data['area'] );
		$data['keywords'] = sanitize_text_field( $data['keywords'] );
		$resolved = Sources::resolve( $data );
		$data['original_title'] = $resolved['available'] ? $resolved['title'] : ( $old['original_title'] ?? '' );
		$saved = wp_insert_post( array( 'ID' => $id, 'post_type' => self::TYPE, 'post_status' => 'publish', 'post_title' => $name ), true );
		if ( is_wp_error( $saved ) ) { return $saved; }
		update_post_meta( $saved, self::META, wp_slash( $data ) );
		return $saved;
	}

	/** Copy an existing guide as an invisible draft; never duplicate the original.
	 * @param int $id Source guide entry ID.
	 * @return int|\WP_Error New draft ID or a validation/permission error.
	 */
	public static function duplicate( int $id ) {
		if ( ! current_user_can( 'manage_options' ) ) { return new \WP_Error( 'bcg_forbidden', __( 'You cannot manage this guide.', 'builder-content-guide' ) ); }
		if ( self::TYPE !== get_post_type( $id ) || ! in_array( get_post_status( $id ), array( 'publish', 'draft' ), true ) ) { return new \WP_Error( 'bcg_invalid', __( 'Guide entry not found.', 'builder-content-guide' ) ); }
		$data = self::data( $id );
		$data['visible'] = false;
		/* translators: %s: Existing guide title. */
		$name = sprintf( __( '%s (copy)', 'builder-content-guide' ), get_the_title( $id ) );
		$copy = wp_insert_post( array( 'post_type' => self::TYPE, 'post_status' => 'draft', 'post_title' => $name ), true );
		if ( ! is_wp_error( $copy ) ) { update_post_meta( $copy, self::META, wp_slash( $data ) ); }
		return $copy;
	}

	/** Build the same authorized view for admin UI and REST. Hidden entries never reach readers.
	 * @param string $search Optional search across guide name, purpose, original title and curated keywords.
	 * @param string $area Optional exact website area.
	 * @return array List of safe guide projections, without original contents.
	 */
	public static function entries( string $search = '', string $area = '' ): array {
		if ( ! current_user_can( Plugin::READ_CAP ) ) { return array(); }
		$entries = array();
		foreach ( get_posts( array( 'post_type' => self::TYPE, 'post_status' => current_user_can( 'manage_options' ) ? array( 'publish', 'draft' ) : 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) ) as $post ) {
			$data = self::data( $post->ID );
			if ( ! current_user_can( 'manage_options' ) && ! $data['visible'] ) { continue; }
			$source = Sources::resolve( $data );
			if ( $area && $area !== $data['area'] ) { continue; }
			if ( $search && false === stripos( remove_accents( $post->post_title . ' ' . $data['purpose'] . ' ' . $source['title'] . ' ' . $data['keywords'] ), remove_accents( $search ) ) ) { continue; }
			$entries[] = array_merge( $data, $source, array( 'guide_status' => $post->post_status, 'id' => $post->ID, 'name' => $post->post_title, 'source_label' => Sources::labels()[ $data['source'] ] ?? '', 'effect_label' => self::effects()[ $data['effect'] ] ?? self::effects()['unknown'] ) );
		}
		return $entries;
	}
}
