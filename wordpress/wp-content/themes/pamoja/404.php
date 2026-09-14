<?php
/**
 * Not found.
 */

get_header();
?>
<section class="article not-found">
	<header class="article-head">
		<p class="tag"><?php pamoja_home_text( 'listing', 'nf_tag' ); ?></p>
		<h1><?php pamoja_home_text( 'listing', 'nf_title' ); ?></h1>
	</header>
	<div class="lede prose"><?php pamoja_home_html( 'listing', 'nf_body' ); ?></div>
	<p class="back-link"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '← Back to the tree', 'pamoja' ); ?></a></p>
</section>
<?php
get_footer();
