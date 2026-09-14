<?php
/**
 * Launch-content importer: Pamoja → Import launch content, or
 * `wp pamoja seed`. Idempotent — every seeded item carries a stable seed id
 * in meta, so re-running updates rather than duplicates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PAMOJA_SEED_META = '_pamoja_seed_id';

/**
 * Find a seeded post by its stable id.
 */
function pamoja_seed_find_post( string $seed_id, string $post_type ): int {
	$found = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => PAMOJA_SEED_META,
			'meta_value'     => $seed_id,
		)
	);
	return $found ? (int) $found[0] : 0;
}

function pamoja_seed_find_term( string $seed_id ): int {
	$found = get_terms(
		array(
			'taxonomy'   => 'branch',
			'hide_empty' => false,
			'meta_key'   => PAMOJA_SEED_META,
			'meta_value' => $seed_id,
			'fields'     => 'ids',
		)
	);
	return ( ! is_wp_error( $found ) && $found ) ? (int) $found[0] : 0;
}

/**
 * Plain paragraphs → block-editor paragraphs.
 */
function pamoja_seed_paragraphs( array $paragraphs ): string {
	$out = array();
	foreach ( $paragraphs as $p ) {
		$out[] = "<!-- wp:paragraph -->\n<p>" . esc_html( $p ) . "</p>\n<!-- /wp:paragraph -->";
	}
	return implode( "\n\n", $out );
}

function pamoja_seed_html_paragraphs( array $paragraphs ): string {
	return implode( "\n", array_map( fn( $p ) => '<p>' . esc_html( $p ) . '</p>', $paragraphs ) );
}

/**
 * Create or update one post; returns the id.
 */
function pamoja_seed_upsert_post( string $seed_id, array $postarr, array $meta = array() ): int {
	$existing = pamoja_seed_find_post( $seed_id, $postarr['post_type'] );
	if ( $existing ) {
		$postarr['ID'] = $existing;
		$id            = wp_update_post( wp_slash( $postarr ), true );
	} else {
		$id = wp_insert_post( wp_slash( $postarr ), true );
	}
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	update_post_meta( $id, PAMOJA_SEED_META, $seed_id );
	foreach ( $meta as $key => $value ) {
		if ( '' === $value || null === $value || array() === $value ) {
			delete_post_meta( $id, $key );
		} else {
			update_post_meta( $id, $key, $value );
		}
	}
	return (int) $id;
}

/**
 * Import one supplied photograph into the media library (once), with alt
 * text, caption, and consent confirmed with its source recorded.
 */
