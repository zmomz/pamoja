<?php
/**
 * Sitemaps: no list of users, and no thin branch pages. Author archives show blog posts only, so a
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

/**
 * A branch page is its description and the events tagged with it. While the
 * description is still a sentence or two, the page is too thin to offer a
 * search engine: it is left out of the sitemap here and marked noindex by the
 * theme. It comes back by itself once the description is written out.
 */
const PAMOJA_BRANCH_MIN_WORDS = 150;

function pamoja_branch_is_thin( $term ): bool {
	$term = get_term( $term, 'branch' );
	if ( ! $term instanceof WP_Term ) {
		return true;
	}
	$words = preg_split( '/\s+/u', trim( wp_strip_all_tags( $term->description ) ), -1, PREG_SPLIT_NO_EMPTY );
	return count( (array) $words ) < PAMOJA_BRANCH_MIN_WORDS;
}

/**
 * @return int[]
 */
function pamoja_thin_branch_ids(): array {
	static $ids = null;
	if ( null === $ids ) {
		$terms = get_terms( array( 'taxonomy' => 'branch', 'hide_empty' => false ) );
		$ids   = is_array( $terms ) ? array_values( array_map( static fn ( WP_Term $t ): int => $t->term_id, array_filter( $terms, 'pamoja_branch_is_thin' ) ) ) : array();
	}
	return $ids;
}

add_filter(
	'wp_sitemaps_taxonomies_query_args',
	static function ( array $args, string $taxonomy ): array {
		if ( 'branch' === $taxonomy && pamoja_thin_branch_ids() ) {
			$args['exclude'] = array_merge( (array) ( $args['exclude'] ?? array() ), pamoja_thin_branch_ids() );
		}
		return $args;
	},
	10,
	2
);

// With every branch thin, the branch sitemap would be an empty list (and a 404), so it is not offered at all.
add_filter(
	'wp_sitemaps_taxonomies',
	static function ( array $taxonomies ): array {
		if ( isset( $taxonomies['branch'] ) ) {
			$all = get_terms( array( 'taxonomy' => 'branch', 'hide_empty' => true, 'fields' => 'ids' ) );
			if ( ! is_array( $all ) || ! array_diff( $all, pamoja_thin_branch_ids() ) ) {
				unset( $taxonomies['branch'] );
			}
		}
		return $taxonomies;
	}
);
