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
 * The player for an event's video, or '' when there is none.
 *
 * The video plays here, on this page. A file the collective has uploaded
 * plays in the browser's own player and never leaves the site at all. A
 * video that lives with a provider is held behind our own poster until the
 * visitor presses play, so nothing is fetched from them and nothing is
 * offered to them until that moment; the frame is then built here from the
 * video's id rather than asked for over oEmbed, so it does not depend on
 * this server being able to reach them. Anything we do not recognise still
 * falls back to oEmbed.
 */
function pamoja_event_video_embed( int $event_id ): string {
	$url = (string) pamoja_meta( $event_id, 'video_url' );
	if ( ! $url ) {
		return '';
	}
	$title = get_the_title( $event_id );

	$file = pamoja_video_file_src( $url );
	if ( $file ) {
		return sprintf(
			'<video controls playsinline preload="metadata"%s><source src="%s" type="%s" />%s</video>',
			pamoja_video_poster_attr( $event_id ),
			esc_url( $file['url'] ),
			esc_attr( $file['type'] ),
			esc_html__( 'Your browser cannot play this video.', 'pamoja' )
		);
	}

	$src = pamoja_video_player_src( $url );
	if ( $src ) {
		return pamoja_video_facade( $src, $title, $event_id );
	}

	$embed = wp_oembed_get( $url, array( 'width' => 1200 ) );
	return $embed ? (string) $embed : '';
}

/**
 * A video the collective has uploaded here, as {url, type}, or null.
 */
function pamoja_video_file_src( string $url ): ?array {
	$types = array( 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogv' => 'video/ogg', 'mov' => 'video/mp4' );
	$ext   = strtolower( (string) pathinfo( (string) wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	if ( ! isset( $types[ $ext ] ) ) {
		return null;
	}
	return array( 'url' => $url, 'type' => $types[ $ext ] );
}

/**
 * poster="…" for a self-hosted video, from the event's own cover photo.
 */
function pamoja_video_poster_attr( int $event_id ): string {
	$id = pamoja_thumbnail_id( $event_id );
	if ( ! $id ) {
		$gallery = pamoja_gallery( $event_id );
		$id      = $gallery ? $gallery[0] : 0;
	}
	$src = $id ? wp_get_attachment_image_url( $id, 'pamoja-wide' ) : '';
	return $src ? ' poster="' . esc_url( $src ) . '"' : '';
}

/**
 * Our own poster over a provider's player: nothing of theirs loads, and no
 * link of theirs is offered, until the visitor presses play. Without
 * JavaScript the player is simply there from the start.
 */
function pamoja_video_facade( string $src, string $title, int $event_id ): string {
	$cover = pamoja_cover_image( $event_id, 'pamoja-wide', array( 'loading' => 'lazy' ) );
	$frame = sprintf(
		'<iframe src="%s" title="%s" loading="lazy" width="1200" height="675" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
		esc_url( $src ),
		esc_attr( sprintf( __( 'Video: %s', 'pamoja' ), $title ) )
	);
	return sprintf(
		'<div class="player" data-player><button class="player-go" type="button" data-src="%1$s" data-title="%2$s">%3$s<span class="player-play" aria-hidden="true"></span><span class="player-label">%4$s</span></button><noscript>%5$s</noscript></div>',
		esc_attr( $src ),
		esc_attr( sprintf( __( 'Video: %s', 'pamoja' ), $title ) ),
		$cover, // Escaped by wp_get_attachment_image().
		esc_html( sprintf( __( 'Play the video: %s', 'pamoja' ), $title ) ),
		$frame // Escaped above.
	);
}

/**
 * The in-page player URL for a video link, or '' when we do not know the
 * provider well enough to build one.
 */
function pamoja_video_player_src( string $url ): string {
	$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	$host = preg_replace( '/^www\./', '', $host );
	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );

	$youtube = '';
	if ( 'youtu.be' === $host ) {
		$youtube = trim( $path, '/' );
	} elseif ( in_array( $host, array( 'youtube.com', 'm.youtube.com', 'youtube-nocookie.com' ), true ) ) {
		$youtube = (string) ( $query['v'] ?? '' );
		if ( ! $youtube && preg_match( '#^/(?:embed|shorts|live|v)/([^/?#]+)#', $path, $m ) ) {
			$youtube = $m[1];
		}
	}
	if ( $youtube && preg_match( '/^[A-Za-z0-9_-]{6,20}$/', $youtube ) ) {
		// youtube-nocookie keeps the visitor untracked until they press play.
		return add_query_arg(
			array( 'rel' => 0, 'modestbranding' => 1, 'playsinline' => 1 ),
			'https://www.youtube-nocookie.com/embed/' . $youtube
		);
	}

	if ( in_array( $host, array( 'vimeo.com', 'player.vimeo.com' ), true ) && preg_match( '#(\d{6,})#', $path, $m ) ) {
		return add_query_arg( array( 'dnt' => 1 ), 'https://player.vimeo.com/video/' . $m[1] );
	}

	return '';
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

