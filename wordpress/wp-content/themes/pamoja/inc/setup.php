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
	echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
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
		// Who we are, for search engines and AI assistants: the collective, where it works, where else to find it.
		$org = array(
			'@type'       => 'NGO',
			'@id'         => home_url( '/#organization' ),
			'name'        => get_bloginfo( 'name' ),
			'description' => $description,
			'url'         => home_url( '/' ),
			'logo'        => PAMOJA_THEME_URI . '/assets/brand/logo-karkadeh-860.png',
			'address'     => pamoja_place_address(),
			'areaServed'  => array(
				'@type' => 'City',
				'name'  => 'Hamilton, Ontario, Canada',
			),
		);
		// What the About page says of how it began (Our story → How we grew).
		$facts                   = pamoja_collective_facts();
		$org['foundingDate']     = $facts['founded'];
		$org['foundingLocation'] = array(
			'@type'   => 'Place',
			'name'    => $facts['place'],
			'address' => pamoja_place_address(),
		);
		$org['founder'] = array_map(
			static fn ( string $name ): array => array( '@type' => 'Person', 'name' => $name ),
			$facts['founders']
		);
		if ( pamoja_contact_email_safe() ) {
			$org['email'] = pamoja_contact_email_safe();
		}
		$profiles = wp_list_pluck( pamoja_social_links(), 'url' );
		if ( $profiles ) {
			$org['sameAs'] = array_values( $profiles );
		}
		$json = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				$org,
				array(
					'@type'      => 'WebSite',
					'@id'        => home_url( '/#website' ),
					'url'        => home_url( '/' ),
					'name'       => get_bloginfo( 'name' ),
					'inLanguage' => get_bloginfo( 'language' ),
					'publisher'  => array( '@id' => home_url( '/#organization' ) ),
				),
			),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
	if ( is_page() && ! is_front_page() ) {
		// The page itself, tied to the site and the collective, so the About and Engage pages carry markup too.
		$json = array(
			'@context'    => 'https://schema.org',
			'@type'       => is_page( 'about' ) ? 'AboutPage' : 'WebPage',
			'@id'         => $url . '#webpage',
			'url'         => $url,
			'name'        => pamoja_plain_text( get_the_title() ),
			'description' => $description,
			'inLanguage'  => get_bloginfo( 'language' ),
			'isPartOf'    => array( '@id' => home_url( '/#website' ) ),
			'about'       => array( '@id' => home_url( '/#organization' ) ),
		);
		if ( $image ) {
			$json['primaryImageOfPage'] = array( '@type' => 'ImageObject', 'url' => $image );
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
	if ( is_singular( 'event' ) ) {
		$json = pamoja_event_schema( get_queried_object_id(), $description, $url, $image );
		if ( $json ) {
			echo '<script type="application/ld+json">' . wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
		}
	}
	if ( is_post_type_archive( 'event' ) ) {
		// The events on the page, in the order it shows them: coming up, ongoing, then past.
		$items = array();
		foreach ( array_merge( pamoja_upcoming_events(), pamoja_ongoing_events(), pamoja_past_events() ) as $event ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => count( $items ) + 1,
				'url'      => get_permalink( $event ),
				'name'     => pamoja_plain_text( get_the_title( $event ) ),
			);
		}
		if ( $items ) {
			$json = array(
				'@context'        => 'https://schema.org',
				'@type'           => 'ItemList',
				'url'             => $url,
				'numberOfItems'   => count( $items ),
				'itemListElement' => $items,
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
		}
	}
}
add_action( 'wp_head', 'pamoja_head_meta', 5 );

/**
 * Where the collective works, as structured data reads an address: the city, not a street (there is no public office).
 * An event's venue adds its street when the editor has entered one.
 */
function pamoja_place_address( string $street = '' ): array {
	$address = array( '@type' => 'PostalAddress' );
	if ( '' !== $street ) {
		$address['streetAddress'] = $street;
	}
	return $address + array(
		'addressLocality' => 'Hamilton',
		'addressRegion'   => 'ON',
		'addressCountry'  => 'CA',
	);
}

/**
 * The facts the About page states about the collective (Our story: "Our name" and "How we grew"), for the structured
 * data and /llms.txt. If that story is ever rewritten under Site copy, change these with it.
 *
 * @return array{founded:string, founders:string[], place:string, host:string, name_meaning:string}
 */
function pamoja_collective_facts(): array {
	return array(
		'founded'      => '2023',
		'founders'     => array( 'Abdo Habbani', 'Fatima Shulli' ),
		'place'        => 'Hamilton, Ontario, Canada',
		'host'         => 'Community Permaculture Lab',
		'name_meaning' => 'Pamoja means “together” in Kiswahili.',
	);
}

/**
 * An event page as structured data: an Event, or an EventSeries for an ongoing gathering. Its dates are as precise
 * as the page's own: the real days once an admin has ticked "Exact dates", otherwise the month when the page's date
 * line names it ("September 2025" → 2025-09) and the year alone when it does not ("2024 & 2025" → 2024). The stored
 * day then only sorts the archive and is never given out as the day the event happens. (Google lists an event in its
 * event results only with exact days; the month or year still tells an AI assistant when it was.)
 */
function pamoja_event_schema( int $id, string $description, string $url, string $image ): array {
	$series = 'ongoing' === (string) pamoja_meta( $id, 'status', 'past' );
	$start  = pamoja_event_date( $id, 'start_date' );
	$end    = pamoja_event_date( $id, 'end_date' );
	if ( ! $series && ! $start ) {
		return array();
	}
	$location = (string) pamoja_meta( $id, 'location' );
	$json     = array(
		'@context'    => 'https://schema.org',
		'@type'       => $series ? 'EventSeries' : 'Event',
		'name'        => pamoja_plain_text( get_the_title( $id ) ),
		'description' => $description,
		'url'         => $url,
		'organizer'   => array(
			'@type' => 'NGO',
			'@id'   => home_url( '/#organization' ),
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
		// Google lists an event in its event results only with a status, a way to attend and an address. A past event
		// that took place keeps "scheduled": the other statuses mean cancelled, moved or postponed.
		'eventStatus'         => 'https://schema.org/EventScheduled',
		'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
		'location'            => array(
			'@type'   => 'Place',
			'name'    => $location ?: 'Hamilton, Ontario',
			'address' => pamoja_place_address( (string) pamoja_meta( $id, 'street_address' ) ),
		),
	);
	if ( $start ) {
		$json['startDate'] = pamoja_event_schema_date( $id, $start );
	}
	if ( $start && $end && $end > $start && pamoja_event_schema_date( $id, $end ) !== $json['startDate'] ) {
		$json['endDate'] = pamoja_event_schema_date( $id, $end );
	}
	if ( $image ) {
		$json['image'] = $image;
	}
	return $json;
}

/**
 * A stored event date as the structured data may state it (ISO 8601, in the precision the page has): the day when the
 * dates are marked exact; else the month, when the date line names that month ("November / December 2026"); else the
 * year.
 */
function pamoja_event_schema_date( int $id, DateTimeImmutable $date ): string {
	if ( pamoja_meta( $id, 'date_exact' ) ) {
		return $date->format( 'Y-m-d' );
	}
	$display = pamoja_event_when( $id );
	foreach ( array( 'F', 'M' ) as $format ) {
		if ( false !== stripos( $display, wp_date( $format, $date->getTimestamp(), wp_timezone() ) ) ) {
			return $date->format( 'Y-m' );
		}
	}
	return $date->format( 'Y' );
}

/**
 * Text for structured data and plain-text files: no tags, and entities such as &#038; read as the characters they are.
 */
function pamoja_plain_text( string $text ): string {
	return trim( html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
}

/**
 * A meta description of at most $max characters that ends where a sentence ends: as many whole sentences as fit, or,
 * when even the first is too long, that sentence cut at the last whole word. Never a word cut in half or a "…".
 * (160 rather than 155 so the site description's opening sentence, 156 characters, survives whole.)
 */
function pamoja_description_text( string $text, int $max = 160 ): string {
	$text = (string) preg_replace( '/\s+/u', ' ', pamoja_plain_text( $text ) );
	if ( mb_strlen( $text ) <= $max ) {
		return $text;
	}
	$out = '';
	foreach ( preg_split( '/(?<=[.!?])\s+/u', $text ) as $sentence ) {
		$next = '' === $out ? $sentence : $out . ' ' . $sentence;
		if ( mb_strlen( $next ) > $max ) {
			break;
		}
		$out = $next;
	}
	if ( '' !== $out ) {
		return $out;
	}
	// At the last comma, semicolon or dash if that keeps most of the room, so the cut ends with a clause; else a word.
	// (Regular expressions rather than mb_strrpos, which WordPress does not supply where mbstring is missing.)
	$cut = mb_substr( $text, 0, $max + 1 );
	if ( preg_match( '/^(.*)(?:, |; | — )/u', $cut, $m ) && mb_strlen( $m[1] ) > $max * 0.6 ) {
		$cut = $m[1];
	} else {
		$cut = (string) preg_replace( '/\s+\S*$/u', '', $cut );
	}
	return (string) preg_replace( '/[\s,;:–—-]+$/u', '', $cut );
}

/**
 * "Pamoja Cultural Collective — Treaty-committed neighbours coming together" on the front page while name and
 * tagline fit in 60 characters; a longer tagline gives way to "Pamoja Cultural Collective — Together in Hamilton,
 * Ontario" (Bing's URL Inspection marks a longer <title> as an error, 2026-10-25). "Title — Pamoja Cultural
 * Collective" elsewhere.
 */
function pamoja_document_title_parts( array $parts ): array {
	if ( is_front_page() ) {
		$name    = get_bloginfo( 'name' );
		$tagline = rtrim( (string) get_bloginfo( 'description' ), ' .' );
		$title   = $name . ' — ' . ( $tagline ?: __( 'Together', 'pamoja' ) );
		if ( mb_strlen( $title ) > 60 ) {
			$title = $name . ' — ' . __( 'Together in Hamilton, Ontario', 'pamoja' );
		}
		return array( 'title' => $title );
	}
	unset( $parts['tagline'] );
	return $parts;
}
add_filter( 'document_title_parts', 'pamoja_document_title_parts' );
add_filter( 'document_title_separator', fn() => '—' );

function pamoja_meta_description(): string {
	return pamoja_description_text( pamoja_meta_description_source() );
}

/**
 * The text a page's description is drawn from, before it is trimmed to length.
 */
function pamoja_meta_description_source(): string {
	if ( is_front_page() ) {
		return pamoja_setting( 'site_description' );
	}
	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			if ( has_excerpt( $post ) ) {
				return get_the_excerpt( $post );
			}
			$text = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
			if ( trim( $text ) ) {
				return $text;
			}
			if ( is_page_template( 'page-about.php' ) ) {
				return pamoja_home( 'about', 'lede' );
			}
			if ( is_page_template( 'page-engage.php' ) ) {
				return pamoja_home( 'engage', 'lede' );
			}
		}
	}
	if ( is_post_type_archive( 'event' ) ) {
		return pamoja_home( 'listing', 'events_intro' );
	}
	if ( is_post_type_archive( 'album' ) ) {
		return pamoja_home( 'listing', 'media_intro' );
	}
	if ( is_home() ) {
		return pamoja_home( 'listing', 'blog_intro' );
	}
	if ( is_tax( 'branch' ) ) {
		$text = trim( wp_strip_all_tags( term_description() ) );
		if ( $text ) {
			return $text;
		}
	}
	return pamoja_setting( 'site_description' );
}

function pamoja_current_url(): string {
	global $wp;
	$request = (string) ( $wp->request ?? '' );
	// Trailing slash kept, so og:url and the form's return URL match the
	// canonical URL instead of taking a 301 on the way back.
	return '' === $request ? home_url( '/' ) : home_url( user_trailingslashit( $request ) );
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
// The feeds name the WordPress version in <generator> too; wp_generator only covers the page head.
add_filter( 'the_generator', '__return_empty_string' );
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
		// archive-event.php builds its own upcoming/ongoing/past lists, so the
		// archive is a single page however many events there are.
		$query->set( 'posts_per_page', -1 );
		$query->set( 'no_found_rows', true );
	}
}
add_action( 'pre_get_posts', 'pamoja_front_queries' );

