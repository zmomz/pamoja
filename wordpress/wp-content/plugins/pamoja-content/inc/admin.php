<?php
/**
 * Admin: the "Pamoja" menu, list-table columns, and admin assets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pamoja_admin_menu() {
	add_menu_page(
		__( 'Pamoja', 'pamoja' ),
		__( 'Pamoja', 'pamoja' ),
		'edit_posts',
		'pamoja',
		'pamoja_render_overview_page',
		'dashicons-palmtree',
		3
	);
	add_submenu_page( 'pamoja', __( 'Overview', 'pamoja' ), __( 'Overview', 'pamoja' ), 'edit_posts', 'pamoja', 'pamoja_render_overview_page', 0 );
}
add_action( 'admin_menu', 'pamoja_admin_menu' );

function pamoja_render_overview_page() {
	$counts = array(
		'post'    => wp_count_posts( 'post' ),
		'event'   => wp_count_posts( 'event' ),
		'album'   => wp_count_posts( 'album' ),
		'partner' => wp_count_posts( 'partner' ),
		'service' => wp_count_posts( 'service' ),
	);
	$cards  = array(
		array(
			'title' => __( 'Blog', 'pamoja' ),
			'count' => $counts['post']->publish ?? 0,
			'text'  => __( 'Stories from the work, announcements and reflections. Newest first at /blog/ — the leaves of the tree.', 'pamoja' ),
			'links' => array(
				admin_url( 'post-new.php' ) => __( 'Write a post', 'pamoja' ),
				admin_url( 'edit.php' )     => __( 'All posts', 'pamoja' ),
			),
		),
		array(
			'title' => __( 'Events', 'pamoja' ),
			'count' => $counts['event']->publish ?? 0,
			'text'  => __( 'Gatherings and programs. Upcoming ones are the fruit on the tree and "Coming up"; featured ones are the home page tiles; every one has its own page.', 'pamoja' ),
			'links' => array(
				admin_url( 'post-new.php?post_type=event' ) => __( 'Add event', 'pamoja' ),
				admin_url( 'edit.php?post_type=event' )     => __( 'All events', 'pamoja' ),
			),
		),
		array(
			'title' => __( 'Media albums', 'pamoja' ),
			'count' => $counts['album']->publish ?? 0,
			'text'  => __( 'Photo sets and video from gatherings. Photos appear only with alt text and consent confirmed.', 'pamoja' ),
			'links' => array(
				admin_url( 'post-new.php?post_type=album' ) => __( 'Add album', 'pamoja' ),
				admin_url( 'edit.php?post_type=album' )     => __( 'All albums', 'pamoja' ),
			),
		),
		array(
			'title' => __( 'Partners & funders', 'pamoja' ),
			'count' => $counts['partner']->publish ?? 0,
			'text'  => __( 'The grove around the tree. Each entry names what the partner actually contributed.', 'pamoja' ),
			'links' => array(
				admin_url( 'edit.php?post_type=partner' ) => __( 'Manage partners', 'pamoja' ),
			),
		),
		array(
			'title' => __( 'Services', 'pamoja' ),
			'count' => $counts['service']->publish ?? 0,
			'text'  => __( 'The offerings shown under Engage → Partner, each with the same eight sections.', 'pamoja' ),
			'links' => array(
				admin_url( 'edit.php?post_type=service' ) => __( 'Manage services', 'pamoja' ),
			),
		),
		array(
			'title' => __( 'Site copy & settings', 'pamoja' ),
			'count' => null,
			'text'  => __( 'Every passage on the home, About and Engage pages, the contact email, social links, the land acknowledgment and the share image.', 'pamoja' ),
			'links' => array(
				admin_url( 'admin.php?page=pamoja-homepage' ) => __( 'Edit site copy', 'pamoja' ),
				admin_url( 'admin.php?page=pamoja-settings' ) => __( 'Site settings', 'pamoja' ),
			),
		),
	);
	?>
	<div class="wrap pamoja-overview">
		<h1><?php esc_html_e( 'Pamoja — content', 'pamoja' ); ?></h1>
		<p class="description" style="max-width:62ch"><?php esc_html_e( 'Newcomers are the subject, never the object. Write every entry with the community as the actor — who hosted, who taught, what knowledge was shared. That applies to headlines, alt text and captions too.', 'pamoja' ); ?></p>
		<div class="pamoja-overview-grid">
			<?php foreach ( $cards as $card ) : ?>
				<div class="pamoja-overview-card">
					<h2><?php echo esc_html( $card['title'] ); ?>
						<?php if ( null !== $card['count'] ) : ?>
							<span class="pamoja-count"><?php echo (int) $card['count']; ?></span>
						<?php endif; ?>
					</h2>
					<p><?php echo esc_html( $card['text'] ); ?></p>
					<p>
						<?php $first = true; foreach ( $card['links'] as $url => $label ) : ?>
							<a class="<?php echo $first ? 'button button-primary' : 'button'; ?>" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
							<?php $first = false; ?>
						<?php endforeach; ?>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Admin CSS/JS on our edit screens and pages.
 */
