<?php
/**
 * IndexNow: when a page, story, event or album is published, changed or taken
 * down, Bing (which Copilot and ChatGPT search draw on) and the other IndexNow
 * engines are told within a minute instead of waiting for the next sitemap read.
 *
 * The key is made once and kept in an option, and served at /<key>.txt, where
 * the engines check that a ping for pamoja.cc really comes from pamoja.cc. It is
 * served before any ping can go out, so no engine ever sees a key it cannot check.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pamoja_indexnow_key(): string {
	$key = (string) get_option( 'pamoja_indexnow_key', '' );
	if ( ! preg_match( '/^[A-Za-z0-9-]{8,128}$/', $key ) ) {
		$key = bin2hex( random_bytes( 16 ) );
		update_option( 'pamoja_indexnow_key', $key, true );
	}
	return $key;
}

/**
 * Answers /<key>.txt with the key, before WordPress looks for a post by that name.
 */
function pamoja_serve_indexnow_key( WP $wp ) {
	if ( ! str_ends_with( (string) $wp->request, '.txt' ) ) {
		return;
	}
	$key = pamoja_indexnow_key();
	if ( $key . '.txt' !== $wp->request ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	echo $key; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- letters, digits and dashes only.
	exit;
}
add_action( 'parse_request', 'pamoja_serve_indexnow_key' );

/**
 * The URLs to send at the end of this request. A save in the block editor runs
 * twice (the post, then its meta boxes), so URLs are gathered and sent once.
 */
function pamoja_indexnow_queue( string $url = '', bool $take = false ): array {
	static $urls = array();
	if ( '' !== $url ) {
		$urls[ $url ] = true;
	}
	$list = array_keys( $urls );
	if ( $take ) {
		$urls = array();
	}
	return $list;
}

/**
 * A public post that is published, or was published and is now changed or gone:
 * its own URL, and the listing it appears in.
 */
function pamoja_indexnow_on_status( string $new_status, string $old_status, WP_Post $post ) {
	if ( 'production' !== wp_get_environment_type() || wp_is_post_revision( $post ) || wp_is_post_autosave( $post ) ) {
		return;
	}
	if ( 'publish' !== $new_status && 'publish' !== $old_status ) {
		return;
	}
	if ( ! is_post_type_viewable( $post->post_type ) ) {
		return;
	}
	if ( function_exists( 'pamoja_unlisted_page_ids' ) && in_array( $post->ID, pamoja_unlisted_page_ids(), true ) ) {
		return;
	}
	// The address the public knew: a draft's own link is ?p=…, and the trash
	// renames the slug to …__trashed before this runs.
	$public              = clone $post;
	$public->post_status = 'publish';
	$public->post_name   = preg_replace( '/__trashed$/', '', $public->post_name );
	pamoja_indexnow_queue( (string) get_permalink( $public ) );
	$listing = 'post' === $post->post_type ? (int) get_option( 'page_for_posts' ) : 0;
	if ( $listing ) {
		pamoja_indexnow_queue( (string) get_permalink( $listing ) );
	} elseif ( 'page' !== $post->post_type ) {
		$archive = get_post_type_archive_link( $post->post_type );
		if ( $archive ) {
			pamoja_indexnow_queue( (string) $archive );
		}
	}
}
add_action( 'transition_post_status', 'pamoja_indexnow_on_status', 10, 3 );

function pamoja_indexnow_send( bool $blocking = false ) {
	$urls = pamoja_indexnow_queue( '', true );
	if ( ! $urls ) {
		return null;
	}
	$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
	$key  = pamoja_indexnow_key();
	return wp_remote_post(
		'https://api.indexnow.org/indexnow',
		array(
			'blocking' => $blocking,
			'timeout'  => 5,
			'headers'  => array( 'Content-Type' => 'application/json; charset=utf-8' ),
			'body'     => wp_json_encode(
				array(
					'host'        => $host,
					'key'         => $key,
					'keyLocation' => home_url( '/' . $key . '.txt' ),
					'urlList'     => array_values( $urls ),
				)
			),
		)
	);
}
add_action( 'shutdown', 'pamoja_indexnow_send', 10, 0 );

/**
 * `wp pamoja indexnow`: sends every URL in the sitemap at once, for the day this
 * ships (the hooks above only send what changes from then on).
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'pamoja indexnow',
		static function () {
			$server = wp_sitemaps_get_server();
			foreach ( $server->registry->get_providers() as $provider ) {
				foreach ( array_keys( $provider->get_object_subtypes() ?: array( '' => null ) ) as $subtype ) {
					for ( $page = 1; $page <= $provider->get_max_num_pages( (string) $subtype ); $page++ ) {
						foreach ( $provider->get_url_list( $page, (string) $subtype ) as $entry ) {
							pamoja_indexnow_queue( $entry['loc'] );
						}
					}
				}
			}
			$count = count( pamoja_indexnow_queue( home_url( '/' ) ) );
			$res   = pamoja_indexnow_send( true );
			if ( is_wp_error( $res ) ) {
				WP_CLI::error( $res->get_error_message() );
			}
			WP_CLI::success( sprintf( 'IndexNow answered %d for %d URLs.', wp_remote_retrieve_response_code( $res ), $count ) );
		}
	);
}
