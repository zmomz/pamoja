<?php
/**
 * Template helpers: page headers, galleries, cards, dates, small parts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The header of an inner page: eyebrow, title, lede, and the door's slice of
 * the tree on the right (the same slice the home tree morphs into).
 *
 * @param array{tag:string, title:string, lede?:string, crop?:string, lit?:string, spots?:array, fruits?:bool} $args
 */
function pamoja_page_header( array $args ) {
	$crop = $args['crop'] ?? '';
	?>
	<header class="ph<?php echo $crop ? '' : ' ph--plain'; ?>">
		<div class="ph-copy">
			<?php if ( ! empty( $args['tag'] ) ) : ?><p class="tag"><?php echo esc_html( $args['tag'] ); ?></p><?php endif; ?>
			<h1><?php echo esc_html( $args['title'] ); ?></h1>
			<?php if ( ! empty( $args['lede'] ) ) : ?><p class="lede"><?php echo nl2br( esc_html( $args['lede'] ) ); ?></p><?php endif; ?>
		</div>
		<?php if ( $crop ) : ?>
			<div class="ph-slice">
				<?php pamoja_tree( array( 'crop' => $crop, 'lit' => $args['lit'] ?? 'all', 'name' => 'tree' ) ); ?>
				<?php if ( ! empty( $args['fruits'] ) ) { pamoja_tree_fruits( $crop ); } ?>
				<?php foreach ( $args['spots'] ?? array() as $s ) : ?>
					<a class="spot spot--<?php echo esc_attr( $s['part'] ); ?><?php echo ! empty( $s['left'] ) ? ' spot--l' : ''; ?><?php echo ! empty( $s['kind'] ) ? ' spot--' . esc_attr( $s['kind'] ) : ''; ?>" href="<?php echo esc_url( $s['url'] ); ?>" style="left:<?php echo (int) $s['x']; ?>%;top:<?php echo (int) $s['y']; ?>%"><i aria-hidden="true"></i><span><?php echo esc_html( $s['label'] ); ?></span></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Page header for listing pages without a tree slice.
 */
function pamoja_page_head( string $tag, string $title, string $intro = '' ) {
	pamoja_page_header( array( 'tag' => $tag, 'title' => $title, 'lede' => $intro ) );
}

/**
 * The sticky chip bar under a page header: the stops on this page.
 *
 * @param array<int, array{id:string, label:string}> $stops
 */
function pamoja_toc( array $stops, string $current = '' ) {
	echo '<nav class="toc" aria-label="' . esc_attr__( 'On this page', 'pamoja' ) . '">';
	foreach ( $stops as $s ) {
		printf( '<a href="#%1$s" data-stop="%1$s"%3$s>%2$s</a>', esc_attr( $s['id'] ), esc_html( $s['label'] ), $s['id'] === $current ? ' class="on" aria-current="true"' : '' );
	}
	echo '</nav>';
}

/**
 * A gallery of consented photos: a responsive grid of figures with captions.
 */
function pamoja_render_gallery( int $post_id, string $class = 'photo-grid' ) {
	$ids = pamoja_gallery( $post_id );
	if ( ! $ids ) {
		return;
	}
	echo '<div class="' . esc_attr( $class ) . '">';
	foreach ( $ids as $id ) {
		$caption = wp_get_attachment_caption( $id );
		$full    = wp_get_attachment_image_url( $id, 'pamoja-wide' );
		echo '<figure class="photo">';
		echo '<a href="' . esc_url( $full ) . '" class="photo-link">';
		echo wp_get_attachment_image( $id, 'pamoja-card', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) );
		echo '</a>';
		if ( $caption ) {
			echo '<figcaption>' . esc_html( $caption ) . '</figcaption>';
		}
		echo '</figure>';
	}
	echo '</div>';
}

/**
 * Cover image for a card: the consented featured image, else the first
 * consented gallery photo. Returns '' when there is nothing to show.
 */
function pamoja_cover_image( int $post_id, string $size = 'pamoja-card', array $attrs = array() ): string {
	$id = pamoja_thumbnail_id( $post_id );
	if ( ! $id ) {
		$gallery = pamoja_gallery( $post_id );
		$id      = $gallery ? $gallery[0] : 0;
	}
	if ( ! $id ) {
		return '';
	}
	return wp_get_attachment_image( $id, $size, false, array_merge( array( 'loading' => 'lazy', 'decoding' => 'async' ), $attrs ) );
}