function pamoja_seed_photo( string $seed_id, array $photo ): int {
	$existing = pamoja_seed_find_post( $seed_id, 'attachment' );
	$path     = PAMOJA_CONTENT_DIR . 'seed/photos/' . $photo['file'];
	if ( ! $existing ) {
		if ( ! file_exists( $path ) ) {
			return 0;
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$tmp = wp_tempnam( $photo['file'] );
		copy( $path, $tmp );
		$file_array = array( 'name' => $photo['file'], 'tmp_name' => $tmp );
		$id         = media_handle_sideload( $file_array, 0, $photo['title'] );
		if ( is_wp_error( $id ) ) {
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return 0;
		}
		$existing = (int) $id;
		update_post_meta( $existing, PAMOJA_SEED_META, $seed_id );
	}
	wp_update_post( array( 'ID' => $existing, 'post_title' => $photo['title'], 'post_excerpt' => $photo['caption'] ?? '' ) );
	update_post_meta( $existing, '_wp_attachment_image_alt', $photo['alt'] );
	update_post_meta( $existing, PAMOJA_CONSENT_META, '1' );
	update_post_meta( $existing, '_pamoja_consent_source', 'Supplied by Pamoja for the website (Pamoja_Website.docx, September 2026). Untick "Consent confirmed" to withdraw.' );
	return $existing;
}

/**
 * Run the import. Returns log lines.
 *
 * @return string[]
 */
function pamoja_seed_run(): array {
	$content = require PAMOJA_CONTENT_DIR . 'seed/content.php';
	$log     = array();
	$ids     = array();

	// The site's clock is Toronto's from the start, so "now" is right below.
	if ( ! get_option( 'timezone_string' ) ) {
		update_option( 'timezone_string', 'America/Toronto' );
	}

	// Branches.
	foreach ( $content['branches'] as $b ) {
		$term_id = pamoja_seed_find_term( $b['id'] );
		if ( $term_id ) {
			wp_update_term( $term_id, 'branch', array( 'name' => $b['name'], 'slug' => $b['slug'], 'description' => $b['description'] ) );
		} else {
			$existing = get_term_by( 'slug', $b['slug'], 'branch' );
			if ( $existing ) {
				$term_id = (int) $existing->term_id;
				wp_update_term( $term_id, 'branch', array( 'name' => $b['name'], 'description' => $b['description'] ) );
			} else {
				$r = wp_insert_term( $b['name'], 'branch', array( 'slug' => $b['slug'], 'description' => $b['description'] ) );
				if ( is_wp_error( $r ) ) {
					$log[] = 'Branch failed: ' . $b['name'] . ' — ' . $r->get_error_message();
					continue;
				}
				$term_id = (int) $r['term_id'];
			}
		}
		update_term_meta( $term_id, PAMOJA_SEED_META, $b['id'] );
		update_term_meta( $term_id, 'order', (int) $b['order'] );
		$ids[ $b['id'] ] = $term_id;
		$log[]           = 'Branch: ' . $b['name'];
	}

	// Partners.
	foreach ( $content['partners'] as $p ) {
		$id = pamoja_seed_upsert_post(
			$p['id'],
			array(
				'post_type'    => 'partner',
				'post_status'  => 'publish',
				'post_title'   => $p['name'],
				'post_content' => $p['contribution'],
				'menu_order'   => (int) $p['order'],
			),
			array(
				'_pamoja_partner_type' => $p['type'],
				'_pamoja_url'          => $p['url'] ?? '',
			)
		);
		if ( $id ) {
			$ids[ $p['id'] ] = $id;
			$log[]           = 'Partner: ' . $p['name'];
		}
	}

	// Photographs supplied for the website.
	foreach ( $content['photos'] ?? array() as $seed_id => $photo ) {
		$id = pamoja_seed_photo( $seed_id, $photo );
		if ( $id ) {
			$ids[ $seed_id ] = $id;
			$log[]           = 'Photo: ' . $photo['title'];
		} else {
			$log[] = 'Photo missing from seed/photos: ' . $photo['file'];
		}
	}

	// Services.
	foreach ( $content['services'] as $s ) {
		$id = pamoja_seed_upsert_post(
			$s['id'],
			array(
				'post_type'   => 'service',
				'post_status' => 'publish',
				'post_title'  => $s['title'],
				'menu_order'  => (int) $s['order'],
			),
			array(
				'_pamoja_summary'          => $s['summary'],
				'_pamoja_what_it_is'       => $s['what_it_is'],
				'_pamoja_what_happens'     => pamoja_seed_html_paragraphs( $s['what_happens'] ),
				'_pamoja_what_to_expect'   => pamoja_seed_html_paragraphs( $s['what_to_expect'] ),
				'_pamoja_need_from_you'    => $s['need_from_you'],
				'_pamoja_protocols'        => pamoja_seed_html_paragraphs( $s['protocols'] ),
				'_pamoja_what_this_is_not' => $s['what_this_is_not'],
				'_pamoja_lead_time'        => $s['lead_time'],
			)
		);
		if ( $id ) {
			$log[] = 'Service: ' . $s['title'];
		}
	}

	// Events.
	foreach ( $content['events'] as $e ) {
		$post_date = min( $e['start_date'] . ' 12:00:00', current_time( 'mysql' ) );
		$partners  = array_values( array_filter( array_map( fn( $ref ) => $ids[ $ref ] ?? 0, $e['partners'] ?? array() ) ) );
		$gallery   = array_values( array_filter( array_map( fn( $ref ) => $ids[ $ref ] ?? 0, $e['gallery'] ?? array() ) ) );
		$cover     = isset( $e['cover'] ) ? ( $ids[ $e['cover'] ] ?? 0 ) : 0;
		$id        = pamoja_seed_upsert_post(
			$e['id'],
			array(
				'post_type'    => 'event',
				'post_status'  => 'publish',
				'post_title'   => $e['title'],
				'post_name'    => $e['slug'],
				'post_content' => pamoja_seed_html_paragraphs( $e['body'] ),
				'menu_order'   => (int) ( $e['order'] ?? 0 ),
				// A publish date in the future would make WordPress schedule the
				// post, so upcoming events are dated now; the start date (meta)
				// is what sorts the archive.
				'post_date'     => $post_date,
				'post_date_gmt' => get_gmt_from_date( $post_date ),
			),
			array(
				'_pamoja_date_display' => $e['date_display'],
				'_pamoja_start_date'   => $e['start_date'],
				'_pamoja_end_date'     => $e['end_date'] ?? '',
				'_pamoja_location'     => $e['location'] ?? '',
				'_pamoja_status'       => $e['status'],
				'_pamoja_handed_on_to' => isset( $e['handed_on_to'] ) ? ( $ids[ $e['handed_on_to'] ] ?? 0 ) : 0,
				'_pamoja_partners'     => $partners,
				'_pamoja_pull_quote'   => $e['pull_quote'] ?? '',
				'_pamoja_featured'     => ! empty( $e['featured'] ) ? '1' : '',
				'_pamoja_video_url'    => $e['video_url'] ?? '',
				'_pamoja_gallery'      => $gallery,
			)
		);
		if ( $id ) {
			if ( $cover ) {
				set_post_thumbnail( $id, $cover );
			}
			$terms = array_values( array_filter( array_map( fn( $ref ) => $ids[ $ref ] ?? 0, $e['branches'] ) ) );
			wp_set_object_terms( $id, $terms, 'branch' );
			$log[] = 'Event: ' . $e['title'];
		}
	}

	// Blog (draft example).
	foreach ( $content['news'] as $n ) {
		if ( pamoja_seed_find_post( $n['id'], 'post' ) ) {
			$log[] = 'Post (already present, left as is): ' . $n['title'];
			continue;
		}
		$id = pamoja_seed_upsert_post(
			$n['id'],
			array(
				'post_type'    => 'post',
				'post_status'  => $n['status'],
				'post_title'   => $n['title'],
				'post_content' => pamoja_seed_paragraphs( $n['body'] ),
			)
		);
		if ( $id ) {
			$log[] = 'Post: ' . $n['title'];
		}
	}

	// A fresh install's sample content has no place on the site.
	foreach ( array( 'post' => 'hello-world', 'page' => 'sample-page' ) as $type => $slug ) {
		$sample = get_page_by_path( $slug, OBJECT, $type );
		if ( $sample && 'trash' !== $sample->post_status && ! get_post_meta( $sample->ID, PAMOJA_SEED_META, true ) ) {
			wp_trash_post( $sample->ID );
			$log[] = 'Trashed the sample ' . $type . ': ' . $sample->post_title;
		}
	}

	// Pages and reading settings. Titles, slugs and templates are kept in
	// step with the seed; page bodies are only written on first creation.
	$page_ids = array();
	foreach ( $content['pages'] as $key => $p ) {
		$id = pamoja_seed_find_post( $p['id'], 'page' );
		if ( ! $id ) {
			$existing = get_page_by_path( $p['slug'] );
			$id       = $existing ? (int) $existing->ID : 0;
		}
		if ( $id ) {
			wp_update_post( array( 'ID' => $id, 'post_title' => $p['title'], 'post_name' => $p['slug'], 'post_status' => 'publish' ) );
			update_post_meta( $id, PAMOJA_SEED_META, $p['id'] );
			$log[] = 'Page (kept, brought up to date): ' . $p['title'];
		} else {
			$id = pamoja_seed_upsert_post(
				$p['id'],
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $p['title'],
					'post_name'    => $p['slug'],
					'post_content' => $p['body'],
				)
			);
			$log[] = 'Page: ' . $p['title'];
		}
		if ( $id ) {
			if ( ! empty( $p['template'] ) ) {
				update_post_meta( $id, '_wp_page_template', $p['template'] );
			}
			$page_ids[ $key ] = $id;
		}
	}
	if ( ! empty( $page_ids['home'] ) && ! empty( $page_ids['news'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['home'] );
		update_option( 'page_for_posts', $page_ids['news'] );
		$log[] = 'Reading settings: homepage = Home, blog = /blog/';
	}
	if ( '/blog/%postname%/' !== get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/blog/%postname%/' );
		$log[] = 'Permalinks: /blog/%postname%/ for posts, /events/… and /media/… for the rest';
	}
	update_option( 'blogname', 'Pamoja Cultural Collective' );
	update_option( 'blogdescription', 'Treaty-committed neighbours coming together.' );
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_pingback_flag', 0 );
	update_option( 'pamoja_seeded', time() );

	flush_rewrite_rules();
	return $log;
}

/* ---------- Admin page ---------- */

function pamoja_seed_menu() {
	add_submenu_page(
		'pamoja',
		__( 'Import launch content', 'pamoja' ),
		__( 'Import launch content', 'pamoja' ),
		'manage_options',
		'pamoja-seed',
		'pamoja_render_seed_page',
		99
	);
}
add_action( 'admin_menu', 'pamoja_seed_menu', 20 );

function pamoja_render_seed_page() {
	$log = array();
	if ( isset( $_POST['pamoja_seed'] ) && check_admin_referer( 'pamoja_seed' ) && current_user_can( 'manage_options' ) ) {
		$log = pamoja_seed_run();
	}
	$seeded = (int) get_option( 'pamoja_seeded' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Import launch content', 'pamoja' ); ?></h1>
		<p style="max-width:62ch"><?php esc_html_e( 'Creates the four branches, the partners and funders, the three services, the six launch events with their photographs, the Home / About / Events / Blog / Engage / Thank-you pages, and sets the reading and permalink settings. Safe to run again: existing items are updated in place, never duplicated. Nothing you have added yourself is touched.', 'pamoja' ); ?></p>
		<?php if ( $seeded ) : ?>
			<p><em><?php echo esc_html( sprintf( __( 'Last imported: %s', 'pamoja' ), wp_date( 'j F Y, H:i', $seeded ) ) ); ?></em></p>
		<?php endif; ?>
		<form method="post">
			<?php wp_nonce_field( 'pamoja_seed' ); ?>
			<p><button type="submit" name="pamoja_seed" value="1" class="button button-primary"><?php esc_html_e( 'Import now', 'pamoja' ); ?></button></p>
		</form>
		<?php if ( $log ) : ?>
			<div class="notice notice-success"><p><strong><?php esc_html_e( 'Done.', 'pamoja' ); ?></strong></p><ul style="list-style:disc;padding-left:1.5em"><?php foreach ( $log as $line ) : ?><li><?php echo esc_html( $line ); ?></li><?php endforeach; ?></ul></div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Nudge until the launch content has been imported once.
 */
function pamoja_seed_notice() {
	if ( get_option( 'pamoja_seeded' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'pamoja_page_pamoja-seed' === $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( 'Pamoja: the launch content (branches, partners, services, events, pages) has not been imported yet.', 'pamoja' ),
		esc_url( admin_url( 'admin.php?page=pamoja-seed' ) ),
		esc_html__( 'Import it now →', 'pamoja' )
	);
}
add_action( 'admin_notices', 'pamoja_seed_notice' );

/* ---------- WP-CLI ---------- */

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'pamoja seed',
		function () {
			foreach ( pamoja_seed_run() as $line ) {
				WP_CLI::log( $line );
			}
			WP_CLI::success( 'Launch content imported.' );
		}
	);
}
