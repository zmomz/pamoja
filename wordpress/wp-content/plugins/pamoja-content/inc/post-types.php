<?php
/**
 * Post types and taxonomies.
 *
 * - post   → relabelled "Blog" (the built-in type, so editors get the
 *            familiar writing flow, categories, tags and RSS).
 * - event  → Events / Programs (public, /events/).
 * - album  → Media albums: photo galleries and video (public, /media/).
 * - service, partner → shown on the homepage only (no public pages).
 * - inquiry → the contact form inbox (admin only).
 * - branch  → taxonomy: the four functions of the work, drives the filter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pamoja_register_post_types() {
	register_post_type(
		'event',
		array(
			'labels'        => array(
				'name'               => __( 'Events', 'pamoja' ),
				'singular_name'      => __( 'Event', 'pamoja' ),
				'menu_name'          => __( 'Events', 'pamoja' ),
				'add_new'            => __( 'Add event', 'pamoja' ),
				'add_new_item'       => __( 'Add a new event or program', 'pamoja' ),
				'edit_item'          => __( 'Edit event', 'pamoja' ),
				'new_item'           => __( 'New event', 'pamoja' ),
				'view_item'          => __( 'View event', 'pamoja' ),
				'view_items'         => __( 'View events', 'pamoja' ),
				'search_items'       => __( 'Search events', 'pamoja' ),
				'not_found'          => __( 'No events yet.', 'pamoja' ),
				'not_found_in_trash' => __( 'No events in the trash.', 'pamoja' ),
				'all_items'          => __( 'Events', 'pamoja' ),
			),
			'description'   => __( 'A leaf on the tree. Write every entry with the community as the actor — who hosted, who taught, what knowledge was shared.', 'pamoja' ),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array( 'slug' => 'events', 'with_front' => false ),
			'menu_icon'     => 'dashicons-calendar-alt',
			'show_in_menu'  => 'pamoja',
			// Classic edit screen: the details, partners and gallery sit in
			// plain view under the story instead of a collapsed drawer.
			'show_in_rest'  => false,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'taxonomies'    => array( 'branch' ),
		)
	);

	register_post_type(
		'album',
		array(
			'labels'        => array(
				'name'               => __( 'Media albums', 'pamoja' ),
				'singular_name'      => __( 'Media album', 'pamoja' ),
				'menu_name'          => __( 'Media albums', 'pamoja' ),
				'add_new'            => __( 'Add album', 'pamoja' ),
				'add_new_item'       => __( 'Add a new album (photos or video)', 'pamoja' ),
				'edit_item'          => __( 'Edit album', 'pamoja' ),
				'new_item'           => __( 'New album', 'pamoja' ),
				'view_item'          => __( 'View album', 'pamoja' ),
				'view_items'         => __( 'View albums', 'pamoja' ),
				'search_items'       => __( 'Search albums', 'pamoja' ),
				'not_found'          => __( 'No albums yet.', 'pamoja' ),
				'not_found_in_trash' => __( 'No albums in the trash.', 'pamoja' ),
				'all_items'          => __( 'Media albums', 'pamoja' ),
			),
			'description'   => __( 'A set of photographs or a video from a gathering. Every photo needs alt text and confirmed consent before it appears.', 'pamoja' ),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array( 'slug' => 'media', 'with_front' => false ),
			'menu_icon'     => 'dashicons-format-gallery',
			'show_in_menu'  => 'pamoja',
			'show_in_rest'  => false,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			'taxonomies'    => array( 'branch' ),
		)
	);

	register_post_type(
		'service',
		array(
			'labels'        => array(
				'name'          => __( 'Services', 'pamoja' ),
				'singular_name' => __( 'Service', 'pamoja' ),
				'menu_name'     => __( 'Services', 'pamoja' ),
				'add_new'       => __( 'Add service', 'pamoja' ),
				'add_new_item'  => __( 'Add a new service', 'pamoja' ),
				'edit_item'     => __( 'Edit service', 'pamoja' ),
				'all_items'     => __( 'Services', 'pamoja' ),
				'not_found'     => __( 'No services yet.', 'pamoja' ),
			),
			'description'   => __( 'One of the offerings on What We Offer. Every service uses the same eight fixed fields, in the same order.', 'pamoja' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => 'pamoja',
			'show_in_rest'  => false,
			'supports'      => array( 'title', 'page-attributes', 'revisions' ),
		)
	);

	register_post_type(
		'partner',
		array(
			'labels'        => array(
				'name'          => __( 'Partners', 'pamoja' ),
				'singular_name' => __( 'Partner', 'pamoja' ),
				'menu_name'     => __( 'Partners & funders', 'pamoja' ),
				'add_new'       => __( 'Add partner', 'pamoja' ),
				'add_new_item'  => __( 'Add a partner, ally or funder', 'pamoja' ),
				'edit_item'     => __( 'Edit partner', 'pamoja' ),
				'all_items'     => __( 'Partners & funders', 'pamoja' ),
				'not_found'     => __( 'No partners yet.', 'pamoja' ),
			),
			'description'   => __( 'A member of the grove. No logo walls: every entry names what the partner actually contributed.', 'pamoja' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => 'pamoja',
			'show_in_rest'  => false,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'inquiry',
		array(
			'labels'          => array(
				'name'          => __( 'Inquiries', 'pamoja' ),
				'singular_name' => __( 'Inquiry', 'pamoja' ),
				'menu_name'     => __( 'Inquiries', 'pamoja' ),
				'edit_item'     => __( 'Inquiry', 'pamoja' ),
				'all_items'     => __( 'Inquiries', 'pamoja' ),
				'not_found'     => __( 'No inquiries yet. Messages sent through the contact form arrive here.', 'pamoja' ),
			),
			'description'     => __( 'Messages sent through the contact form.', 'pamoja' ),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'pamoja',
			'show_in_rest'    => false,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'pamoja_register_post_types' );

function pamoja_register_taxonomies() {
	register_taxonomy(
		'branch',
		array( 'event', 'post', 'album' ),
		array(
			'labels'            => array(
				'name'          => __( 'Branches', 'pamoja' ),
				'singular_name' => __( 'Branch', 'pamoja' ),
				'menu_name'     => __( 'Branches', 'pamoja' ),
				'all_items'     => __( 'All branches', 'pamoja' ),
				'edit_item'     => __( 'Edit branch', 'pamoja' ),
				'add_new_item'  => __( 'Add a branch', 'pamoja' ),
				'search_items'  => __( 'Search branches', 'pamoja' ),
				'not_found'     => __( 'No branches yet.', 'pamoja' ),
			),
			'description'       => __( 'The four functions of Pamoja\'s work. Pick every branch a gathering genuinely served — the overlap is the point.', 'pamoja' ),
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'branch', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'pamoja_register_taxonomies' );

/**
 * The built-in post type is the blog.
 */
