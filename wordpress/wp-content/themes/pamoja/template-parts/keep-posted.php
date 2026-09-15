<?php
/**
 * "Tell me when it's announced": one email field for an upcoming event.
 * Posts to the plugin's handler; the script upgrades it to stay in place.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$event = $args['event'] ?? null;
if ( ! $event instanceof WP_Post || ! function_exists( 'pamoja_inquiry_action_url' ) ) {
	return;
}
$uid = 'keep-' . $event->ID . '-' . wp_unique_id();

// Without JavaScript the handler sends the visitor back here with the result
// and the event it was about, so the right form answers for itself wherever
// it renders — the home page and the events list, not only the event page.
$pamoja_keep = '';
// phpcs:disable WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['keep'] ) && (int) ( $_GET['keep_event'] ?? 0 ) === (int) $event->ID ) {
	$pamoja_keep = sanitize_key( wp_unslash( $_GET['keep'] ) );
}
// phpcs:enable WordPress.Security.NonceVerification.Recommended

if ( 'sent' === $pamoja_keep ) :
	?>
	<p class="keep-done" role="status"><?php pamoja_home_text( 'coming', 'keep_done' ); ?></p>
	<?php
	return;
endif;

if ( 'error' === $pamoja_keep ) :
	?>
	<p class="keep-error" role="alert"><?php esc_html_e( 'That didn’t go through. Please check the address and try again.', 'pamoja' ); ?></p>
	<?php
endif;
?>
<form class="keep" method="post" action="<?php echo esc_url( pamoja_inquiry_action_url() ); ?>" data-done="<?php echo esc_attr( pamoja_home( 'coming', 'keep_done' ) ); ?>">
	<input type="hidden" name="action" value="pamoja_keep_posted">
	<input type="hidden" name="event" value="<?php echo (int) $event->ID; ?>">
	<input type="hidden" name="pamoja_t" value="<?php echo (int) time(); ?>">
	<input type="hidden" name="pamoja_back" value="<?php echo esc_url( pamoja_current_url() ); ?>">
	<p class="visually-hidden" aria-hidden="true"><label><?php esc_html_e( 'Leave this field empty if you are a person', 'pamoja' ); ?> <input name="bot-field" tabindex="-1" autocomplete="off"></label></p>
	<label class="visually-hidden" for="<?php echo esc_attr( $uid ); ?>"><?php pamoja_home_text( 'coming', 'keep_label' ); ?></label>
	<input id="<?php echo esc_attr( $uid ); ?>" name="email" type="email" required autocomplete="email" placeholder="<?php echo esc_attr( pamoja_home( 'coming', 'keep_label' ) ); ?>">
	<button type="submit"><?php pamoja_home_text( 'coming', 'keep_button' ); ?></button>
	<p class="keep-msg" role="status" aria-live="polite"></p>
</form>
