<?php
/**
 * Media albums: photo sets and video.
 */

get_header();
pamoja_page_header(
	array(
		'tag'   => pamoja_home( 'listing', 'media_tag' ),
		'title' => pamoja_home( 'listing', 'media_title' ),
		'lede'  => pamoja_home( 'listing', 'media_intro' ),
		'crop'  => 'canopy',
		'lit'   => 'canopy',
	)
);
?>
<div class="wrap listing">
	<?php if ( have_posts() ) : ?>
		<div class="strip strip--events">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/cards/tile', null, array( 'post' => get_post() ) ); ?>
			<?php endwhile; ?>
		</div>
		<?php pamoja_pagination(); ?>
	<?php else : ?>
		<p class="lede"><?php esc_html_e( 'The first photographs are waiting on consent. They will appear here.', 'pamoja' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
