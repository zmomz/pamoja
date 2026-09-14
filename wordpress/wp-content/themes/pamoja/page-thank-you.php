<?php
/**
 * The thank-you page after an inquiry (slug: thank-you).
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<section class="article thank-you">
		<header class="article-head">
			<p class="tag"><?php esc_html_e( 'Thank you', 'pamoja' ); ?></p>
			<h1><?php the_title(); ?></h1>
		</header>
		<div class="prose entry-content"><?php the_content(); ?></div>
		<p class="back-link"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '← Back to the tree', 'pamoja' ); ?></a></p>
	</section>
	<?php
endwhile;
get_footer();
