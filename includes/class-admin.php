<?php
/** WordPress admin guide and curation forms. */
namespace Deckerweb\BuilderContentGuide;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Renders the task list, details, explicit access grants and administration. */
final class Admin {
	/** @var string[] Registered guide screen hooks for localized menu names. */
	private static array $screens = array();
	/** Register admin hooks.
	 * @return void
	 */
	public static function boot(): void {
		add_action( 'admin_menu', array( self::class, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( self::class, 'assets' ) );
		add_action( 'admin_enqueue_scripts', array( self::class, 'editor_assets' ) );
		add_action( 'admin_post_bcg_save', array( self::class, 'save' ) );
		add_action( 'admin_post_bcg_delete', array( self::class, 'delete' ) );
		add_action( 'admin_post_bcg_restore', array( self::class, 'restore' ) );
		add_action( 'admin_post_bcg_access', array( self::class, 'access' ) );
		add_action( 'admin_post_bcg_menu', array( self::class, 'menu_settings' ) );
		add_action( 'admin_post_bcg_duplicate', array( self::class, 'duplicate' ) );
		add_action( 'add_meta_boxes', array( self::class, 'original_box' ), 10, 2 );
		add_action( 'admin_notices', array( self::class, 'menu_notice' ) );
		add_action( 'admin_bar_menu', array( self::class, 'editor_toolbar' ), 90 );
		add_filter( 'page_row_actions', array( self::class, 'original_action' ), 10, 2 );
		add_action( 'admin_post_bcg_contact', array( self::class, 'contact' ) );
		add_filter( 'post_row_actions', array( self::class, 'original_action' ), 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( BCG_FILE ), array( self::class, 'action_links' ) );
	}

	/** Add the guide menu available only to explicit readers and administrators.
	 * @return void
	 */
	public static function menu(): void {
		$appearance = Settings::menu();
		$label = $appearance['label'] ?: __( 'Find content', 'builder-content-guide' );
		self::$screens[] = add_menu_page( $label, $label, Plugin::READ_CAP, 'builder-content-guide', array( self::class, 'page' ), $appearance['icon'], 26 );
		self::$screens[] = add_submenu_page( 'builder-content-guide', __( 'Find content', 'builder-content-guide' ), __( 'Find content', 'builder-content-guide' ), Plugin::READ_CAP, 'builder-content-guide', array( self::class, 'page' ) );
		self::$screens[] = add_submenu_page( 'builder-content-guide', __( 'Manage guide', 'builder-content-guide' ), __( 'Manage guide', 'builder-content-guide' ), 'manage_options', 'builder-content-guide-manage', array( self::class, 'page' ) );
		self::$screens[] = add_submenu_page( 'builder-content-guide', __( 'Add guide entry', 'builder-content-guide' ), __( 'Add guide entry', 'builder-content-guide' ), 'manage_options', 'builder-content-guide-add', array( self::class, 'page' ) );
		self::$screens[] = add_submenu_page( 'builder-content-guide', __( 'How to find content', 'builder-content-guide' ), __( 'How to find content', 'builder-content-guide' ), Plugin::READ_CAP, 'builder-content-guide-help', array( self::class, 'page' ) );
	}

	/** Load local styles and dialog behavior only on this plugin's admin screen.
	 * @param string $hook Current admin screen hook.
	 * @return void
	 */
	public static function assets( string $hook ): void {
		if ( ! in_array( $hook, self::$screens, true ) ) { return; }
		wp_enqueue_style( 'bcg-admin', plugins_url( 'assets/admin.css', BCG_FILE ), array(), '1.0.0' );
		wp_enqueue_script( 'bcg-admin', plugins_url( 'assets/admin.js', BCG_FILE ), array(), '1.0.0', true );
	}

	/** Add a native guide panel to supported WordPress block editors, including full-screen Site Editor.
	 * @return void
	 */
	public static function editor_assets(): void {
		$screen = get_current_screen();
		if ( ! current_user_can( 'manage_options' ) || ! $screen || ! $screen->is_block_editor() || ! in_array( $GLOBALS['pagenow'] ?? '', array( 'post.php', 'post-new.php', 'site-editor.php' ), true ) ) { return; }
		wp_enqueue_script( 'bcg-editor', plugins_url( 'assets/editor.js', BCG_FILE ), array( 'wp-plugins', 'wp-element', 'wp-data', 'wp-editor', 'wp-components' ), '1.0.0', true );
		wp_localize_script( 'bcg-editor', 'bcgEditorGuide', array( 'title' => __( 'Content guide', 'builder-content-guide' ), 'add' => __( 'Add guide entry', 'builder-content-guide' ), 'related' => __( 'Related guides', 'builder-content-guide' ), 'addUrl' => self::url( array( 'view' => 'edit' ) ), 'readUrl' => self::url() ) );
	}

	/** Build the guide URL with optional parameters.
	 * @param array $args Query parameters.
	 * @return string Local admin URL.
	 */
	public static function url( array $args = array() ): string {
		$page = 'builder-content-guide';
		if ( 'manage' === ( $args['view'] ?? '' ) ) { $page = 'builder-content-guide-manage'; unset( $args['view'] ); }
		elseif ( 'help' === ( $args['view'] ?? '' ) ) { $page = 'builder-content-guide-help'; unset( $args['view'] ); }
		elseif ( 'edit' === ( $args['view'] ?? '' ) ) { $page = 'builder-content-guide-add'; unset( $args['view'] ); }
		return add_query_arg( array_merge( array( 'page' => $page ), $args ), admin_url( 'admin.php' ) );
	}

	/** Prepend the authorized settings action to the plugin list.
	 * @param array $links Existing plugin actions.
	 * @return array Updated actions.
	 */
	public static function action_links( array $links ): array {
		if ( current_user_can( 'manage_options' ) ) {
			array_unshift( $links, '<a href="' . esc_url( self::url( array( 'view' => 'manage' ) ) ) . '">' . esc_html__( 'Settings', 'builder-content-guide' ) . '</a>' );
		}
		return $links;
	}

	/** Offer deliberate linking at supported original post rows.
	 * @param array $actions Existing row actions.
	 * @param \WP_Post $post Original post.
	 * @return array Updated actions.
	 */
	public static function original_action( array $actions, \WP_Post $post ): array {
		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'read_post', $post->ID ) ) { return $actions; }
		$reference = Sources::reference_for_post( $post );
		if ( $reference && Sources::resolve( array( 'source' => explode( ':', $reference, 2 )[0], 'original' => explode( ':', $reference, 2 )[1] ) )['available'] ) {
			$actions['bcg'] = self::original_links( $reference );
		}
		return $actions;
	}

	/** Build entry links for an authorized original; related hidden guides remain administrator-only.
	 * @param string $reference Canonical source reference.
	 * @return string Escaped navigation links.
	 */
	public static function original_links( string $reference ): string {
		if ( ! current_user_can( 'manage_options' ) ) { return ''; }
		$links = '<a href="' . esc_url( self::url( array( 'view' => 'edit', 'reference' => $reference ) ) ) . '">' . esc_html__( 'Add guide entry', 'builder-content-guide' ) . '</a>';
		foreach ( get_posts( array( 'post_type' => Store::TYPE, 'post_status' => array( 'publish', 'draft' ), 'numberposts' => -1 ) ) as $guide ) {
			$entry = Store::data( $guide->ID );
			if ( $reference === $entry['source'] . ':' . $entry['original'] ) {
				$links .= ' · <a href="' . esc_url( self::url( array( 'reference' => $reference ) ) ) . '">' . esc_html__( 'Related guides', 'builder-content-guide' ) . '</a>';
				break;
			}
		}
		return $links;
	}

	/** Add a guide metabox to supported native post editors.
	 * @param string $type Current post type.
	 * @param \WP_Post $post Original being edited.
	 * @return void
	 */
	public static function original_box( string $type, \WP_Post $post ): void {
		if ( ! current_user_can( 'manage_options' ) || ! Sources::reference_for_post( $post ) || use_block_editor_for_post( $post ) ) { return; }
		add_meta_box( 'bcg-original-guides', __( 'Content guide', 'builder-content-guide' ), array( self::class, 'render_original_box' ), $type, 'side' );
	}

	/** Render guide links without modifying the original when its editor is saved.
	 * @param \WP_Post $post Original post.
	 * @return void
	 */
	public static function render_original_box( \WP_Post $post ): void {
		echo '<p>' . self::original_links( Sources::reference_for_post( $post ) ) . '</p>';
	}

	/** Show links for the selected classic menu in its existing admin screen.
	 * @return void
	 */
	public static function menu_notice(): void {
		if ( 'nav-menus.php' !== ( $GLOBALS['pagenow'] ?? '' ) || ! current_user_can( 'manage_options' ) || ! current_user_can( 'edit_theme_options' ) ) { return; }
		if ( '0' === self::query( 'menu' ) ) { return; }
		$id = absint( self::query( 'menu' ) );
		if ( ! $id ) { $id = absint( get_user_option( 'nav_menu_recently_edited' ) ); }
		if ( $id && wp_get_nav_menu_object( $id ) ) { echo '<div class="notice notice-info"><p>' . esc_html__( 'Content guide', 'builder-content-guide' ) . ': ' . self::original_links( 'menu:' . $id ) . '</p></div>'; }
	}

	/** Add native toolbar links for the original currently open in Elementor or the Site Editor.
	 * @param \WP_Admin_Bar $bar Native WordPress toolbar.
	 * @return void
	 */
	public static function editor_toolbar( \WP_Admin_Bar $bar ): void {
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		$reference = '';
		if ( 'post.php' === ( $GLOBALS['pagenow'] ?? '' ) ) {
			$post = get_post( absint( self::query( 'post' ) ) );
			if ( $post ) { $reference = Sources::reference_for_post( $post ); }
		} elseif ( 'site-editor.php' === ( $GLOBALS['pagenow'] ?? '' ) ) {
			$route = self::query( 'p' );
			if ( str_starts_with( $route, '/wp_template_part/' ) ) { $reference = 'template_part:' . substr( $route, strlen( '/wp_template_part/' ) ); }
			elseif ( str_starts_with( $route, '/wp_navigation/' ) ) { $reference = 'navigation:' . substr( $route, strlen( '/wp_navigation/' ) ); }
			elseif ( in_array( self::query( 'postType' ), array( 'wp_template_part', 'wp_navigation' ), true ) ) { $reference = ( 'wp_navigation' === self::query( 'postType' ) ? 'navigation:' : 'template_part:' ) . self::query( 'postId' ); }
		}
		if ( ! $reference ) { return; }
		list( $source, $original ) = explode( ':', $reference, 2 );
		if ( ! Sources::resolve( array( 'source' => $source, 'original' => $original ) )['available'] ) { return; }
		$bar->add_node( array( 'id' => 'bcg-guide', 'title' => __( 'Content guide', 'builder-content-guide' ), 'href' => self::url( array( 'reference' => $reference ) ) ) );
		$bar->add_node( array( 'id' => 'bcg-guide-add', 'parent' => 'bcg-guide', 'title' => __( 'Add guide entry', 'builder-content-guide' ), 'href' => self::url( array( 'view' => 'edit', 'reference' => $reference ) ) ) );
	}

	/** Read a scalar query parameter safely.
	 * @param string $key Parameter name.
	 * @return string Sanitized query value.
	 */
	private static function query( string $key ): string {
		return isset( $_GET[ $key ] ) && is_string( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : '';
	}

	/** Render the authorized guide screen; no writes occur during GET requests.
	 * @return void
	 */
	public static function page(): void {
		if ( ! current_user_can( Plugin::READ_CAP ) ) { wp_die( esc_html__( 'You cannot read this guide.', 'builder-content-guide' ), '', array( 'response' => 403 ) ); }
		$manager = current_user_can( 'manage_options' );
		$view = self::query( 'view' );
		$page = self::query( 'page' );
		if ( 'builder-content-guide-manage' === $page ) { $view = 'manage'; }
		elseif ( 'builder-content-guide-add' === $page ) { $view = 'edit'; }
		elseif ( 'builder-content-guide-help' === $page ) { $view = 'help'; }
		echo '<div class="wrap bcg"><header><h1>Builder Content Guide</h1><p>' . esc_html__( 'Find the right building block and understand what your change affects.', 'builder-content-guide' ) . '</p></header>';
		if ( $manager ) {
			echo '<nav class="bcg-nav" aria-label="' . esc_attr__( 'Guide views', 'builder-content-guide' ) . '"><a class="button" href="' . esc_url( self::url() ) . '">' . esc_html__( 'Find content', 'builder-content-guide' ) . '</a> <a class="button" href="' . esc_url( self::url( array( 'view' => 'manage' ) ) ) . '">' . esc_html__( 'Manage guide', 'builder-content-guide' ) . '</a></nav>';
		}
		if ( 'help' !== $view ) { echo '<p class="bcg-help-link"><a href="' . esc_url( self::url( array( 'view' => 'help' ) ) ) . '">' . esc_html__( 'How to find content', 'builder-content-guide' ) . '</a></p>'; }
		if ( 'help' === $view ) {
			Help::page();
		} elseif ( $manager && 'edit' === $view ) {
			self::form( absint( self::query( 'entry' ) ) );
		} elseif ( $manager && 'manage' === $view ) {
			self::manage();
		} else {
			self::reader( $manager );
		}
		self::footer();
		echo '</div>';
	}

	/** Render search, area filter, task list and a selected detail panel.
	 * @param bool $manager Whether administrative annotations are available.
	 * @return void
	 */
	private static function reader( bool $manager ): void {
		$search = self::query( 'search' ); $area = self::query( 'area' );
		$all = Store::entries(); $areas = array_unique( array_column( $all, 'area' ) ); sort( $areas );
		$entries = Store::entries( $search, $area );
		$reference = self::query( 'reference' );
		if ( $reference ) { $entries = array_values( array_filter( $entries, static function ( $entry ) use ( $reference ) { return $reference === $entry['source'] . ':' . $entry['original']; } ) ); }
		$selected = absint( self::query( 'entry' ) );
		echo '<form method="get" class="bcg-toolbar"><input type="hidden" name="page" value="builder-content-guide"><input type="hidden" name="reference" value="' . esc_attr( $reference ) . '"><label>' . esc_html__( 'What would you like to change?', 'builder-content-guide' ) . '<input type="search" name="search" value="' . esc_attr( $search ) . '"></label><label>' . esc_html__( 'Website area', 'builder-content-guide' ) . '<select name="area"><option value="">' . esc_html__( 'All areas', 'builder-content-guide' ) . '</option>';
		foreach ( $areas as $value ) { echo '<option value="' . esc_attr( $value ) . '" ' . selected( $area, $value, false ) . '>' . esc_html( $value ) . '</option>'; }
		echo '</select></label><button class="button">' . esc_html__( 'Find', 'builder-content-guide' ) . '</button></form>';
		if ( $reference ) { echo '<p>' . esc_html__( 'Related guides', 'builder-content-guide' ) . ' · <a href="' . esc_url( self::url() ) . '">' . esc_html__( 'Show all guide entries', 'builder-content-guide' ) . '</a></p>'; }
		if ( ! $entries ) {
			echo '<section class="bcg-panel bcg-empty"><h2>' . esc_html__( 'No guide entries found.', 'builder-content-guide' ) . '</h2><p>' . esc_html__( 'Try a shorter task or everyday term, or choose All areas. Your website administrator may still need to add or release the item.', 'builder-content-guide' ) . '</p><p><a class="button" href="' . esc_url( self::url( array( 'view' => 'help' ) ) ) . '">' . esc_html__( 'How to find content', 'builder-content-guide' ) . '</a> <a href="' . esc_url( self::url() ) . '">' . esc_html__( 'Show all guide entries', 'builder-content-guide' ) . '</a></p></section>';
			Help::contact_box(); return;
		}
		if ( $selected && ! in_array( $selected, array_column( $entries, 'id' ), true ) ) {
			echo '<section class="bcg-panel"><h2>' . esc_html__( 'This guide is not available in the current view.', 'builder-content-guide' ) . '</h2><p>' . esc_html__( 'It may be hidden, removed or excluded by your filters. Ask your website support contact if you need this guide.', 'builder-content-guide' ) . '</p><a href="' . esc_url( self::url() ) . '">' . esc_html__( 'Show all guide entries', 'builder-content-guide' ) . '</a></section>';
			Help::contact_box(); return;
		}
		$detail = $entries[0];
		$active = in_array( $selected, array_column( $entries, 'id' ), true ) ? $selected : $entries[0]['id'];
		echo '<div class="bcg-columns"><ul class="bcg-tasks" aria-label="' . esc_attr__( 'Maintenance tasks', 'builder-content-guide' ) . '">';
		foreach ( $entries as $entry ) {
			if ( $selected === $entry['id'] ) { $detail = $entry; }
			echo '<li><a ' . ( $entry['id'] === $active ? 'aria-current="true" ' : '' ) . 'href="' . esc_url( self::url( array( 'entry' => $entry['id'], 'search' => $search, 'area' => $area, 'reference' => $reference ) ) ) . '"><strong>' . esc_html( $entry['name'] ) . '</strong><span>' . esc_html( $entry['purpose'] ) . '</span><small>' . esc_html( $entry['area'] . ' · ' . $entry['source_label'] ) . '</small></a>';
			if ( $manager && ( ! $entry['available'] || ! $entry['visible'] ) ) { echo '<span class="bcg-status">' . esc_html( ! $entry['available'] ? __( 'Needs review: original unavailable', 'builder-content-guide' ) : __( 'Hidden from readers', 'builder-content-guide' ) ) . '</span>'; }
			echo '</li>';
		}
		echo '</ul><section class="bcg-detail" aria-labelledby="bcg-detail-title"><h2 id="bcg-detail-title">' . esc_html( $detail['name'] ) . '</h2><p>' . esc_html( $detail['purpose'] ) . '</p><p><strong>' . esc_html( $detail['effect_label'] ) . '</strong></p><dl>';
		foreach ( array( 'area' => __( 'Website area', 'builder-content-guide' ), 'impact' => __( 'Effect of a change', 'builder-content-guide' ), 'usage' => __( 'Where it is used', 'builder-content-guide' ), 'note' => __( 'Editing guidance', 'builder-content-guide' ), 'title' => __( 'Original name', 'builder-content-guide' ), 'source_label' => __( 'Source', 'builder-content-guide' ) ) as $key => $label ) {
			echo '<dt>' . esc_html( $label ) . '</dt><dd>' . nl2br( esc_html( $detail[ $key ] ) ) . '</dd>';
		}
		echo '</dl>';
		if ( $detail['steps'] ) {
			echo '<h3>' . esc_html__( 'Step by step', 'builder-content-guide' ) . '</h3><ol class="bcg-steps">';
			foreach ( preg_split( '/\r\n|\r|\n/', $detail['steps'] ) as $step ) { if ( trim( $step ) ) { echo '<li>' . esc_html( $step ) . '</li>'; } }
			echo '</ol>';
		}
		echo '<p class="description">' . esc_html__( 'Usage and effects are described manually by the website administrator.', 'builder-content-guide' ) . '</p><p>' . esc_html( $detail['status'] ) . '</p>';
		if ( $detail['edit_url'] ) {
			if ( 'multiple' === $detail['effect'] ) { echo '<p class="bcg-impact-reminder"><strong>' . esc_html__( 'This building block is used in multiple places. Read the effect and usage notes before editing.', 'builder-content-guide' ) . '</strong></p>'; }
			elseif ( 'unknown' === $detail['effect'] ) { echo '<p class="bcg-impact-reminder">' . esc_html__( 'Check usage before editing. If unsure, ask your website support contact.', 'builder-content-guide' ) . '</p>'; }
			echo '<a class="button button-primary" href="' . esc_url( $detail['edit_url'] ) . '">' . esc_html__( 'Edit original', 'builder-content-guide' ) . '</a>';
		} else {
			echo '<a class="button" href="' . esc_url( self::url( array( 'view' => 'help' ) ) . ( Help::has_contact() ? '#bcg-contact-title' : '' ) ) . '">' . esc_html__( 'Get help', 'builder-content-guide' ) . '</a>';
		}
		if ( $detail['website_url'] ) { echo ' <a class="button" href="' . esc_url( $detail['website_url'], array( 'http', 'https' ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'View website', 'builder-content-guide' ) . ' <span class="screen-reader-text">' . esc_html__( '(opens in a new tab)', 'builder-content-guide' ) . '</span></a>'; }
		echo '<div class="bcg-share"><label for="bcg-guide-link">' . esc_html__( 'Direct link to this guide', 'builder-content-guide' ) . '</label><input id="bcg-guide-link" readonly value="' . esc_attr( self::url( array( 'entry' => $detail['id'] ) ) ) . '"><button type="button" class="button" data-bcg-copy>' . esc_html__( 'Copy guide link', 'builder-content-guide' ) . '</button><span role="status" data-bcg-copy-status data-success="' . esc_attr__( 'Link copied.', 'builder-content-guide' ) . '" data-fallback="' . esc_attr__( 'Select and copy the link above.', 'builder-content-guide' ) . '"></span><p class="description">' . esc_html__( 'The recipient needs guide read access. Copying a link does not grant access.', 'builder-content-guide' ) . '</p></div>';
		if ( $manager ) { echo ' <a class="button" href="' . esc_url( self::url( array( 'view' => 'edit', 'entry' => $detail['id'] ) ) ) . '">' . esc_html__( 'Edit guide entry', 'builder-content-guide' ) . '</a>'; }
		echo '</section></div>';
	}

	/** Render the curation overview and explicit site-scoped access settings.
	 * @return void
	 */
	private static function manage(): void {
		echo '<h2>' . esc_html__( 'Manage guide', 'builder-content-guide' ) . '</h2><p>' . esc_html__( 'You do not need a guide for every content item. Start with frequent maintenance tasks, common stumbling blocks and shared templates whose changes affect several places.', 'builder-content-guide' ) . '</p><p><a class="button button-primary" href="' . esc_url( self::url( array( 'view' => 'edit' ) ) ) . '">' . esc_html__( 'Add guide entry', 'builder-content-guide' ) . '</a></p><ul class="bcg-tasks">';
		foreach ( Store::entries() as $entry ) {
			echo '<li><a href="' . esc_url( self::url( array( 'view' => 'edit', 'entry' => $entry['id'] ) ) ) . '"><strong>' . esc_html( $entry['name'] ) . '</strong><span>' . esc_html( $entry['source_label'] . ' · ' . $entry['title'] ) . '</span></a><span>' . esc_html( $entry['visible'] ? __( 'Visible to readers', 'builder-content-guide' ) : __( 'Hidden from readers', 'builder-content-guide' ) ) . '</span>';
			if ( 'draft' === $entry['guide_status'] ) { echo '<p>' . esc_html__( 'Draft', 'builder-content-guide' ) . '</p>'; }
			if ( ! $entry['available'] ) { echo '<p class="bcg-status">' . esc_html__( 'Needs review: original unavailable', 'builder-content-guide' ) . '</p>'; }
			echo '</li>';
		}
		echo '</ul>';
		foreach ( get_posts( array( 'post_type' => Store::TYPE, 'post_status' => 'trash', 'numberposts' => -1 ) ) as $trashed ) {
			echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="bcg_restore"><input type="hidden" name="entry" value="' . esc_attr( $trashed->ID ) . '"><p>' . esc_html( $trashed->post_title ) . ' · ' . esc_html__( 'In trash', 'builder-content-guide' ) . '</p>';
			wp_nonce_field( 'bcg_restore_' . $trashed->ID );
			submit_button( __( 'Restore guide entry', 'builder-content-guide' ), 'secondary', 'bcg-restore-entry-' . $trashed->ID ); echo '</form>';
		}
		echo '<section class="bcg-panel"><h2>' . esc_html__( 'Guide access', 'builder-content-guide' ) . '</h2><p>' . esc_html__( 'Grant read access deliberately. This does not grant editing rights for originals. Administrators can always manage this guide.', 'builder-content-guide' ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="bcg_access">';
		wp_nonce_field( 'bcg_access' );
		$granted = (array) get_option( 'bcg_reader_roles', array() );
		foreach ( wp_roles()->roles as $key => $role ) {
			if ( ! empty( $role['capabilities']['manage_options'] ) ) { continue; }
			echo '<label class="bcg-check"><input type="checkbox" name="roles[]" value="' . esc_attr( $key ) . '" ' . checked( in_array( $key, $granted, true ), true, false ) . '> ' . esc_html( translate_user_role( $role['name'] ) ) . '</label>';
		}
		submit_button( __( 'Save access', 'builder-content-guide' ) );
		echo '</form></section>';
		if ( '1' === self::query( 'contact_saved' ) ) { echo '<p role="status">' . esc_html__( 'Support contact saved.', 'builder-content-guide' ) . '</p>'; }
		Help::settings_form();
		if ( '1' === self::query( 'menu_saved' ) ) { echo '<p role="status">' . esc_html__( 'Menu appearance saved.', 'builder-content-guide' ) . '</p>'; }
		Settings::menu_form();
	}

	/** Render the entry form; preserves unavailable original references on editing.
	 * @param int $id Existing guide entry ID or zero.
	 * @return void
	 */
	private static function form( int $id ): void {
		if ( $id && get_post_type( $id ) !== Store::TYPE ) { wp_die( esc_html__( 'Guide entry not found.', 'builder-content-guide' ), '', array( 'response' => 404 ) ); }
		if ( '1' === self::query( 'duplicated' ) ) { echo '<p role="status">' . esc_html__( 'Guide copied as a draft and hidden from readers. Review it before sharing.', 'builder-content-guide' ) . '</p>'; }
		$data = Store::data( $id ); $catalog = Sources::catalog(); $choices = Sources::choices( $catalog );
		$reference = $id ? $data['source'] . ':' . $data['original'] : self::query( 'reference' );
		if ( $id && ! isset( $choices[ $reference ] ) ) { $choices[ $reference ] = __( 'Original unavailable', 'builder-content-guide' ) . ' · ' . $data['original_title'] . ' (' . $reference . ')'; }
		echo '<section class="bcg-panel"><h2>' . esc_html__( 'Guide entry', 'builder-content-guide' ) . '</h2><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="bcg_save"><input type="hidden" name="entry" value="' . esc_attr( $id ) . '">';
		wp_nonce_field( 'bcg_save_' . $id );
		self::original_picker( $catalog );
		echo '<label for="bcg-reference">' . esc_html__( 'Original', 'builder-content-guide' ) . '</label><select id="bcg-reference" name="reference" required size="8"><option value="">' . esc_html__( 'Choose an original', 'builder-content-guide' ) . '</option>';
		foreach ( $choices as $value => $label ) { echo '<option value="' . esc_attr( $value ) . '" ' . selected( $reference, $value, false ) . '>' . esc_html( $label ) . '</option>'; }
		echo '</select>';
		echo '<p role="status" data-bcg-picker-status data-empty="' . esc_attr__( 'No matching originals. Change the search or filters.', 'builder-content-guide' ) . '"></p>';
		self::field( 'name', __( 'Name for readers', 'builder-content-guide' ), $id ? get_the_title( $id ) : '', false, true );
		echo '<p class="description">' . esc_html__( 'Use a task name, such as Change the phone number in the footer.', 'builder-content-guide' ) . '</p>';
		self::field( 'keywords', __( 'Additional search terms', 'builder-content-guide' ), $data['keywords'] );
		echo '<p class="description">' . esc_html__( 'For example: phone, contact, address. These terms help readers find this task.', 'builder-content-guide' ) . '</p>';
		foreach ( array( 'purpose' => __( 'Purpose', 'builder-content-guide' ), 'area' => __( 'Website area', 'builder-content-guide' ) ) as $key => $label ) { self::field( $key, $label, $data[ $key ], 'purpose' === $key, true ); }
		echo '<label>' . esc_html__( 'Effect of a change', 'builder-content-guide' ) . '<select name="effect">';
		foreach ( Store::effects() as $value => $label ) { echo '<option value="' . esc_attr( $value ) . '" ' . selected( $data['effect'], $value, false ) . '>' . esc_html( $label ) . '</option>'; }
		echo '</select></label>';
		foreach ( array( 'impact' => __( 'Explain the effect', 'builder-content-guide' ), 'usage' => __( 'Where it is used', 'builder-content-guide' ), 'note' => __( 'Editing guidance', 'builder-content-guide' ) ) as $key => $label ) { self::field( $key, $label, $data[ $key ], true ); }
		self::field( 'steps', __( 'Step by step', 'builder-content-guide' ), $data['steps'], true );
		echo '<p class="description">' . esc_html__( 'One step per line. Example: Select the contact block, replace the phone number, save, then check the website.', 'builder-content-guide' ) . '</p>';
		self::field( 'example_url', __( 'Example page address (optional)', 'builder-content-guide' ), $data['example_url'] );
		echo '<p class="description">' . esc_html__( 'Use a full HTTP or HTTPS address of a checked example page. Public pages and posts use their own address when this field is empty.', 'builder-content-guide' ) . '</p>';
		echo '<label class="bcg-check"><input type="checkbox" name="visible" value="1" ' . checked( $data['visible'], true, false ) . '> ' . esc_html__( 'Visible to readers', 'builder-content-guide' ) . '</label>';
		submit_button( __( 'Save guide entry', 'builder-content-guide' ), 'primary', 'bcg-save-entry' );
		echo '</form>';
		if ( $id ) {
			self::duplicate_form( $id );
			echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="bcg_delete"><input type="hidden" name="entry" value="' . esc_attr( $id ) . '">';
			wp_nonce_field( 'bcg_delete_' . $id );
			submit_button( __( 'Move guide entry to trash', 'builder-content-guide' ), 'secondary', 'bcg-trash-entry' ); echo '</form>';
		}
		echo '</section>';
	}

	/** Render accessible filters that progressively enhance the native original selector.
	 * @param array $catalog Original metadata already loaded for this form.
	 * @return void
	 */
	private static function original_picker( array $catalog ): void {
		$types = array_unique( array_column( $catalog, 'type' ) ); sort( $types );
		echo '<fieldset class="bcg-picker" hidden><legend>' . esc_html__( 'Find an original', 'builder-content-guide' ) . '</legend><label for="bcg-original-search">' . esc_html__( 'Search originals', 'builder-content-guide' ) . '</label><input type="search" id="bcg-original-search"><div class="bcg-picker-filters"><label for="bcg-original-provider">' . esc_html__( 'Provider', 'builder-content-guide' ) . '</label><select id="bcg-original-provider"><option value="">' . esc_html__( 'All providers', 'builder-content-guide' ) . '</option>';
		foreach ( array( 'wordpress' => 'WordPress', 'elementor' => 'Elementor', 'bricks' => 'Bricks', 'generatepress' => 'GeneratePress' ) as $key => $label ) { echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>'; }
		echo '</select><label for="bcg-original-type">' . esc_html__( 'Content type', 'builder-content-guide' ) . '</label><select id="bcg-original-type"><option value="">' . esc_html__( 'All content types', 'builder-content-guide' ) . '</option>';
		foreach ( $types as $type ) { echo '<option value="' . esc_attr( $type ) . '">' . esc_html( $type ) . '</option>'; }
		echo '</select></div><p class="description">' . esc_html__( 'Filtering keeps your selected original. Select a result to change the assignment.', 'builder-content-guide' ) . '</p></fieldset>';
		echo '<script type="application/json" id="bcg-original-catalog">' . wp_json_encode( $catalog, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . '</script>';
	}

	/** Render a nonce-protected duplication form for a guide, never its original.
	 * @param int $id Guide entry ID.
	 * @return void
	 */
	private static function duplicate_form( int $id ): void {
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="bcg_duplicate"><input type="hidden" name="entry" value="' . esc_attr( $id ) . '">';
		wp_nonce_field( 'bcg_duplicate_' . $id );
		submit_button( __( 'Duplicate guide as draft', 'builder-content-guide' ), 'secondary', 'bcg-duplicate-entry' );
		echo '<p class="description">' . esc_html__( 'Only the guide is copied. Review the draft and explicitly enable reader visibility.', 'builder-content-guide' ) . '</p></form>';
	}

	/** Render an escaped labeled form field.
	 * @param string $key Field name.
	 * @param string $label Localized visible label.
	 * @param string $value Current field value.
	 * @param bool $textarea Whether to use a textarea.
	 * @param bool $required Whether the field is required.
	 * @return void
	 */
	private static function field( string $key, string $label, string $value, bool $textarea = false, bool $required = false ): void {
		$id = 'bcg-field-' . $key;
		echo '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
		if ( $textarea ) { echo '<textarea id="' . esc_attr( $id ) . '" rows="3" name="' . esc_attr( $key ) . '" ' . ( $required ? 'required' : '' ) . '>' . esc_textarea( $value ) . '</textarea>'; }
		else { echo '<input id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" ' . ( $required ? 'required' : '' ) . '>'; }

	}

	/** Require management authorization for all mutation handlers.
	 * @return void
	 */
	private static function authorize(): void {
		if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) { wp_die( esc_html__( 'Please submit this action using the form.', 'builder-content-guide' ), '', array( 'response' => 405 ) ); }
		if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'You cannot manage this guide.', 'builder-content-guide' ), '', array( 'response' => 403 ) ); }
	}

	/** Extract a scalar POST entry ID without accepting arrays.
	 * @return int Entry ID or zero.
	 */
	private static function posted_id(): int { return isset( $_POST['entry'] ) && is_scalar( $_POST['entry'] ) ? absint( $_POST['entry'] ) : 0; }

	/** Save a validated guide via a nonce-protected POST action.
	 * @return void
	 */
	public static function save(): void {
		self::authorize(); $id = self::posted_id(); check_admin_referer( 'bcg_save_' . $id );
		$input = array();
		foreach ( array( 'name', 'reference', 'purpose', 'area', 'effect', 'impact', 'usage', 'note', 'keywords', 'steps', 'example_url', 'visible' ) as $key ) { if ( isset( $_POST[ $key ] ) ) { $input[ $key ] = wp_unslash( $_POST[ $key ] ); } }
		$result = Store::save( $id, $input );
		if ( is_wp_error( $result ) ) { wp_die( esc_html( $result->get_error_message() ), '', array( 'response' => 400, 'back_link' => true ) ); }
		wp_safe_redirect( self::url( array( 'entry' => $result ) ) ); exit;
	}

	/** Duplicate an existing guide through an authorized, nonce-protected POST request.
	 * @return void
	 */
	public static function duplicate(): void {
		self::authorize(); $id = self::posted_id(); check_admin_referer( 'bcg_duplicate_' . $id );
		$result = Store::duplicate( $id );
		if ( is_wp_error( $result ) ) { wp_die( esc_html( $result->get_error_message() ), '', array( 'response' => 400, 'back_link' => true ) ); }
		wp_safe_redirect( self::url( array( 'view' => 'edit', 'entry' => $result, 'duplicated' => '1' ) ) ); exit;
	}

	/** Trash only this plugin's guide entry; originals remain untouched.
	 * @return void
	 */
	public static function delete(): void {
		self::authorize(); $id = self::posted_id(); check_admin_referer( 'bcg_delete_' . $id );
		if ( get_post_type( $id ) !== Store::TYPE ) { wp_die( esc_html__( 'Guide entry not found.', 'builder-content-guide' ), '', array( 'response' => 404 ) ); }
		wp_trash_post( $id ); wp_safe_redirect( self::url( array( 'view' => 'manage' ) ) ); exit;
	}

	/** Restore only a trashed guide entry without modifying the referenced original.
	 * @return void
	 */
	public static function restore(): void {
		self::authorize(); $id = self::posted_id(); check_admin_referer( 'bcg_restore_' . $id );
		if ( get_post_type( $id ) !== Store::TYPE || 'trash' !== get_post_status( $id ) ) { wp_die( esc_html__( 'Guide entry not found.', 'builder-content-guide' ), '', array( 'response' => 404 ) ); }
		if ( wp_untrash_post( $id ) ) { wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) ); }
		wp_safe_redirect( self::url( array( 'view' => 'manage' ) ) ); exit;
	}

	/** Save explicit reader role grants for the current website without modifying role edit capabilities.
	 * @return void
	 */
	public static function access(): void {
		self::authorize(); check_admin_referer( 'bcg_access' );
		$roles = isset( $_POST['roles'] ) && is_array( $_POST['roles'] ) ? wp_unslash( $_POST['roles'] ) : array();
		$valid = array();
		foreach ( $roles as $role ) { if ( is_string( $role ) && isset( wp_roles()->roles[ $role ] ) ) { $valid[] = $role; } }
		update_option( 'bcg_reader_roles', array_values( array_unique( $valid ) ), false );
		wp_safe_redirect( self::url( array( 'view' => 'manage' ) ) ); exit;
	}

	/** Save optional support settings through a capability- and nonce-protected POST action.
	 * @return void
	 */
	public static function contact(): void {
		self::authorize(); check_admin_referer( 'bcg_contact' );
		$input = array();
		foreach ( array( 'enabled', 'name', 'note', 'email', 'url', 'phone' ) as $key ) { if ( isset( $_POST[ $key ] ) ) { $input[ $key ] = wp_unslash( $_POST[ $key ] ); } }
		$result = Help::save_contact( $input );
		if ( is_wp_error( $result ) ) { wp_die( esc_html( $result->get_error_message() ), '', array( 'response' => 400, 'back_link' => true ) ); }
		wp_safe_redirect( self::url( array( 'view' => 'manage', 'contact_saved' => '1' ) ) ); exit;
	}

	/** Save site menu appearance through a capability- and nonce-protected POST form.
	 * @return void
	 */
	public static function menu_settings(): void {
		self::authorize(); check_admin_referer( 'bcg_menu' );
		$input = array();
		foreach ( array( 'label', 'icon' ) as $key ) { if ( isset( $_POST[ $key ] ) ) { $input[ $key ] = wp_unslash( $_POST[ $key ] ); } }
		$result = Settings::save_menu( $input );
		if ( is_wp_error( $result ) ) { wp_die( esc_html( $result->get_error_message() ), '', array( 'response' => 400, 'back_link' => true ) ); }
		wp_safe_redirect( self::url( array( 'view' => 'manage', 'menu_saved' => '1' ) ) ); exit;
	}

	/** Render an accessible local changelog dialog with escaped, localized content.
	 * @return void
	 */
	private static function footer(): void {
		echo '<footer class="bcg-footer">deckerweb · Builder Content Guide 1.0.0 · <button type="button" class="button-link" data-bcg-changelog>' . esc_html__( 'Changelog', 'builder-content-guide' ) . '</button></footer><dialog id="bcg-changelog" aria-labelledby="bcg-changelog-title"><h2 id="bcg-changelog-title">' . esc_html__( 'Changelog', 'builder-content-guide' ) . '</h2>';
		$history = json_decode( (string) file_get_contents( __DIR__ . '/history.json' ), true );
		$categories = array( 'new' => __( 'New:', 'builder-content-guide' ), 'improved' => __( 'Improved:', 'builder-content-guide' ), 'fixed' => __( 'Fixed:', 'builder-content-guide' ), 'misc' => __( 'Misc:', 'builder-content-guide' ) );
		foreach ( is_array( $history ) ? $history : array() as $release ) {
			echo '<h3>' . esc_html( $release['version'] ) . '</h3><p><time datetime="' . esc_attr( $release['date'] ) . '">' . esc_html( wp_date( get_option( 'date_format' ), strtotime( $release['date'] . ' 12:00:00 UTC' ) ) ) . '</time>';
			if ( ! empty( $release['development'] ) ) { echo ' · ' . esc_html__( 'In development', 'builder-content-guide' ); }
			echo '</p><ul>';
			foreach ( $release['entries'] as $entry ) {
				// English messages in history.json are also included in the host catalogs by the build tool.
				echo '<li><span class="bcg-badge">' . esc_html( $categories[ $entry['category'] ] ?? '' ) . '</span> ' . esc_html( translate( $entry['en'], 'builder-content-guide' ) ) . '</li>';
			}
			echo '</ul>';
		}
		echo '<button type="button" class="button" data-bcg-close>' . esc_html__( 'Close', 'builder-content-guide' ) . '</button></dialog>';
	}
}
