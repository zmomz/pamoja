<?php
/**
 * One blog post card. Expects $args['post'].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$item = $args['post'] ?? null;
if ( ! $item instanceof WP_Post ) {
	return;
}
$cover = pamoja_cover_image( $item->ID );
?>
<article class="post<?php echo $cover ? '' : ' post--plain'; ?>">
	<?php if ( $cover ) : ?>
		<a class="post-cover" href="<?php echo esc_url( get_permalink( $item ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo $cover; // Escaped by wp_get_attachment_image(). ?></a>
	<?php endif; ?>
	<div class="post-body">
		<p class="post-meta"><time datetime="<?php echo esc_attr( get_the_date( 'c', $item ) ); ?>"><?php echo esc_html( get_the_date( '', $item ) ); ?></time></p>
		<h3><a href="<?php echo esc_url( get_permalink( $item ) ); ?>"><?php echo esc_html( get_the_title( $item ) ); ?></a></h3>
		<p class="teaser"><?php echo esc_html( pamoja_teaser( $item, 160 ) ); ?></p>
		<a class="lnk" href="<?php echo esc_url( get_permalink( $item ) ); ?>"><?php esc_html_e( 'Read on →', 'pamoja' ); ?></a>
	</div>
</article>
