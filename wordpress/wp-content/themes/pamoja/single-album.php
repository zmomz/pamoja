<?php
/**
 * One album: video first if there is one, then the consented photos.
 */

get_header();
while ( have_posts() ) :
	the_post();
	$id    = get_the_ID();
	$video = (string) pamoja_meta( $id, 'video_url' );
	$event = (int) pamoja_meta( $id, 'event' );
	$embed = pamoja_event_video_embed( $id );
	?>
	<article <?php post_class( 'article article--wide single-album' ); ?>>
		<header class="article-head">
			<p class="tag"><a href="<?php echo esc_url( get_post_type_archive_link( 'album' ) ); ?>">← <?php pamoja_home_text( 'listing', 'media_tag' ); ?></a> · <?php echo esc_html( pamoja_album_when( $id ) ); ?><?php if ( $event && 'publish' === get_post_status( $event ) ) : ?> · <a href="<?php echo esc_url( get_permalink( $event ) ); ?>"><?php echo esc_html( get_the_title( $event ) ); ?></a><?php endif; ?></p>
			<h1><?php the_title(); ?></h1>
		</header>
		<?php if ( trim( get_the_content() ) ) : ?>
			<div class="prose entry-content"><?php the_content(); ?></div>
		<?php endif; ?>
		<?php if ( $embed ) : ?>
			<div class="video-embed"><?php echo $embed; // oEmbed HTML from a trusted provider. ?></div>
		<?php elseif ( $video ) : ?>
			<p><a href="<?php echo esc_url( $video ); ?>" rel="noopener"><?php esc_html_e( 'Watch the video →', 'pamoja' ); ?></a></p>
		<?php endif; ?>
		<?php if ( pamoja_gallery( $id ) ) : ?>
			<?php pamoja_render_gallery( $id ); ?>
			<?php pamoja_home_para( 'listing', 'consent_note', 'consent-note' ); ?>
		<?php elseif ( ! $embed ) : ?>
			<p class="lede"><?php esc_html_e( 'The photographs from this gathering are waiting on consent.', 'pamoja' ); ?></p>
		<?php endif; ?>
		<p class="back-link"><a href="<?php echo esc_url( get_post_type_archive_link( 'album' ) ); ?>">← <?php pamoja_home_text( 'listing', 'media_title' ); ?></a></p>
	</article>
	<?php
endwhile;
get_footer();