/**
 * Line-drawn glyphs for the root cards.
 */
function pamoja_glyph( string $name ) {
	$paths = array(
		'dish'    => '<path d="M20 22 h80" /><path d="M26 22 c0 22 14 34 34 34 s34 -12 34 -34" /><path d="M52 64 h16" />',
		'two-row' => '<path d="M8 26 c14 -10 28 -10 42 0 s28 10 42 0 c8 -6 16 -8 20 -6" /><path d="M8 48 c14 -10 28 -10 42 0 s28 10 42 0 c8 -6 16 -8 20 -6" />',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return;
	}
	echo '<figure class="glyph" aria-hidden="true"><svg viewBox="0 0 120 72" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">' . $paths[ $name ] . '</svg></figure>'; // Static markup.
}

/**
 * Date line for an event: the display string, else the start date.
 */
function pamoja_event_when( int $event_id ): string {
	$display = (string) pamoja_meta( $event_id, 'date_display' );
	if ( $display ) {
		return $display;
	}
	$start = pamoja_event_date( $event_id, 'start_date' );
	return $start ? $start->format( 'F Y' ) : get_the_date( 'F Y', $event_id );
}

/**
 * A stored Y-m-d as a date in the site's timezone (so "2026-10-01" is
 * October, whatever the server's clock says), or null.
 */
function pamoja_event_date( int $event_id, string $key ): ?DateTimeImmutable {
	$raw = (string) pamoja_meta( $event_id, $key );
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ) {
		return null;
	}
	$date = DateTimeImmutable::createFromFormat( '!Y-m-d', $raw, wp_timezone() );
	return $date ?: null;
}

/**
 * Two short lines for the date block on an upcoming event: "Nov–Dec" / "2026".
 *
 * @return array{0:string, 1:string}
 */
function pamoja_event_date_block( int $event_id ): array {
	$s = pamoja_event_date( $event_id, 'start_date' );
	$e = pamoja_event_date( $event_id, 'end_date' );
	if ( ! $s ) {
		return array( pamoja_event_when( $event_id ), '' );
	}
	if ( $e && $e->format( 'Y' ) === $s->format( 'Y' ) && $e->format( 'n' ) !== $s->format( 'n' ) ) {
		return array( date_i18n( 'M', $s->getTimestamp() + $s->getOffset() ) . '–' . date_i18n( 'M', $e->getTimestamp() + $e->getOffset() ), $s->format( 'Y' ) );
	}
	return array( date_i18n( 'M', $s->getTimestamp() + $s->getOffset() ), $s->format( 'Y' ) );
}

/**
 * Date line for an album: the display string, else the publish date.
 */
function pamoja_album_when( int $album_id ): string {
	$display = (string) pamoja_meta( $album_id, 'date_display' );
	return $display ?: get_the_date( 'F Y', $album_id );
}

/**
 * Branch tag pills for a post.
 */
function pamoja_branch_tags( int $post_id ) {
	$terms = pamoja_post_branches( $post_id );
	if ( ! $terms ) {
		return;
	}
	echo '<ul class="branch-tags" aria-label="' . esc_attr__( 'Branches this gathering served', 'pamoja' ) . '">';
	foreach ( $terms as $term ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
	}
	echo '</ul>';
}

/**
 * "With A · B · C" credit line for an event.
 */
function pamoja_credits_line( int $event_id ): string {
	$partners = pamoja_event_partners( $event_id );
	if ( ! $partners ) {
		return '';
	}
	return sprintf( __( 'With %s', 'pamoja' ), implode( ' · ', array_map( 'get_the_title', $partners ) ) );
}

/**
 * Pagination in the site's voice.
 */
function pamoja_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => __( '← Newer', 'pamoja' ),
			'next_text'          => __( 'Older →', 'pamoja' ),
			'screen_reader_text' => __( 'Pages', 'pamoja' ),
		)
	);
}

/**
 * A row of photo tiles for a list of events or albums.
 *
 * @param WP_Post[] $posts
 */
function pamoja_tiles( array $posts, string $class = 'strip' ) {
	if ( ! $posts ) {
		return;
	}
	echo '<div class="' . esc_attr( $class ) . '">';
	foreach ( $posts as $post ) {
		get_template_part( 'template-parts/cards/tile', null, array( 'post' => $post ) );
	}
	echo '</div>';
}
