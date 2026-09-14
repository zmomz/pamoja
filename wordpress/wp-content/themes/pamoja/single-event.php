<?php
/**
 * One event: the story, the video if there is one, who was credited, the
 * branches it served, the consented photos — and, if it is upcoming, a way
 * to be told when it is announced.
 */

get_header();
while ( have_posts() ) :
	the_post();
	$id       = get_the_ID();
	$status   = pamoja_meta( $id, 'status', 'past' );
	$location = (string) pamoja_meta( $id, 'location' );
	$quote    = (string) pamoja_meta( $id, 'pull_quote' );
	$partners = pamoja_event_partners( $id );
	$albums   = pamoja_event_albums( $id );
	$cover    = pamoja_thumbnail_id( $id );
	$gallery  = pamoja_gallery( $id );
	$video    = pamoja_event_video_embed( $id );
	$keep     = isset( $_GET['keep'] ) ? sanitize_key( $_GET['keep'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<article <?php post_class( 'article single-event' ); ?>>
		<header class="article-head">
			<p class="tag"><a href="<?php echo esc_url( pamoja_events_url( in_array( $status, array( 'upcoming', 'ongoing' ), true ) ? $status : 'past' ) ); ?>">← <?php pamoja_home_text( 'listing', 'events_tag' ); ?></a></p>
			<h1><?php the_title(); ?></h1>
			<p class="article-meta">
				<span class="badge badge-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( pamoja_event_status_label( $id ) ); ?></span>
				<span><?php echo esc_html( pamoja_event_when( $id ) ); ?><?php echo $location ? ' · ' . esc_html( $location ) : ''; ?></span>
			</p>
		</header>

		<?php if ( $video ) : ?>
			<div class="video-embed"><?php echo $video; // oEmbed HTML from a trusted provider. ?></div>
		<?php elseif ( $cover ) : ?>
			<figure class="article-cover"><?php echo wp_get_attachment_image( $cover, 'pamoja-wide', false, array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
				<?php if ( wp_get_attachment_caption( $cover ) ) : ?><figcaption><?php echo esc_html( wp_get_attachment_caption( $cover ) ); ?></figcaption><?php endif; ?>
			</figure>
		<?php endif; ?>

		<div class="article-grid">
			<div class="prose entry-content">
				<?php if ( $quote ) : ?>
					<blockquote class="pull-quote"><p><?php echo esc_html( $quote ); ?></p></blockquote>
				<?php endif; ?>
				<?php the_content(); ?>
				<?php if ( 'upcoming' === $status ) : ?>
					<div class="keep-box">
						<p class="keep-lead"><?php esc_html_e( 'Want to know when this is announced?', 'pamoja' ); ?></p>
						<?php if ( 'sent' === $keep ) : ?>
							<p class="keep-done" role="status"><?php pamoja_home_text( 'coming', 'keep_done' ); ?></p>
						<?php else : ?>
							<?php if ( 'error' === $keep ) : ?><p class="keep-error" role="alert"><?php esc_html_e( 'That didn’t go through. Please check the address and try again.', 'pamoja' ); ?></p><?php endif; ?>
							<?php get_template_part( 'template-parts/keep-posted', null, array( 'event' => get_post() ) ); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<aside class="article-aside">
				<?php if ( pamoja_post_branches( $id ) ) : ?>
					<h2 class="aside-heading"><?php esc_html_e( 'Branches served', 'pamoja' ); ?></h2>
					<?php pamoja_branch_tags( $id ); ?>
				<?php endif; ?>
				<?php if ( $partners ) : ?>
					<h2 class="aside-heading"><?php esc_html_e( 'With', 'pamoja' ); ?></h2>
					<ul class="aside-list">
						<?php foreach ( $partners as $partner ) : ?>
							<?php $url = (string) pamoja_meta( $partner->ID, 'url' ); ?>
							<li>
								<strong><?php if ( $url ) : ?><a href="<?php echo esc_url( $url ); ?>" rel="noopener"><?php echo esc_html( get_the_title( $partner ) ); ?></a><?php else : ?><?php echo esc_html( get_the_title( $partner ) ); ?><?php endif; ?></strong>
								<span><?php echo esc_html( pamoja_partner_contribution( $partner ) ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( $albums ) : ?>
					<h2 class="aside-heading"><?php esc_html_e( 'Albums', 'pamoja' ); ?></h2>
					<ul class="aside-list">
						<?php foreach ( $albums as $album ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $album ) ); ?>"><?php echo esc_html( get_the_title( $album ) ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</aside>
		</div>

		<?php if ( $gallery && ( $video || count( $gallery ) > 1 || $gallery[0] !== $cover ) ) : ?>
			<section class="article-gallery" aria-labelledby="gallery-heading">
				<h2 class="sub-head" id="gallery-heading"><?php pamoja_home_text( 'listing', 'photos_heading' ); ?></h2>
				<?php pamoja_render_gallery( $id ); ?>
				<?php pamoja_home_para( 'listing', 'consent_note', 'consent-note' ); ?>
			</section>
		<?php endif; ?>

		<p class="back-link"><a href="<?php echo esc_url( pamoja_events_url() ); ?>">← <?php pamoja_home_text( 'listing', 'events_title' ); ?></a></p>
	</article>
	<?php
endwhile;
get_footer();
