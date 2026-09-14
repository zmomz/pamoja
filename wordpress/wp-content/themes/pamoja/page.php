<?php
/**
 * An ordinary page.
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'article' ); ?>>
		<header class="article-head">
			<h1><?php the_title(); ?></h1>
		</header>
		<div class="prose entry-content"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;
get_footer();
