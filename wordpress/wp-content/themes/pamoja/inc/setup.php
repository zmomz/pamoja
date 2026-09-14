<?php
/**
 * Theme setup: supports, image sizes, assets, head/meta, queries.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pamoja_theme_setup() {
	load_theme_textdomain( 'pamoja', PAMOJA_THEME_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'script', 'style' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	remove_theme_support( 'core-block-patterns' );

	add_image_size( 'pamoja-card', 900, 600, true );
	add_image_size( 'pamoja-tile', 900, 1020, true );
	add_image_size( 'pamoja-wide', 1600, 900, false );
	set_post_thumbnail_size( 1200, 800, false );
}
add_action( 'after_setup_theme', 'pamoja_theme_setup' );

/**
 * The theme depends on the content plugin.
 */
function pamoja_plugin_dependency_notice() {
	if ( function_exists( 'pamoja_register_post_types' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	printf(
		'<div class="notice notice-error"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( 'The Pamoja theme needs the "Pamoja Content" plugin for events, the blog, media albums, services, partners and the forms.', 'pamoja' ),
		esc_url( admin_url( 'plugins.php' ) ),
		esc_html__( 'Activate it →', 'pamoja' )
	);
}
add_action( 'admin_notices', 'pamoja_plugin_dependency_notice' );

/**
 * Front-end assets. Fonts are self-hosted; no third-party requests.
 */
function pamoja_enqueue_assets() {
	wp_enqueue_style( 'pamoja-fonts', PAMOJA_THEME_URI . '/assets/css/fonts.css', array(), PAMOJA_THEME_VERSION );
	wp_enqueue_style( 'pamoja', PAMOJA_THEME_URI . '/assets/css/pamoja.css', array( 'pamoja-fonts' ), PAMOJA_THEME_VERSION );
	wp_enqueue_script( 'pamoja', PAMOJA_THEME_URI . '/assets/js/site.js', array(), PAMOJA_THEME_VERSION, array( 'strategy' => 'defer' ) );
	wp_localize_script(
		'pamoja',
		'pamojaSite',
		array(
			'keepPostedUrl' => function_exists( 'pamoja_inquiry_action_url' ) ? pamoja_inquiry_action_url() : '',
		)
	);

	// The block library CSS is only needed where block content renders.
	if ( ! is_singular() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'pamoja_enqueue_assets', 20 );

/**
 * html.js before first paint.
 */
function pamoja_head_js_class() {
	echo "<script>document.documentElement.classList.add('js');</script>\n";
}
add_action( 'wp_head', 'pamoja_head_js_class', 0 );

/**
 * Icons, theme colour, social meta, optional Plausible, JSON-LD.
 */
function pamoja_head_meta() {
	$brand = PAMOJA_THEME_URI . '/assets/brand/';
	echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $brand . 'favicon-32.png' ) . '">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $brand . 'apple-touch-icon.png' ) . '">' . "\n";
	echo '<meta name="theme-color" content="#822B2D">' . "\n";

	$description = pamoja_meta_description();
	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";

	$title = wp_get_document_title();
	$url   = pamoja_current_url();
	$image = pamoja_og_image_url();
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( array( 'post', 'event', 'album' ) ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";

	$plausible = pamoja_setting( 'plausible_domain' );
	if ( $plausible ) {
		echo '<script defer data-domain="' . esc_attr( $plausible ) . '" src="https://plausible.io/js/script.js"></script>' . "\n";
	}

	if ( is_front_page() ) {
		$json = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'NGO',
			'name'        => get_bloginfo( 'name' ),
			'description' => $description,
			'url'         => home_url( '/' ),
			'areaServed'  => array(
				'@type' => 'City',
				'name'  => 'Hamilton, Ontario',
			),
		);
		if ( pamoja_contact_email_safe() ) {
			$json['email'] = pamoja_contact_email_safe();
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
	if ( is_singular( 'event' ) ) {
		$id    = get_queried_object_id();
		$start = (string) pamoja_meta( $id, 'start_date' );
		$json  = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Event',
			'name'        => get_the_title( $id ),
			'description' => $description,
			'url'         => $url,
			'organizer'   => array( '@type' => 'Organization', 'name' => get_bloginfo( 'name' ) ),
		);
		if ( $start ) {
			$json['startDate'] = $start;
		}
		$location = (string) pamoja_meta( $id, 'location' );
		$json['location'] = array( '@type' => 'Place', 'name' => $location ?: 'Hamilton, Ontario' );
		if ( $image ) {
			$json['image'] = $image;
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'pamoja_head_meta', 5 );

/**
 * "Pamoja Cultural Collective — Treaty-committed neighbours coming together"
 * on the front page; "Title — Pamoja Cultural Collective" elsewhere.
 */
function pamoja_document_title_parts( array $parts ): array {
	if ( is_front_page() ) {
		return array( 'title' => get_bloginfo( 'name' ) . ' — ' . ( get_bloginfo( 'description' ) ?: __( 'Together', 'pamoja' ) ) );
	}
	unset( $parts['tagline'] );
	return $parts;
}
add_filter( 'document_title_parts', 'pamoja_document_title_parts' );
add_filter( 'document_title_separator', fn() => '—' );

function pamoja_meta_description(): string {
	if ( is_front_page() ) {
		return pamoja_setting( 'site_description' );
	}
	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			if ( has_excerpt( $post ) ) {
				return wp_strip_all_tags( get_the_excerpt( $post ) );
			}
			$text = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
			if ( $text ) {
				return wp_trim_words( $text, 30, '…' );
			}
			if ( is_page_template( 'page-about.php' ) ) {
				return wp_strip_all_tags( pamoja_home( 'about', 'lede' ) );
			}
			if ( is_page_template( 'page-engage.php' ) ) {
				return wp_strip_all_tags( pamoja_home( 'engage', 'lede' ) );
			}
		}
	}
	if ( is_post_type_archive( 'event' ) ) {
		return wp_strip_all_tags( pamoja_home( 'listing', 'events_intro' ) );
	}
	if ( is_post_type_archive( 'album' ) ) {
		return wp_strip_all_tags( pamoja_home( 'listing', 'media_intro' ) );
	}
	if ( is_home() ) {
		return wp_strip_all_tags( pamoja_home( 'listing', 'blog_intro' ) );
	}
	return pamoja_setting( 'site_description' );
}

function pamoja_current_url(): string {
	global $wp;
	return home_url( add_query_arg( array(), $wp->request ?? '' ) );
}

function pamoja_og_image_url(): string {
	if ( is_singular() && function_exists( 'pamoja_get_publishable_thumbnail_id' ) ) {
		$id = pamoja_get_publishable_thumbnail_id( get_queried_object_id() );
		if ( $id ) {
			$src = wp_get_attachment_image_src( $id, 'pamoja-wide' );
			if ( $src ) {
				return $src[0];
			}
		}
	}
	$default = (int) pamoja_setting( 'og_image' );
	if ( $default ) {
		$src = wp_get_attachment_image_src( $default, 'pamoja-wide' );
		if ( $src ) {
			return $src[0];
		}
	}
	return PAMOJA_THEME_URI . '/assets/brand/og-image.png';
}

/**
 * Body classes the CSS keys on: the page kind and the lit part of the tree.
 */
function pamoja_body_class( array $classes ): array {
	$classes[] = is_front_page() ? 'is-home' : 'is-subpage';
	$classes[] = 'part-' . pamoja_current_part();
	return $classes;
}
add_filter( 'body_class', 'pamoja_body_class' );

/**
 * Keep the site quiet: no emoji script, no RSD/wlw links, no comments.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

/**
 * Albums by date; the events page builds its own lists (upcoming, ongoing,
 * past) so its main query just needs to exist.
 */
function pamoja_front_queries( WP_Query $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'album' ) ) {
		$query->set( 'posts_per_page', 18 );
	}
	if ( $query->is_post_type_archive( 'event' ) ) {
		$query->set( 'posts_per_page', 1 );
	}
}
add_action( 'pre_get_posts', 'pamoja_front_queries' );

/**
 * Excerpts end quietly.
 */
add_filter( 'excerpt_more', fn() => '…' );
add_filter( 'excerpt_length', fn() => 32 );
