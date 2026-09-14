<?php
/**
 * One blog post.
 */

get_header();
while ( have_posts() ) :
	the_post();
	$cover = pamoja_thumbnail_id( get_the_ID() );
	$terms = pamoja_post_branches( get_the_ID() );
	?>
	<article <?php post_class( 'article' ); ?>>
		<header class="article-head">
			<p class="tag"><a href="<?php echo esc_url( pamoja_blog_url() ); ?>">← <?php pamoja_home_text( 'listing', 'blog_tag' ); ?></a> · <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="lede"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
			<?php endif; ?>
		</header>
		<?php if ( $cover ) : ?>
			<figure class="article-cover"><?php echo wp_get_attachment_image( $cover, 'pamoja-wide', false, array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
				<?php if ( wp_get_attachment_caption( $cover ) ) : ?><figcaption><?php echo esc_html( wp_get_attachment_caption( $cover ) ); ?></figcaption><?php endif; ?>
			</figure>
		<?php endif; ?>
		<div class="prose entry-content"><?php the_content(); ?></div>
		<?php if ( $terms ) : ?>
			<footer class="article-foot"><?php pamoja_branch_tags( get_the_ID() ); ?></footer>
		<?php endif; ?>
		<p class="back-link"><a href="<?php echo esc_url( pamoja_blog_url() ); ?>">← <?php pamoja_home_text( 'listing', 'blog_title' ); ?></a></p>
	</article>
	<?php
endwhile;
get_footer();