function pamoja_admin_assets( string $hook ) {
	$screen = get_current_screen();
	$ours   = $screen && (
		in_array( $screen->post_type, array( 'event', 'album', 'service', 'partner', 'inquiry' ), true )
		|| str_starts_with( (string) $screen->id, 'pamoja' )
		|| str_starts_with( (string) $screen->id, 'toplevel_page_pamoja' )
	);
	if ( ! $ours ) {
		return;
	}
	wp_enqueue_style( 'pamoja-admin', PAMOJA_CONTENT_URL . 'assets/admin.css', array(), PAMOJA_CONTENT_VERSION );
	wp_enqueue_media();
	wp_enqueue_script( 'pamoja-admin', PAMOJA_CONTENT_URL . 'assets/admin.js', array( 'jquery', 'jquery-ui-sortable', 'media-editor' ), PAMOJA_CONTENT_VERSION, true );
	wp_localize_script(
		'pamoja-admin',
		'pamojaAdmin',
		array(
			'addPhotos' => __( 'Add photos', 'pamoja' ),
			'useThese'  => __( 'Add to album', 'pamoja' ),
			'chooseImage' => __( 'Choose image', 'pamoja' ),
			'edit'      => __( 'Edit', 'pamoja' ),
			'remove'    => __( 'Remove from album', 'pamoja' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'pamoja_admin_assets' );

/**
 * Partner logos use the featured image slot.
 */
function pamoja_partner_thumbnail_labels( $labels ) {
	$labels->featured_image        = __( 'Logo (optional)', 'pamoja' );
	$labels->set_featured_image    = __( 'Set logo', 'pamoja' );
	$labels->remove_featured_image = __( 'Remove logo', 'pamoja' );
	$labels->use_featured_image    = __( 'Use as logo', 'pamoja' );
	return $labels;
}
add_filter( 'post_type_labels_partner', 'pamoja_partner_thumbnail_labels' );

function pamoja_partner_editor_title( $title, $post ) {
	if ( $post instanceof WP_Post && 'partner' === $post->post_type ) {
		return __( 'Name of the partner, ally or funder', 'pamoja' );
	}
	if ( $post instanceof WP_Post && 'service' === $post->post_type ) {
		return __( 'Service name, e.g. Workshops', 'pamoja' );
	}
	if ( $post instanceof WP_Post && 'event' === $post->post_type ) {
		return __( 'The gathering\'s public name', 'pamoja' );
	}
	return $title;
}
add_filter( 'enter_title_here', 'pamoja_partner_editor_title', 10, 2 );

function pamoja_partner_content_hint() {
	$screen = get_current_screen();
	if ( ! $screen || 'post' !== $screen->base ) {
		return;
	}
	$hints = array(
		'partner' => array( __( 'Contribution', 'pamoja' ), __( 'what they actually contributed: space, funding, teaching, stewardship. The text carries the credit, not the logo.', 'pamoja' ) ),
		'event'   => array( __( 'The story', 'pamoja' ), __( '2–3 paragraphs. The community is the actor — who hosted, who taught, what knowledge was shared. Dates, status, credits and photos are in the boxes below.', 'pamoja' ) ),
		'album'   => array( __( 'Description (optional)', 'pamoja' ), __( 'A line or two about the gathering these photos come from. Photos and video are in the box below.', 'pamoja' ) ),
	);
	if ( isset( $hints[ $screen->post_type ] ) ) {
		printf( '<p class="description" style="margin:0 0 6px"><strong>%s</strong> — %s</p>', esc_html( $hints[ $screen->post_type ][0] ), esc_html( $hints[ $screen->post_type ][1] ) );
	}
}
add_action( 'edit_form_after_title', 'pamoja_partner_content_hint' );

/* ---------- List-table columns ---------- */

function pamoja_event_columns( array $columns ): array {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['pamoja_date']   = __( 'When', 'pamoja' );
			$new['pamoja_status'] = __( 'Status', 'pamoja' );
			$new['pamoja_photos'] = __( 'Photos', 'pamoja' );
		}
	}
	unset( $new['date'] );
	return $new;
}
add_filter( 'manage_event_posts_columns', 'pamoja_event_columns' );

function pamoja_album_columns( array $columns ): array {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['pamoja_photos'] = __( 'Photos', 'pamoja' );
			$new['pamoja_event']  = __( 'Event', 'pamoja' );
		}
	}
	return $new;
}
add_filter( 'manage_album_posts_columns', 'pamoja_album_columns' );

