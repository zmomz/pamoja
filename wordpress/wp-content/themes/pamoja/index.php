<?php
/**
 * Fallback listing (search results, taxonomy archives, anything else).
 */

get_header();
if ( is_search() ) {
	pamoja_page_head( __( 'Search', 'pamoja' ), sprintf( __( 'Results for “%s”', 'pamoja' ), get_search_query() ) );
} elseif ( is_tax( 'branch' ) ) {
	$term = get_queried_object();
	pamoja_page_head( __( 'Branch', 'pamoja' ), $term->name, $term->description );
} else {
	pamoja_page_head( '', wp_strip_all_tags( get_the_archive_title() ) );
}
?>
<div class="wrap listing">
	<?php if ( have_posts() ) : ?>
		<div class="posts">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php if ( in_array( get_post_type(), array( 'event', 'album' ), true ) ) : ?>
					<?php get_template_part( 'template-parts/cards/tile', null, array( 'post' => get_post() ) ); ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/cards/news', null, array( 'post' => get_post() ) ); ?>
				<?php endif; ?>
			<?php endwhile; ?>
		</div>
		<?php pamoja_pagination(); ?>
	<?php else : ?>
		<p class="lede"><?php esc_html_e( 'Nothing here yet.', 'pamoja' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
