<?php
/**
 * The inquiry form (brief §5.6). Posts to the plugin's handler, which stores
 * the message under Pamoja → Inquiries and emails the contact inbox.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'pamoja_inquiry_fields' ) ) {
	printf( '<p class="inquiry-note">%s <a href="mailto:%s">%s</a></p>', esc_html__( 'Write to us at', 'pamoja' ), esc_attr( pamoja_contact_email_safe() ), esc_html( pamoja_contact_email_safe() ) );
	return;
}

$fields  = pamoja_inquiry_fields();
$error   = isset( $_GET['inquiry'] ) && 'error' === $_GET['inquiry']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$missing = isset( $_GET['missing'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['missing'] ) ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<div class="inquiry">
	<div class="inquiry-note"><?php pamoja_home_html( 'contact', 'form_note' ); ?></div>

	<?php if ( $error ) : ?>
		<p class="inquiry-error" role="alert"><?php pamoja_home_text( 'contact', 'error' ); ?></p>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( pamoja_inquiry_action_url() ); ?>" class="inquiry-form" id="inquiry-form">
		<input type="hidden" name="action" value="pamoja_inquiry">
		<input type="hidden" name="pamoja_t" value="<?php echo (int) time(); ?>">
		<input type="hidden" name="pamoja_back" value="<?php echo esc_url( pamoja_current_url() . '#conversation' ); ?>">
		<p class="visually-hidden" aria-hidden="true">
			<label><?php esc_html_e( 'Leave this field empty if you are a person', 'pamoja' ); ?>
				<input name="bot-field" tabindex="-1" autocomplete="off"></label>
		</p>

		<?php foreach ( $fields as $key => $field ) : ?>
			<?php
			$id      = 'inquiry-' . $key;
			$hint_id = $id . '-hint';
			$invalid = in_array( $key, $missing, true );
			?>
			<?php if ( 'choice' === $field['type'] ) : ?>
				<fieldset class="field field--choice">
					<legend><?php echo esc_html( $field['label'] ); ?></legend>
					<div class="pills">
						<?php $first = true; foreach ( $field['options'] as $value => $label ) : ?>
							<label class="pill"><input type="radio" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php checked( $first ); ?>><span><?php echo esc_html( $label ); ?></span></label>
							<?php $first = false; ?>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php else : ?>
				<div class="field<?php echo $invalid ? ' is-invalid' : ''; ?>">
					<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?><?php if ( empty( $field['required'] ) ) : ?> <small><?php esc_html_e( '(optional)', 'pamoja' ); ?></small><?php endif; ?></label>
					<?php if ( 'textarea' === $field['type'] ) : ?>
						<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="<?php echo (int) ( $field['rows'] ?? 3 ); ?>"<?php echo $field['required'] ? ' required' : ''; ?><?php echo ! empty( $field['hint'] ) ? ' aria-describedby="' . esc_attr( $hint_id ) . '"' : ''; ?>></textarea>
					<?php else : ?>
						<input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $field['type'] ); ?>"<?php echo $field['required'] ? ' required' : ''; ?><?php echo ! empty( $field['autocomplete'] ) ? ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"' : ''; ?><?php echo ! empty( $field['hint'] ) ? ' aria-describedby="' . esc_attr( $hint_id ) . '"' : ''; ?>>
					<?php endif; ?>
					<?php if ( ! empty( $field['hint'] ) ) : ?>
						<p class="hint" id="<?php echo esc_attr( $hint_id ); ?>"><?php echo esc_html( $field['hint'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>

		<button class="btn submit" type="submit"><?php pamoja_home_text( 'contact', 'submit' ); ?></button>
	</form>
</div>
