<?php
/**
 * Content getters used by the templates. Everything comes through here so
 * the templates never query directly and the consent rule is applied once.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Branch terms in order (from the plugin), or [] without it.
 *
 * @return WP_Term[]
 */
function pamoja_branches(): array {
	return function_exists( 'pamoja_get_branches' ) ? pamoja_get_branches() : array();
}

/**
 * Meta reader that works with or without the plugin.
 */
function pamoja_meta( int $post_id, string $key, $default = '' ) {
	if ( function_exists( 'pamoja_get_meta' ) ) {
		return pamoja_get_meta( $post_id, $key, $default );
	}
	$value = get_post_meta( $post_id, '_pamoja_' . $key, true );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * Which shelf an event belongs on, whatever its status field still says.
 *
 * "Upcoming" is a promise about the future: the day after an event happens
 * it moves to "past" on its own, so nothing is advertised that has already
 * been and gone, and nothing silently disappears while an editor catches up.
 * Anything else (past, handed-on, unset) is past.
 */
function pamoja_event_shelf( int $event_id ): string {
	$status = (string) pamoja_meta( $event_id, 'status', 'past' );
	if ( 'ongoing' === $status ) {
		return 'ongoing';
	}
	if ( 'upcoming' === $status ) {
		return pamoja_event_has_passed( $event_id ) ? 'past' : 'upcoming';
	}
	return 'past';
}

/**
 * The date an event sorts by: the start when we are looking forward, the
 * last day when we are looking back. '' when no date has been entered.
 */
function pamoja_event_sort_date( int $event_id, bool $forward ): string {
	$date = $forward
		? pamoja_event_date( $event_id, 'start_date' )
		: ( pamoja_event_date( $event_id, 'end_date' ) ?: pamoja_event_date( $event_id, 'start_date' ) );
	return $date ? $date->format( 'Y-m-d' ) : '';
}

/**
 * Events on one shelf. Upcoming soonest-first, everything else most recent
 * first; an event whose date has not been filled in yet still shows, at the
 * end of its list, rather than vanishing from the site. Cached per request:
 * the tree, the menu and the lists all ask.
 *
 * @return WP_Post[]
 */
function pamoja_events_by_status( string $status ): array {
	static $cache = array();
	if ( isset( $cache[ $status ] ) ) {
		return $cache[ $status ];
	}
	if ( ! post_type_exists( 'event' ) ) {
		return $cache[ $status ] = array();
	}
	$posts = get_posts(
		array(
			'post_type'      => 'event',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	$out = array();
	foreach ( $posts as $post ) {
		if ( pamoja_event_shelf( $post->ID ) === $status ) {
			$out[] = $post;
		}
	}
	$forward = 'upcoming' === $status;
	usort(
		$out,
		static function ( WP_Post $a, WP_Post $b ) use ( $forward ) {
			$da = pamoja_event_sort_date( $a->ID, $forward );
			$db = pamoja_event_sort_date( $b->ID, $forward );
			if ( '' === $da || '' === $db ) {
				// Undated events go last, newest first among themselves.
				return ( ( '' === $da ? 1 : 0 ) - ( '' === $db ? 1 : 0 ) ) ?: strcmp( $b->post_date, $a->post_date );
			}
			return $forward ? strcmp( $da, $db ) : strcmp( $db, $da );
		}
	);
	return $cache[ $status ] = $out;
}

/** @return WP_Post[] */
function pamoja_upcoming_events(): array {
	return pamoja_events_by_status( 'upcoming' );
}

/** @return WP_Post[] */
function pamoja_ongoing_events(): array {
	return pamoja_events_by_status( 'ongoing' );
}

/** @return WP_Post[] */
function pamoja_past_events(): array {
	return pamoja_events_by_status( 'past' );
}

/**
 * The home page tiles: events marked featured, in their Order, then by date.
 *
 * @return WP_Post[]
 */
function pamoja_featured_events( int $limit = 4 ): array {
	if ( ! post_type_exists( 'event' ) ) {
		return array();
	}
	$posts = get_posts(
		array(
			'post_type'      => 'event',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'meta_key'       => '_pamoja_featured',
			'meta_value'     => '1',
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		)
	);
	return $posts;
}

function pamoja_upcoming_count_label( ?int $n = null ): string {
	$n = null === $n ? count( pamoja_upcoming_events() ) : $n;
	if ( 0 === $n ) {
		return __( 'nothing announced yet', 'pamoja' );
	}
	return sprintf( _n( '%d coming up', '%d coming up', $n, 'pamoja' ), $n );
}

function pamoja_past_count_label(): string {
	$n = count( pamoja_past_events() ) + count( pamoja_ongoing_events() );
	return sprintf( _n( '%d gathering so far', '%d gatherings so far', $n, 'pamoja' ), $n );
}

/**
 * The oEmbed for an event's video, or '' when there is none.
 */
function pamoja_event_video_embed( int $event_id ): string {
	$url = (string) pamoja_meta( $event_id, 'video_url' );
	if ( ! $url ) {
		return '';
	}
	$embed = wp_oembed_get( $url, array( 'width' => 1200 ) );
	return $embed ? (string) $embed : '';
}

/**
 * Services in menu order.
 *
 * @return WP_Post[]
 */
function pamoja_services(): array {
	if ( ! post_type_exists( 'service' ) ) {
		return array();
	}
	return get_posts(
		array(
			'post_type'      => 'service',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
}

/**
 * Partners in menu order.
 *
 * @return WP_Post[]
 */
function pamoja_partners(): array {
	if ( ! post_type_exists( 'partner' ) ) {
		return array();
	}
	return get_posts(
		array(
			'post_type'      => 'partner',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
}

/**
 * Partners credited on an event.
 *
 * @return WP_Post[]
 */
function pamoja_event_partners( int $event_id ): array {
	$ids = array_filter( array_map( 'intval', (array) pamoja_meta( $event_id, 'partners', array() ) ) );
	if ( ! $ids ) {
		return array();
	}
	return get_posts(
		array(
			'post_type'      => 'partner',
			'post_status'    => 'publish',
			'post__in'       => $ids,
			'orderby'        => 'post__in',
			'posts_per_page' => -1,
		)
	);
}

/**
 * Plain contribution text of a partner (the editor content, tags stripped).
 */
function pamoja_partner_contribution( WP_Post $partner ): string {
	return trim( wp_strip_all_tags( strip_shortcodes( $partner->post_content ) ) );
}

/**
 * Event status label for the badge, from the plugin's one list.
 */
function pamoja_event_status_label( int $event_id ): string {
	$status = (string) pamoja_meta( $event_id, 'status', 'past' );
	if ( 'handed-on' === $status ) {
		$to = (int) pamoja_meta( $event_id, 'handed_on_to' );
		if ( $to && 'publish' === get_post_status( $to ) ) {
			return sprintf( __( 'Handed on — now stewarded by %s', 'pamoja' ), get_the_title( $to ) );
		}
	}
	$options = function_exists( 'pamoja_event_status_options' ) ? pamoja_event_status_options() : array();
	return $options[ $status ] ?? ucfirst( $status );
}

/**
 * True when an event's last date (end, else start) is before today in the
 * site's timezone. Used so an "upcoming" event stops being advertised the
 * day after it happens, whatever its status field still says.
 */
function pamoja_event_has_passed( int $event_id ): bool {
	$last = pamoja_event_date( $event_id, 'end_date' ) ?: pamoja_event_date( $event_id, 'start_date' );
	if ( ! $last ) {
		return false;
	}
	return $last->format( 'Y-m-d' ) < current_time( 'Y-m-d' );
}

/**
 * Teaser: the excerpt if set, else the first paragraph trimmed.
 */
function pamoja_teaser( WP_Post $post, int $max = 200 ): string {
	if ( has_excerpt( $post ) ) {
		return wp_strip_all_tags( get_the_excerpt( $post ) );
	}
	$text = trim( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) );
	$text = preg_replace( '/\s+/', ' ', $text );
	$first = preg_split( '/(?<=[.!?])\s+(?=[A-Z])/', $text, 3 );
	$text  = trim( ( $first[0] ?? '' ) . ' ' . ( $first[1] ?? '' ) );
	if ( mb_strlen( $text ) <= $max ) {
		return $text;
	}
	$cut = mb_substr( $text, 0, $max );
	$sp  = mb_strrpos( $cut, ' ' );
	return ( $sp ? mb_substr( $cut, 0, $sp ) : $cut ) . '…';
}

/**
 * Branch slugs an event/post serves.
 *
 * @return WP_Term[]
 */
function pamoja_post_branches( int $post_id ): array {
	$terms = get_the_terms( $post_id, 'branch' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}
	$order = array_flip( array_map( fn( $t ) => $t->term_id, pamoja_branches() ) );
	usort( $terms, fn( $a, $b ) => ( $order[ $a->term_id ] ?? 99 ) <=> ( $order[ $b->term_id ] ?? 99 ) );
	return $terms;
}

/**
 * Rich-text service field with draft paragraphs removed.
 */
function pamoja_service_html( int $service_id, string $key ): string {
	return wp_kses_post( pamoja_strip_draft_notes( wpautop( (string) pamoja_meta( $service_id, $key ) ) ) );
}

/**
 * The one editorial rule for draft notes: a paragraph that starts with "["
 * never renders, wherever it is written.
 */
function pamoja_strip_draft_notes( string $html ): string {
	return (string) preg_replace( '/<p(?:\s[^>]*)?>\s*\[.*?<\/p>/s', '', $html );
}

/**
 * Latest published posts.
 *
 * @return WP_Post[]
 */
function pamoja_news( int $limit = 3 ): array {
	return get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
		)
	);
}

/**
 * Albums linked to an event.
 *
 * @return WP_Post[]
 */
function pamoja_event_albums( int $event_id ): array {
	if ( ! post_type_exists( 'album' ) ) {
		return array();
	}
	return get_posts(
		array(
			'post_type'      => 'album',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => '_pamoja_event',
			'meta_value'     => $event_id,
		)
	);
}

/**
 * Gallery ids that may be shown (consent + alt), or [] without the plugin.
 *
 * @return int[]
 */
function pamoja_gallery( int $post_id ): array {
	return function_exists( 'pamoja_get_publishable_gallery' ) ? pamoja_get_publishable_gallery( $post_id ) : array();
}

/**
 * Featured image id if it passes the consent rule.
 */
function pamoja_thumbnail_id( int $post_id ): int {
	return function_exists( 'pamoja_get_publishable_thumbnail_id' ) ? pamoja_get_publishable_thumbnail_id( $post_id ) : 0;
}

