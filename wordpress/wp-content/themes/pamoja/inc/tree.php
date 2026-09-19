<?php
/**
 * The tree, at every size: the hero, the menu, the header mark, a page's
 * slice. Each instance is the same SVG inlined with its ids suffixed (a
 * shared <symbol> + <use> made Chromium re-style every copy each animation
 * frame). Parts are dimmed by the .lit-* classes. Crops (the canopy for
 * Events and Blog) are just a different viewBox.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The raw SVG fragment, read once per request.
 */
function pamoja_tree_markup(): string {
	static $svg = null;
	if ( null === $svg ) {
		ob_start();
		get_template_part( 'template-parts/tree/tree' );
		$svg = trim( (string) ob_get_clean() );
	}
	return $svg;
}

/**
 * ViewBoxes for the crops each door opens on.
 */
function pamoja_tree_viewbox( string $crop ): string {
	switch ( $crop ) {
		case 'canopy':
			return '0 40 720 420';
		case 'seeds':
			return '0 420 720 380';
		case 'roots':
			return '0 500 720 300';
		default:
			return '0 0 720 800';
	}
}

/**
 * Print one tree.
 *
 * @param array{class?:string, lit?:string, crop?:string, id?:string, name?:string, label?:string} $args
 *   lit   — the part to light ('all' for none dimmed).
 *   crop  — 'full' | 'canopy' | 'seeds' | 'roots'.
 *   name  — a view-transition-name, so the tree morphs between pages.
 */
function pamoja_tree( array $args = array() ) {
	static $n = 0;
	$n++;
	$lit   = $args['lit'] ?? 'all';
	$crop  = $args['crop'] ?? 'full';
	$class = 'tree-map lit-' . sanitize_html_class( $lit ) . ' crop-' . sanitize_html_class( $crop ) . ( ! empty( $args['class'] ) ? ' ' . $args['class'] : '' );
	$id    = ! empty( $args['id'] ) ? ' id="' . esc_attr( $args['id'] ) . '"' : '';
	$style = ! empty( $args['name'] ) ? ' style="view-transition-name:' . esc_attr( $args['name'] ) . '"' : '';
	$label = $args['label'] ?? '';
	$attrs = $label ? ' role="img" aria-label="' . esc_attr( $label ) . '"' : ' aria-hidden="true" focusable="false"';

	$svg = pamoja_tree_markup();
	foreach ( array( 'ht-sky', 'ht-soil-fade', 'ht-soil-mask', 'ht-soil', 'tree-grove', 'tree-soil', 'tree-roots', 'tree-trunk', 'tree-branches', 'tree-canopy', 'tree-seeds' ) as $tree_id ) {
		$svg = str_replace( array( 'id="' . $tree_id . '"', 'url(#' . $tree_id . ')' ), array( 'id="' . $tree_id . '-' . $n . '"', 'url(#' . $tree_id . '-' . $n . ')' ), $svg );
	}
	$svg = str_replace( array( '__VIEWBOX__', '__ATTRS__' ), array( esc_attr( pamoja_tree_viewbox( $crop ) ), $attrs ), $svg );
	if ( $label ) {
		$svg = preg_replace( '/^(<svg[^>]*>)/', '$1<title>' . esc_html( $label ) . '</title>', $svg, 1 );
	}
	printf(
		'<div class="%s"%s%s data-lit="%s">%s</div>',
		esc_attr( $class ),
		$id, // Escaped above.
		$style, // Escaped above.
		esc_attr( $lit ),
		$svg // Static theme markup with escaped attributes.
	);
}

/**
 * The hotspots: every place in the manager's words, positioned on the full
 * tree. Percentages are of the tree's box. Engage is not on the tree — it is
 * a door in the header and the footer, not a part of the plant — and how we
 * work together sits in the roots beside the ethics it grows from.
 *
 * @return array<int, array{id:string, label:string, sub?:string, url:string, part:string, x:int, y:int, side:string, kind?:string}>
 */
function pamoja_tree_spots(): array {
	$map  = pamoja_site_map();
	$spot = function ( string $door, string $id, int $x, int $y, string $side = 'right', string $sub = '', string $kind = '' ) {
		$item = pamoja_site_map_item( $door, $id );
		if ( ! $item ) {
			return null;
		}
		return array(
			'id'    => $id,
			'label' => $item['label'],
			'sub'   => $sub,
			'url'   => $item['url'],
			'part'  => $item['part'],
			'x'     => $x,
			'y'     => $y,
			'side'  => $side,
			'kind'  => $kind,
		);
	};
	$upcoming = pamoja_upcoming_events();
	$spots    = array(
		array( 'id' => 'blog', 'label' => $map['blog']['label'], 'sub' => __( 'stories from the work', 'pamoja' ), 'url' => $map['blog']['url'], 'part' => 'canopy', 'x' => 44, 'y' => 14, 'side' => 'right', 'kind' => '' ),
		array( 'id' => 'events', 'label' => $map['events']['label'], 'sub' => pamoja_upcoming_count_label( count( $upcoming ) ), 'url' => $map['events']['url'], 'part' => 'canopy', 'x' => 58, 'y' => 26, 'side' => 'right', 'kind' => 'fruit' ),
		$spot( 'about', 'our-story', 51, 58 ),
		$spot( 'about', 'how-we-work-together', 20, 82 ),
		$spot( 'about', 'ethics-and-values', 25, 91 ),
		$spot( 'about', 'why-we-exist', 87, 88, 'left' ),
	);
	return array_values( array_filter( $spots ) );
}

