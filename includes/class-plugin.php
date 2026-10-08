<?php
/** Hook registration and guide permissions. */
namespace Deckerweb\BuilderContentGuide;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Keeps guide access separate from original editing permissions. */
final class Plugin {
	const READ_CAP = 'read_builder_content_guide';

	/** Register hooks without frontend assets or background tasks.
	 * @return void
	 */
	public static function boot(): void {
		load_plugin_textdomain( 'builder-content-guide', false, dirname( plugin_basename( BCG_FILE ) ) . '/languages' );
		add_action( 'init', array( Store::class, 'register' ) );
		add_action( 'init', array( Components::class, 'updater' ), 20 );
		add_filter( 'map_meta_cap', array( self::class, 'read_capability' ), 10, 4 );
		add_action( 'rest_api_init', array( self::class, 'rest' ) );
		if ( is_admin() ) { Admin::boot(); }
	}

	/** Map explicit site-scoped reader grants; never grants edit rights.
	 * @param array $caps Required primitive capabilities.
	 * @param string $cap Requested capability.
	 * @param int $user_id User being checked.
	 * @param array $args Additional object arguments (unused).
	 * @return array Required primitive capabilities.
	 */
	public static function read_capability( array $caps, string $cap, int $user_id, array $args ): array {
		if ( self::READ_CAP !== $cap ) { return $caps; }
		$user = get_userdata( $user_id );
		if ( ! $user ) { return array( 'do_not_allow' ); }
		if ( $user->has_cap( 'manage_options' ) || ! empty( $user->allcaps[ self::READ_CAP ] ) || array_intersect( $user->roles, (array) get_option( 'bcg_reader_roles', array() ) ) ) {
			return array( 'read' );
		}
		return array( 'do_not_allow' );
	}

	/** Register a read-only authenticated REST view; internal storage has no public REST route.
	 * @return void
	 */
	public static function rest(): void {
		register_rest_route( 'builder-content-guide/v1', '/entries', array(
			'methods' => 'GET', 'permission_callback' => array( self::class, 'rest_permission' ),
			'callback' => array( self::class, 'rest_entries' ),
			'args' => array(
				'search' => array( 'type' => 'string', 'validate_callback' => 'rest_validate_request_arg', 'sanitize_callback' => 'sanitize_text_field' ),
				'area' => array( 'type' => 'string', 'validate_callback' => 'rest_validate_request_arg', 'sanitize_callback' => 'sanitize_text_field' ),
			),
		) );
	}

	/** Authorize REST access using the explicit reader capability.
	 * @return bool|\WP_Error True for readers or a user-localized permission error.
	 */
	public static function rest_permission() {
		if ( current_user_can( self::READ_CAP ) ) { return true; }
		$switched = switch_to_user_locale( get_current_user_id() );
		try {
			return new \WP_Error( 'bcg_forbidden', __( 'You cannot read this guide.', 'builder-content-guide' ), array( 'status' => rest_authorization_required_code() ) );
		} finally {
			if ( $switched ) { restore_previous_locale(); }
		}
	}

	/** Translate response labels into the authenticated user's locale, restoring it afterwards.
	 * @param \WP_REST_Request $request Request with optional search and area filters.
	 * @return \WP_REST_Response Authorized guide entries with private-cache headers.
	 */
	public static function rest_entries( \WP_REST_Request $request ): \WP_REST_Response {
		$switched = switch_to_user_locale( get_current_user_id() );
		try {
			$response = new \WP_REST_Response( Store::entries( (string) $request->get_param( 'search' ), (string) $request->get_param( 'area' ) ) );
			$response->header( 'Cache-Control', 'private, no-store' );
			return $response;
		} finally {
			if ( $switched ) { restore_previous_locale(); }
		}
	}
}