/**
 * /events/page/2/ used to serve the whole archive again under a 200. There is
 * only ever one page of it, so send anyone who asks for another back to it.
 */
function pamoja_events_archive_is_one_page() {
	if ( is_post_type_archive( 'event' ) && is_paged() ) {
		wp_safe_redirect( pamoja_events_url(), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'pamoja_events_archive_is_one_page', 1 );

/**
 * /contact/ and /get-involved/ are where people guess the Engage page lives.
 * Only while nothing else answers there: a page given either slug wins.
 */
function pamoja_guessed_engage_urls() {
	global $wp;
	if ( ! is_404() ) {
		return;
	}
	$request = trim( (string) ( $wp->request ?? '' ), '/' );
	if ( 'contact' === $request ) {
		wp_safe_redirect( pamoja_conversation_url(), 301 );
		exit;
	}
	if ( 'get-involved' === $request ) {
		wp_safe_redirect( pamoja_engage_url(), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'pamoja_guessed_engage_urls', 1 );

/**
 * WordPress 404s its own sitemap while the blog has no published posts:
 * handle_404() runs first, finds no posts for the sitemap query and sets the
 * status, so crawlers get valid XML under a 404 and discard it. The sitemap
 * route knows what it is, so let it answer for itself — render_sitemaps()
 * still sends a real 404 when a sitemap is genuinely empty.
 */
function pamoja_sitemap_sets_its_own_status( $preempt, $query ) {
	return ! empty( $query->is_sitemap ) ? true : $preempt;
}
add_filter( 'pre_handle_404', 'pamoja_sitemap_sets_its_own_status', 10, 2 );

/**
 * Pages that are not worth finding in a search: the thank-you page, which is
 * only reached by sending a form, and the blog page while it has no stories.
 */
function pamoja_unlisted_page_ids(): array {
	static $ids = null;
	if ( null === $ids ) {
		$ids  = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'page-thank-you.php',
			)
		);
		$blog = (int) get_option( 'page_for_posts' );
		if ( $blog && ! get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids' ) ) ) {
			$ids[] = $blog;
		}
		$ids = array_map( 'intval', $ids );
	}
	return $ids;
}

/**
 * Keep an empty media archive, the unlisted pages and a branch page whose
 * description is still a sentence or two out of search results until there
 * is something worth showing there.
 */
function pamoja_robots( array $robots ): array {
	$unlisted = ( is_page() || is_home() ) && in_array( get_queried_object_id(), pamoja_unlisted_page_ids(), true );
	$thin     = is_tax( 'branch' ) && function_exists( 'pamoja_branch_is_thin' ) && pamoja_branch_is_thin( get_queried_object() );
	if ( $unlisted || $thin || ( is_post_type_archive( 'album' ) && ! have_posts() ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'pamoja_robots' );

/**
 * The page sitemap leaves out the unlisted pages, so it never offers search
 * engines a page that tells them not to index it.
 */
function pamoja_sitemap_leaves_out_unlisted( array $args, string $post_type ): array {
	if ( 'page' === $post_type ) {
		$args['post__not_in'] = array_merge( $args['post__not_in'] ?? array(), pamoja_unlisted_page_ids() );
	}
	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'pamoja_sitemap_leaves_out_unlisted', 10, 2 );

/**
 * WordPress prints a canonical link on single posts and pages only; the blog,
 * the events listing and the branch pages name themselves too, so a link with
 * a query string or a tracking tag still counts for the clean URL.
 */
function pamoja_listing_canonical() {
	if ( is_front_page() || is_singular() || is_search() || is_404() || ! ( is_home() || is_archive() ) ) {
		return;
	}
	echo '<link rel="canonical" href="' . esc_url( pamoja_current_url() ) . '">' . "\n";
}
add_action( 'wp_head', 'pamoja_listing_canonical', 5 );

/**
 * Photos uploaded from now on get their resized copies as WebP, a fraction of
 * the weight of a PNG or JPEG. Only where the server's image library can write
 * WebP; elsewhere uploads stay as they are. (Checking support runs this filter
 * again, which then answers with the formats unchanged.)
 */
function pamoja_uploads_as_webp( array $formats ): array {
	static $webp = null;
	if ( null === $webp ) {
		$webp = false;
		$webp = wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) );
	}
	if ( $webp ) {
		$formats['image/jpeg'] = 'image/webp';
		$formats['image/png']  = 'image/webp';
	}
	return $formats;
}
add_filter( 'image_editor_output_format', 'pamoja_uploads_as_webp' );

/**
 * Excerpts end quietly.
 */
add_filter( 'excerpt_more', fn() => '…' );
add_filter( 'excerpt_length', fn() => 32 );
