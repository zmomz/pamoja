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
