<?php
/**
 * The site map: four doors, ten places, each with its part of the tree.
 *
 *   About   → soil · trunk · branches · roots   (one page, four stops)
 *   Events  → fruit (the canopy)                (upcoming, ongoing, past)
 *   Blog    → leaves (the canopy)
 *   Engage  → seeds                             (volunteer · partner · support)
 *
 * Pages are found by their template, so renaming a page in WordPress never
 * breaks the menu; only deleting the page does.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL of the page using a template (About, Engage), or '' when none exists.
 */
function pamoja_page_url_by_template( string $template ): string {
	static $cache = array();
	if ( isset( $cache[ $template ] ) ) {
		return $cache[ $template ];
	}
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_wp_page_template',
			'meta_value'     => $template,
		)
	);
	$cache[ $template ] = $pages ? (string) get_permalink( $pages[0] ) : '';
	return $cache[ $template ];
}

function pamoja_about_url( string $stop = '' ): string {
	$url = pamoja_page_url_by_template( 'page-about.php' ) ?: home_url( '/about/' );
	return $stop ? $url . '#' . $stop : $url;
}

function pamoja_engage_url( string $stop = '' ): string {
	$url = pamoja_page_url_by_template( 'page-engage.php' ) ?: home_url( '/engage/' );
	return $stop ? $url . '#' . $stop : $url;
}

function pamoja_events_url( string $stop = '' ): string {
	$url = get_post_type_archive_link( 'event' ) ?: home_url( '/events/' );
	return $stop ? $url . '#' . $stop : $url;
}

function pamoja_blog_url(): string {
	$page = (int) get_option( 'page_for_posts' );
	return $page ? (string) get_permalink( $page ) : home_url( '/blog/' );
}

function pamoja_conversation_url(): string {
	return pamoja_engage_url( 'conversation' );
}

function pamoja_thank_you_url(): string {
	return pamoja_page_url_by_template( 'page-thank-you.php' );
}

/**
 * One map item by its id, or null.
 */
function pamoja_site_map_item( string $door, string $id ): ?array {
	foreach ( pamoja_site_map()[ $door ]['items'] ?? array() as $item ) {
		if ( ( $item['id'] ?? '' ) === $id ) {
			return $item;
		}
	}
	return null;
}

// The plugin asks for these so it never has to know a page's slug.
add_filter( 'pamoja_conversation_url', 'pamoja_conversation_url' );
add_filter( 'pamoja_thank_you_url', 'pamoja_thank_you_url' );

/**
 * The whole map, in menu order.
 *
 * @return array<string, array{label:string, url:string, part:string, tree:string, items:array<int, array{label:string, url:string, part:string, desc?:string}>}>
 */
function pamoja_site_map(): array {
	$map = array(
		'about'  => array(
			'label' => __( 'About', 'pamoja' ),
			'url'   => pamoja_about_url(),
			'part'  => 'trunk',
			'tree'  => __( 'Soil · trunk · branches · roots', 'pamoja' ),
			'items' => array(
				array( 'id' => 'why-we-exist', 'label' => pamoja_home( 'about_why', 'title' ), 'url' => pamoja_about_url( 'why-we-exist' ), 'part' => 'soil', 'desc' => __( 'The ground we start from', 'pamoja' ) ),
				array( 'id' => 'our-story', 'label' => pamoja_home( 'about_story', 'title' ), 'url' => pamoja_about_url( 'our-story' ), 'part' => 'trunk', 'desc' => __( 'Our name, and how we grew', 'pamoja' ) ),
				array( 'id' => 'how-we-work-together', 'label' => pamoja_home( 'about_how', 'title' ), 'url' => pamoja_about_url( 'how-we-work-together' ), 'part' => 'branches' ),
				array( 'id' => 'ethics-and-values', 'label' => pamoja_home( 'about_values', 'title' ), 'url' => pamoja_about_url( 'ethics-and-values' ), 'part' => 'roots' ),
			),
		),
		'events' => array(
			'label' => __( 'Events', 'pamoja' ),
			'url'   => pamoja_events_url(),
			'part'  => 'canopy',
			'tree'  => __( 'Fruit', 'pamoja' ),
			'items' => array(
				array( 'id' => 'upcoming', 'label' => __( 'Upcoming events', 'pamoja' ), 'url' => pamoja_events_url( 'upcoming' ), 'part' => 'canopy', 'desc' => pamoja_upcoming_count_label() ),
				array( 'id' => 'past', 'label' => __( 'Past events', 'pamoja' ), 'url' => pamoja_events_url( 'past' ), 'part' => 'canopy', 'desc' => pamoja_past_count_label() ),
			),
		),
		'blog'   => array(
			'label' => __( 'Blog', 'pamoja' ),
			'url'   => pamoja_blog_url(),
			'part'  => 'canopy',
			'tree'  => __( 'Leaves', 'pamoja' ),
			'items' => array(
				array( 'id' => 'stories', 'label' => __( 'Stories from the work', 'pamoja' ), 'url' => pamoja_blog_url(), 'part' => 'canopy' ),
			),
		),
		'engage' => array(
			'label' => __( 'Engage', 'pamoja' ),
			'url'   => pamoja_engage_url(),
			'part'  => 'seeds',
			'tree'  => __( 'Seeds', 'pamoja' ),
			'items' => array(
				array( 'id' => 'volunteer', 'label' => pamoja_home( 'engage_volunteer', 'title' ), 'url' => pamoja_engage_url( 'volunteer' ), 'part' => 'seeds' ),
				array( 'id' => 'partner', 'label' => pamoja_home( 'engage_partner', 'title' ), 'url' => pamoja_engage_url( 'partner' ), 'part' => 'seeds' ),
				array( 'id' => 'support', 'label' => pamoja_home( 'engage_support', 'title' ), 'url' => pamoja_engage_url( 'support' ), 'part' => 'seeds' ),
			),
		),
	);
	return apply_filters( 'pamoja_site_map', $map );
}

/**
 * Which door the current request belongs to ('' on the home page).
 */
function pamoja_current_door(): string {
	if ( is_front_page() ) {
		return '';
	}
	if ( is_page_template( 'page-about.php' ) ) {
		return 'about';
	}
	if ( is_page_template( 'page-engage.php' ) ) {
		return 'engage';
	}
	if ( is_post_type_archive( 'event' ) || is_singular( 'event' ) || is_post_type_archive( 'album' ) || is_singular( 'album' ) ) {
		return 'events';
	}
	if ( is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_tax( 'branch' ) ) {
		return 'blog';
	}
	return '';
}

/**
 * The part of the tree that is lit for the current request.
 */
function pamoja_current_part(): string {
	$door = pamoja_current_door();
	if ( '' === $door ) {
		return 'all';
	}
	$map = pamoja_site_map();
	return $map[ $door ]['part'] ?? 'all';
}