function pamoja_partner_columns( array $columns ): array {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['pamoja_type'] = __( 'Type', 'pamoja' );
		}
	}
	unset( $new['date'] );
	return $new;
}
add_filter( 'manage_partner_posts_columns', 'pamoja_partner_columns' );

function pamoja_service_columns( array $columns ): array {
	unset( $columns['date'] );
	$columns['pamoja_order'] = __( 'Order', 'pamoja' );
	return $columns;
}
add_filter( 'manage_service_posts_columns', 'pamoja_service_columns' );

function pamoja_inquiry_columns( array $columns ): array {
	return array(
		'cb'                  => $columns['cb'],
		'title'               => __( 'From', 'pamoja' ),
		'pamoja_organization' => __( 'Organization', 'pamoja' ),
		'pamoja_email'        => __( 'Email', 'pamoja' ),
		'pamoja_build'        => __( 'Hoping to build', 'pamoja' ),
		'date'                => __( 'Received', 'pamoja' ),
	);
}
add_filter( 'manage_inquiry_posts_columns', 'pamoja_inquiry_columns' );

function pamoja_custom_column( string $column, int $post_id ) {
	switch ( $column ) {
		case 'pamoja_date':
			echo esc_html( pamoja_get_meta( $post_id, 'date_display' ) );
			break;
		case 'pamoja_status':
			$status = pamoja_get_meta( $post_id, 'status', 'past' );
			$labels = pamoja_event_status_options();
			$label  = $labels[ $status ] ?? $status;
			if ( 'upcoming' === $status && function_exists( 'pamoja_event_has_passed' ) && pamoja_event_has_passed( (int) $post_id ) ) {
				$label .= ' — ' . __( 'date has passed', 'pamoja' );
			}
			echo esc_html( $label );
			break;
		case 'pamoja_photos':
			$all = count( pamoja_get_gallery_ids( $post_id ) );
			$ok  = count( pamoja_get_publishable_gallery( $post_id ) );
			if ( ! $all ) {
				echo '—';
			} elseif ( $ok === $all ) {
				echo esc_html( sprintf( _n( '%d photo', '%d photos', $all, 'pamoja' ), $all ) );
			} else {
				printf(
					'<span style="color:#b32d2e">%s</span>',
					esc_html( sprintf( __( '%1$d of %2$d ready — %3$d held back', 'pamoja' ), $ok, $all, $all - $ok ) )
				);
			}
			break;
		case 'pamoja_event':
			$event = (int) pamoja_get_meta( $post_id, 'event' );
			echo $event ? esc_html( get_the_title( $event ) ) : '—';
			break;
		case 'pamoja_type':
			$type   = pamoja_get_meta( $post_id, 'partner_type', 'partner' );
			$labels = pamoja_partner_type_options();
			echo esc_html( $labels[ $type ] ?? $type );
			break;
		case 'pamoja_order':
			echo (int) get_post_field( 'menu_order', $post_id );
			break;
		case 'pamoja_organization':
			echo esc_html( pamoja_get_meta( $post_id, 'inquiry_organization' ) );
			break;
		case 'pamoja_email':
			$email = pamoja_get_meta( $post_id, 'inquiry_email' );
			if ( $email ) {
				printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) );
			}
			break;
		case 'pamoja_build':
			echo esc_html( wp_trim_words( pamoja_get_meta( $post_id, 'inquiry_build' ), 18 ) );
			break;
	}
}
add_action( 'manage_event_posts_custom_column', 'pamoja_custom_column', 10, 2 );
add_action( 'manage_album_posts_custom_column', 'pamoja_custom_column', 10, 2 );
add_action( 'manage_partner_posts_custom_column', 'pamoja_custom_column', 10, 2 );
add_action( 'manage_service_posts_custom_column', 'pamoja_custom_column', 10, 2 );
add_action( 'manage_inquiry_posts_custom_column', 'pamoja_custom_column', 10, 2 );

/**
 * Events list: most recent start date first.
 */
function pamoja_event_admin_order( WP_Query $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'event' !== $query->get( 'post_type' ) || $query->get( 'orderby' ) ) {
		return;
	}
	$query->set( 'meta_key', '_pamoja_start_date' );
	$query->set( 'orderby', 'meta_value' );
	$query->set( 'order', 'DESC' );
}
add_action( 'pre_get_posts', 'pamoja_event_admin_order' );
