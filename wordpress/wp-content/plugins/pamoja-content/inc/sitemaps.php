<?php
/**
 * Sitemaps: no list of users. Author archives show blog posts only, so a
 * user who has published only events is listed at a page that answers 404,
 * and the list gives away the login name.
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'wp_sitemaps_add_provider',
	static function ( $provider, string $name ) {
		return 'users' === $name ? false : $provider;
	},
	10,
	2
);