function pamoja_relabel_posts_as_news() {
	global $wp_post_types;
	if ( ! isset( $wp_post_types['post'] ) ) {
		return;
	}
	$labels                     = $wp_post_types['post']->labels;
	$labels->name               = __( 'Blog', 'pamoja' );
	$labels->singular_name      = __( 'Blog post', 'pamoja' );
	$labels->menu_name          = __( 'Blog', 'pamoja' );
	$labels->name_admin_bar     = __( 'Blog post', 'pamoja' );
	$labels->add_new            = __( 'Write a post', 'pamoja' );
	$labels->add_new_item       = __( 'Write a blog post', 'pamoja' );
	$labels->edit_item          = __( 'Edit post', 'pamoja' );
	$labels->new_item           = __( 'New post', 'pamoja' );
	$labels->view_item          = __( 'View post', 'pamoja' );
	$labels->view_items         = __( 'View blog', 'pamoja' );
	$labels->search_items       = __( 'Search posts', 'pamoja' );
	$labels->not_found          = __( 'No posts yet.', 'pamoja' );
	$labels->not_found_in_trash = __( 'No posts in the trash.', 'pamoja' );
	$labels->all_items          = __( 'All posts', 'pamoja' );
	$wp_post_types['post']->menu_icon = 'dashicons-megaphone';
}
add_action( 'init', 'pamoja_relabel_posts_as_news', 20 );

/**
 * Branch term order (1–4) lives in term meta.
 */
function pamoja_branch_order_field( $term = null ) {
	$order = $term instanceof WP_Term ? (int) get_term_meta( $term->term_id, 'order', true ) : 0;
	?>
	<tr class="form-field">
		<th scope="row"><label for="pamoja-branch-order"><?php esc_html_e( 'Display order', 'pamoja' ); ?></label></th>
		<td>
			<input type="number" id="pamoja-branch-order" name="pamoja_branch_order" min="1" step="1" value="<?php echo esc_attr( $order ?: '' ); ?>" style="width:6em" />
			<p class="description"><?php esc_html_e( 'Order on Our Work (1–4).', 'pamoja' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'branch_edit_form_fields', 'pamoja_branch_order_field' );

function pamoja_branch_add_order_field() {
	?>
	<div class="form-field">
		<label for="pamoja-branch-order"><?php esc_html_e( 'Display order', 'pamoja' ); ?></label>
		<input type="number" id="pamoja-branch-order" name="pamoja_branch_order" min="1" step="1" style="width:6em" />
		<p><?php esc_html_e( 'Order on Our Work (1–4).', 'pamoja' ); ?></p>
	</div>
	<?php
}
add_action( 'branch_add_form_fields', 'pamoja_branch_add_order_field' );

function pamoja_save_branch_order( $term_id ) {
	if ( isset( $_POST['pamoja_branch_order'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- core term nonce already checked.
		update_term_meta( $term_id, 'order', absint( wp_unslash( $_POST['pamoja_branch_order'] ) ) );
	}
}
add_action( 'edited_branch', 'pamoja_save_branch_order' );
add_action( 'created_branch', 'pamoja_save_branch_order' );

/**
 * Branches in display order.
 *
 * @return WP_Term[]
 */
function pamoja_get_branches() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'branch',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	usort(
		$terms,
		function ( $a, $b ) {
			$oa = (int) get_term_meta( $a->term_id, 'order', true ) ?: 99;
			$ob = (int) get_term_meta( $b->term_id, 'order', true ) ?: 99;
			return $oa === $ob ? strcmp( $a->name, $b->name ) : $oa <=> $ob;
		}
	);
	return $terms;
}
