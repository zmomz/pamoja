<?php
/**
 * The tree, at every size: the hero, the menu, the header mark, a page's
 * slice. Each instance is the same SVG inlined with its ids suffixed (a
 * shared <symbol> + <use> made Chromium re-style every copy each animation
 * frame). Parts are dimmed by the .lit-* classes. Crops (the canopy for
 * Events and Blog, the seeds for Engage) are just a different viewBox.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PAMOJA_TREE_PARTS = array( 'soil', 'roots', 'trunk', 'branches', 'canopy', 'seeds' );

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
 * Kept for templates that may still call it; the tree no longer needs a
 * shared symbol.
 */
function pamoja_tree_symbol() {}

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
	foreach ( array( 'ht-sky', 'ht-soil-fade', 'ht-soil-mask', 'ht-soil', 'tree-soil', 'tree-roots', 'tree-trunk', 'tree-branches', 'tree-canopy', 'tree-seeds' ) as $tree_id ) {
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
 * tree. Percentages are of the tree's box.
 *
 * @return array<int, array{label:string, sub?:string, url:string, part:string, x:int, y:int, side:string, kind?:string}>
 */
function pamoja_tree_spots(): array {
	$map  = pamoja_site_map();
	$spot = function ( string $door, int $i, int $x, int $y, string $side = 'right', string $sub = '', string $kind = '' ) use ( $map ) {
		$item = $map[ $door ]['items'][ $i ] ?? null;
		if ( ! $item ) {
			return null;
		}
		return array(
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
		array( 'label' => $map['blog']['label'], 'sub' => __( 'stories from the work', 'pamoja' ), 'url' => $map['blog']['url'], 'part' => 'canopy', 'x' => 44, 'y' => 14, 'side' => 'right', 'kind' => '' ),
		array( 'label' => $map['events']['label'], 'sub' => pamoja_upcoming_count_label( count( $upcoming ) ), 'url' => $map['events']['url'], 'part' => 'canopy', 'x' => 58, 'y' => 26, 'side' => 'right', 'kind' => 'fruit' ),
		$spot( 'about', 2, 22, 42 ),
		$spot( 'about', 1, 51, 58 ),
		array( 'label' => $map['engage']['label'], 'sub' => __( 'volunteer · partner · support', 'pamoja' ), 'url' => $map['engage']['url'], 'part' => 'seeds', 'x' => 62, 'y' => 76, 'side' => 'left', 'kind' => 'seed' ),
		$spot( 'about', 3, 30, 89 ),
		$spot( 'about', 0, 76, 93, 'left' ),
	);
	return array_values( array_filter( $spots ) );
}

/**
 * Print the hotspot links over a full tree.
 *
 * @param bool $subs Whether to show the small second line under a label.
 */
function pamoja_tree_hotspots( bool $subs = true ) {
	foreach ( pamoja_tree_spots() as $s ) {
		printf(
			'<a class="spot spot--%1$s%2$s%3$s" href="%4$s" data-part="%1$s" style="left:%5$d%%;top:%6$d%%"><i aria-hidden="true"></i><span>%7$s%8$s</span></a>',
			esc_attr( $s['part'] ),
			'left' === $s['side'] ? ' spot--l' : '',
			$s['kind'] ? ' spot--' . esc_attr( $s['kind'] ) : '',
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
