<?php
/**
 * Small guards for a site that is public but has no accounts to give away.
 *
 * The collective's WordPress has one administrator. Anything that hands an
 * anonymous visitor that account's name is a free head start on guessing its
 * password, so the author's name is not published anywhere, and the browser
 * is told the few things it needs to know about how to treat our pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The REST API lists every user by name at /wp-json/wp/v2/users. Nothing on
 * this site reads it, so it is only there for people who are not signed in
 * to enumerate. Take it away from them.
 */
function pamoja_hide_rest_users( array $endpoints ): array {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}
	foreach ( array_keys( $endpoints ) as $route ) {
		if ( str_starts_with( (string) $route, '/wp/v2/users' ) ) {
			unset( $endpoints[ $route ] );
		}
	}
	return $endpoints;
}
add_filter( 'rest_endpoints', 'pamoja_hide_rest_users' );

/**
 * The same name travels in oEmbed responses and the author archive. The site
 * writes as a collective, so neither is wanted.
 */
function pamoja_strip_oembed_author( array $data ): array {
	unset( $data['author_name'], $data['author_url'] );
	return $data;
}
add_filter( 'oembed_response_data', 'pamoja_strip_oembed_author' );

function pamoja_no_author_archives() {
	if ( is_author() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'pamoja_no_author_archives', 1 );

/**
 * Headers every page should carry. Kept to the ones that cannot break a
 * WordPress admin screen or an embedded video.
 */
function pamoja_security_headers( array $headers ): array {
	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
	$headers['X-Frame-Options']        = 'SAMEORIGIN';
	return $headers;
}
add_filter( 'wp_headers', 'pamoja_security_headers' );

/**
 * Nothing on this site takes comments, trackbacks or pingbacks, so the
 * matching feeds and endpoints are closed rather than merely empty.
 */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'feed_links_show_comments_feed', '__return_false' );

function pamoja_no_comment_feeds() {
	if ( is_comment_feed() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'pamoja_no_comment_feeds', 1 );
