<?php
/**
 * An upcoming event: date block, title, teaser, credits, and "tell me when
 * it's announced". Expects $args['event'].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$event = $args['event'] ?? null;
if ( ! $event instanceof WP_Post ) {
	return;
}
list( $d1, $d2 ) = pamoja_event_date_block( $event->ID );
$credits         = pamoja_credits_line( $event->ID );
$location        = (string) pamoja_meta( $event->ID, 'location' );
?>
<article class="ev">
	<div class="date" aria-hidden="true"><?php echo esc_html( $d1 ); ?><?php if ( $d2 ) : ?><br><?php echo esc_html( $d2 ); ?><?php endif; ?></div>
	<div class="ev-body">
		<p class="ev-meta"><?php echo esc_html( pamoja_event_when( $event->ID ) ); ?><?php echo $location ? ' · ' . esc_html( $location ) : ''; ?></p>
		<h3><a href="<?php echo esc_url( get_permalink( $event ) ); ?>"><?php echo esc_html( get_the_title( $event ) ); ?></a></h3>
		<p class="teaser"><?php echo esc_html( pamoja_teaser( $event, 240 ) ); ?></p>
		<?php if ( $credits ) : ?><p class="credits"><?php echo esc_html( $credits ); ?></p><?php endif; ?>
		<?php get_template_part( 'template-parts/keep-posted', null, array( 'event' => $event ) ); ?>
	</div>
</article>