/**
 * The hero's grove: the same tree Pamoja's is, at the same height, standing
 * across the whole hero rather than tucked behind the hill.
 *
 * The collective is one organization among others on this land. Drawing the
 * neighbours small would have said the opposite, so every tree here is the
 * height of Pamoja's own (602 units, canopy at y=68 down to the soil line at
 * y=672 — hence scale 2.82 on a glyph 213 tall) and only opacity, not size,
 * tells you which one this site belongs to. Alternate trees are mirrored: a
 * grove rather than one tree copied, with none of them made bigger for it.
 */
function pamoja_hero_grove( int $count = 7 ) {
	$glyph = '<path d="M-7 0 C-5 -44 -4 -88 -3 -128 L3 -128 C4 -88 5 -44 7 0 Z" class="f-karkadeh-deep" />'
		. '<g fill="none" class="st-karkadeh-deep" stroke-width="6" stroke-linecap="round">'
		. '<path d="M0 -92 C-12 -104 -24 -116 -36 -126" />'
		. '<path d="M0 -112 C11 -124 22 -134 33 -142" />'
		. '</g>'
		. '<ellipse cx="-38" cy="-136" rx="38" ry="33" class="f-river-pale" />'
		. '<ellipse cx="40" cy="-142" rx="40" ry="35" class="f-river" />'
		. '<ellipse cx="0" cy="-168" rx="52" ry="45" class="f-river-light" />';

	echo '<div class="hero-grove" aria-hidden="true">';
	for ( $i = 0; $i < $count; $i++ ) {
		printf(
			'<svg viewBox="0 0 720 800" preserveAspectRatio="xMidYMax meet" aria-hidden="true" focusable="false"><g transform="translate(360 672) scale(%s2.82 2.82)">%s</g></svg>',
			( $i % 2 ) ? '-' : '',
			$glyph // Static theme markup.
		);
	}
	echo '</div>';
}

/**
 * Print the hotspot links over a full tree.
 *
 * @param bool $subs Whether to show the small second line under a label.
 */
function pamoja_tree_hotspots( bool $subs = true ) {
	foreach ( pamoja_tree_spots() as $s ) {
		printf(
			'<a class="spot spot--%1$s%2$s%3$s%4$s" href="%5$s" data-part="%1$s" style="left:%6$d%%;top:%7$d%%"><i aria-hidden="true"></i><span>%8$s%9$s</span></a>',
			esc_attr( $s['part'] ),
			'left' === $s['side'] ? ' spot--l' : '',
			$s['kind'] ? ' spot--' . esc_attr( $s['kind'] ) : '',
			! empty( $s['id'] ) ? ' at-' . esc_attr( $s['id'] ) : '',
			esc_url( $s['url'] ),
			(int) $s['x'],
			(int) $s['y'],
			esc_html( $s['label'] ),
			$subs && $s['sub'] ? '<small>' . esc_html( $s['sub'] ) . '</small>' : ''
		);
	}
}

/**
 * The living part of the map: one fruit on the canopy per upcoming event.
 * Positions are of the full tree; the caller crops.
 */
function pamoja_tree_fruits( string $crop = 'full' ) {
	$events = pamoja_upcoming_events();
	if ( ! $events ) {
		return;
	}
	$positions = array(
		array( 64, 22 ),
		array( 36, 30 ),
		array( 52, 12 ),
		array( 26, 48 ),
		array( 72, 40 ),
	);
	// The canopy crop shows rows 40–460 of 800: rescale y.
	foreach ( array_slice( $events, 0, count( $positions ) ) as $i => $event ) {
		list( $x, $y ) = $positions[ $i ];
		if ( 'canopy' === $crop ) {
			$y = (int) round( ( $y * 8 - 40 ) / 4.2 );
		}
		printf(
			'<a class="fruit f%1$d" href="%2$s" style="left:%3$d%%;top:%4$d%%" aria-label="%5$s"><span class="fruit-tip">%6$s</span></a>',
			(int) ( $i % 3 ) + 1,
			esc_url( get_permalink( $event ) ),
			$x,
			$y,
			esc_attr( sprintf( __( 'Upcoming: %s', 'pamoja' ), get_the_title( $event ) ) ),
			esc_html( get_the_title( $event ) )
		);
	}
}
