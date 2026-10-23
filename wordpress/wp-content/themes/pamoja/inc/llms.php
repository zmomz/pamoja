<?php
/**
 * /llms.txt: what the collective is and where everything on the site lives, as plain text for AI assistants. Built from
 * the live site map, events, posts and settings on each request, so it never goes stale.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The file's text, in the llms.txt layout: a title, a one-line summary, then sections of links.
 */
function pamoja_llms_txt(): string {
	$link  = static fn ( string $label, string $url, string $note = '' ): string => '- [' . wp_strip_all_tags( $label ) . '](' . esc_url_raw( $url ) . ')' . ( '' !== $note ? ': ' . wp_strip_all_tags( $note ) : '' );
	$lines = array( '# ' . get_bloginfo( 'name' ), '', '> ' . wp_strip_all_tags( pamoja_setting( 'site_description' ) ), '' );

	foreach ( pamoja_site_map() as $door ) {
		$lines[] = '## ' . $door['label'];
		$lines[] = $link( $door['label'], $door['url'] );
		foreach ( $door['items'] as $item ) {
			$lines[] = $link( $item['label'], $item['url'], (string) ( $item['desc'] ?? '' ) );
		}
		$lines[] = '';
	}

	$events = get_posts( array( 'post_type' => 'event', 'post_status' => 'publish', 'posts_per_page' => 10 ) );
	if ( $events ) {
		$lines[] = '## ' . __( 'Recent events', 'pamoja' );
		foreach ( $events as $event ) {
			$start   = function_exists( 'pamoja_meta' ) ? (string) pamoja_meta( $event->ID, 'start_date' ) : '';
			$lines[] = $link( get_the_title( $event ), get_permalink( $event ), $start ? substr( $start, 0, 10 ) : '' );
		}
		$lines[] = '';
	}

	$posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 10 ) );
	if ( $posts ) {
		$lines[] = '## ' . __( 'Stories from the work', 'pamoja' );
		foreach ( $posts as $post ) {
			$lines[] = $link( get_the_title( $post ), get_permalink( $post ), get_the_excerpt( $post ) );
		}
		$lines[] = '';
	}

	$lines[] = '## ' . __( 'Contact', 'pamoja' );
	$lines[] = $link( __( 'Start a conversation', 'pamoja' ), pamoja_conversation_url() );
	if ( pamoja_contact_email_safe() ) {
		$lines[] = '- ' . __( 'Email', 'pamoja' ) . ': ' . pamoja_contact_email_safe();
	}
	foreach ( pamoja_social_links() as $profile ) {
		$lines[] = $link( $profile['label'], $profile['url'] );
	}

	return implode( "\n", $lines ) . "\n";
}

/**
 * Answers /llms.txt before WordPress looks for a post by that name (and answers 404).
 */
function pamoja_serve_llms_txt( WP $wp ) {
	if ( 'llms.txt' !== $wp->request ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	echo pamoja_llms_txt(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain text, each part stripped above.
	exit;
}
add_action( 'parse_request', 'pamoja_serve_llms_txt' );
