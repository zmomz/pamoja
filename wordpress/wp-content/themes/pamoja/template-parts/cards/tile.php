<?php
/**
 * A photo tile for an event or album. Expects $args['post'].
 * Without a consented photo it is a plain card, never an empty box.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$item = $args['post'] ?? null;
if ( ! $item instanceof WP_Post ) {
	return;
}
$is_event = 'event' === $item->post_type;
$cover    = pamoja_cover_image( $item->ID, 'pamoja-tile' );
$video    = (bool) pamoja_meta( $item->ID, 'video_url' );
$when     = $is_event ? pamoja_event_when( $item->ID ) : pamoja_album_when( $item->ID );
$where    = $is_event ? (string) pamoja_meta( $item->ID, 'location' ) : '';
$status   = $is_event ? (string) pamoja_meta( $item->ID, 'status', 'past' ) : '';
$sub      = $where ? $where . ' · ' . $when : $when;
?>
<a class="tile<?php echo $cover ? '' : ' tile--plain'; ?><?php echo $video ? ' tile--video' : ''; ?>" href="<?php echo esc_url( get_permalink( $item ) ); ?>">
	<?php if ( $cover ) : ?>
		<?php echo $cover; // Escaped by wp_get_attachment_image(). ?>
	<?php endif; ?>
	<?php if ( $video ) : ?><span class="play" aria-hidden="true"></span><?php endif; ?>
	<span class="tile-body">
		<?php if ( 'upcoming' === $status ) : ?><span class="tile-badge"><?php echo esc_html( pamoja_event_status_label( $item->ID ) ); ?></span><?php endif; ?>
		<b><?php echo esc_html( get_the_title( $item ) ); ?></b>
		<span class="tile-sub"><?php echo esc_html( $sub ); ?><?php echo $video ? ' · ' . esc_html__( 'Video', 'pamoja' ) : ''; ?></span>
	</span>
</a>
