<?php
/**
 * The blog listing (the page chosen as "Posts page", /blog/): the leaves.
 */

get_header();
$page  = get_option( 'page_for_posts' ) ? get_post( (int) get_option( 'page_for_posts' ) ) : null;
$intro = $page && trim( wp_strip_all_tags( $page->post_content ) ) ? wp_strip_all_tags( $page->post_content ) : pamoja_home( 'listing', 'blog_intro' );

pamoja_page_header(
	array(
		'tag'   => pamoja_home( 'listing', 'blog_tag' ),
		'title' => pamoja_home( 'listing', 'blog_title' ),
		'lede'  => $intro,
		'crop'  => 'canopy',
		'lit'   => 'canopy',
	)
);
?>
<div class="wrap listing">
	<?php if ( have_posts() ) : ?>
		<div class="posts">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/cards/news', null, array( 'post' => get_post() ) ); ?>
			<?php endwhile; ?>
		</div>
		<?php pamoja_pagination(); ?>
	<?php else : ?>
		<p class="lede"><?php pamoja_home_text( 'listing', 'blog_empty' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
