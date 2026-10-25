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

/**
 * The events listing (/events/) is the page a search for "Pamoja events" should land on, but WordPress's sitemaps
 * list posts, pages and terms only, never a post type's archive. This provider adds the archives of the event and
 * album types, each dated by its last edited entry, and only while they have something published (an empty archive
 * is noindex, see the theme's pamoja_robots()). The blog page joins the page sitemap by itself once a story is
 * published; until then it is noindex, see the theme's pamoja_unlisted_page_ids().
 */
final class Pamoja_Archive_Sitemap_Provider extends WP_Sitemaps_Provider {
	public function __construct() {
		$this->name        = 'archives';
		$this->object_type = 'archive';
	}

	/**
	 * @param int    $page_num
	 * @param string $object_subtype
	 * @return array<int, array{loc:string, lastmod:string}>
	 */
	public function get_url_list( $page_num, $object_subtype = '' ) {
		if ( 1 !== (int) $page_num ) {
			return array();
		}
		$list = array();
		foreach ( array( 'event', 'album' ) as $post_type ) {
			$link   = get_post_type_archive_link( $post_type );
			$latest = get_posts(
				array(
					'post_type'      => $post_type,
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'orderby'        => 'modified',
					'order'          => 'DESC',
				)
			);
			if ( $link && $latest ) {
				$list[] = array(
					'loc'     => $link,
					'lastmod' => wp_date( DATE_W3C, strtotime( $latest[0]->post_modified_gmt ) ),
				);
			}
		}
		return $list;
	}

	/**
	 * @param string $object_subtype
	 * @return int
	 */
	public function get_max_num_pages( $object_subtype = '' ) {
		return $this->get_url_list( 1 ) ? 1 : 0;
	}
}

add_action(
	'wp_sitemaps_init',
	static function ( WP_Sitemaps $sitemaps ): void {
		$sitemaps->registry->add_provider( 'archives', new Pamoja_Archive_Sitemap_Provider() );
	}
);
