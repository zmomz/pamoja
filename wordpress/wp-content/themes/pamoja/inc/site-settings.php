<?php
/**
 * Site settings (Pamoja → Site settings): contact email, social links, the
 * land acknowledgment, share image, analytics. Stored in one option.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PAMOJA_SETTINGS_OPTION = 'pamoja_settings';

function pamoja_settings_schema(): array {
	return array(
		'contact_email'     => array(
			'label'   => __( 'Contact email', 'pamoja' ),
			'type'    => 'email',
			'default' => '',
			'help'    => __( 'Shown in the contact section and footer, and where inquiry-form messages are sent. Until it is set, the site administrator email is used.', 'pamoja' ),
		),
		'site_description'  => array(
			'label'   => __( 'Site description (search engines and sharing)', 'pamoja' ),
			'type'    => 'textarea',
			'default' => 'Pamoja Cultural Collective — treaty-committed neighbours coming together in Hamilton to create the conditions for social cohesion and collective prosperity. A collective built through relationships.',
		),
		'footer_tagline'    => array(
			'label'   => __( 'Footer tagline', 'pamoja' ),
			'type'    => 'text',
			'default' => 'Treaty-committed neighbours coming together in Hamilton.',
		),
		'footer_line'       => array(
			'label'   => __( 'Footer bottom line', 'pamoja' ),
			'type'    => 'text',
			'default' => 'Pamoja means together. Built on ethical common ground in Hamilton.',
		),
		'social_links'      => array(
			'label'   => __( 'Social links', 'pamoja' ),
			'type'    => 'textarea',
			'default' => '',
			'help'    => __( 'One per line, as "Label | https://…" — for example "Instagram | https://instagram.com/pamoja". Shown in the contact section and footer.', 'pamoja' ),
		),
		'land_ack'          => array(
			'label'   => __( 'Land acknowledgment (footer, sitewide)', 'pamoja' ),
			'type'    => 'html',
			'default' => '',
			'help'    => __( 'Appears in the footer of every page once the confirmed wording is entered. Leave empty until then — do not improvise it. A paragraph starting with "[" is treated as a draft note and not shown.', 'pamoja' ),
		),
		'og_image'          => array(
			'label'   => __( 'Default share image', 'pamoja' ),
			'type'    => 'image',
			'default' => '',
			'help'    => __( 'Used when a page has no consented featured image of its own. Falls back to the brand image.', 'pamoja' ),
		),
		'plausible_domain'  => array(
			'label'   => __( 'Plausible analytics domain (optional)', 'pamoja' ),
			'type'    => 'text',
			'default' => '',
			'help'    => __( 'Privacy-respecting analytics, no cookies. Leave empty for no analytics at all. No Google Analytics is ever loaded.', 'pamoja' ),
		),
	);
}

function pamoja_setting( string $key ): string {
	static $saved = null;
	if ( null === $saved ) {
		$saved = (array) get_option( PAMOJA_SETTINGS_OPTION, array() );
	}
	$schema = pamoja_settings_schema();
	if ( ! isset( $schema[ $key ] ) ) {
		return '';
	}
	$value = $saved[ $key ] ?? '';
	if ( '' === trim( (string) $value ) ) {
		$value = $schema[ $key ]['default'];
	}
	return (string) $value;
}

/**
 * Contact email for display: the setting, else the plugin's fallback.
 */
function pamoja_contact_email_safe(): string {
	if ( function_exists( 'pamoja_contact_email' ) ) {
		return pamoja_contact_email();
	}
	$email = pamoja_setting( 'contact_email' );
	return is_email( $email ) ? $email : (string) get_option( 'admin_email' );
}

/**
 * @return array<int, array{label:string, url:string}>
 */
function pamoja_social_links(): array {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', pamoja_setting( 'social_links' ) ) as $line ) {
		if ( ! str_contains( $line, '|' ) ) {
			continue;
		}
		list( $label, $url ) = array_map( 'trim', explode( '|', $line, 2 ) );
		$url                 = esc_url( $url );
		if ( $label && $url ) {
			$out[] = compact( 'label', 'url' );
		}
	}
	return $out;
}

/**
 * Land acknowledgment HTML, or '' while it is empty or still a draft note.
 */
function pamoja_land_acknowledgment(): string {
	$html = trim( pamoja_setting( 'land_ack' ) );
	if ( '' === $html || str_starts_with( trim( wp_strip_all_tags( $html ) ), '[' ) ) {
		return '';
	}
	return wp_kses_post( wpautop( $html ) );
}

function pamoja_settings_register() {
	register_setting(
		'pamoja_settings',
		PAMOJA_SETTINGS_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'pamoja_settings_sanitize',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'pamoja_settings_register' );

function pamoja_settings_sanitize( $input ): array {
	$clean = array();
	foreach ( pamoja_settings_schema() as $key => $field ) {
		$raw = isset( $input[ $key ] ) ? (string) $input[ $key ] : '';
		switch ( $field['type'] ) {
			case 'email':
				$raw = sanitize_email( $raw );
				break;
			case 'html':
				$raw = wp_kses_post( $raw );
				break;
			case 'textarea':
				$raw = sanitize_textarea_field( $raw );
				break;
			case 'image':
				$raw = (string) absint( $raw );
				$raw = '0' === $raw ? '' : $raw;
				break;
			default:
				$raw = sanitize_text_field( $raw );
		}
		if ( '' !== trim( $raw ) ) {
			$clean[ $key ] = $raw;
		}
	}
	return $clean;
}

function pamoja_settings_menu() {
	$parent = function_exists( 'pamoja_register_post_types' ) ? 'pamoja' : 'themes.php';
	add_submenu_page(
		$parent,
		__( 'Site settings', 'pamoja' ),
		__( 'Site settings', 'pamoja' ),
		'manage_options',
		'pamoja-settings',
		'pamoja_settings_render_page',
		2
	);
}
add_action( 'admin_menu', 'pamoja_settings_menu', 16 );

function pamoja_settings_render_page() {
	$saved = (array) get_option( PAMOJA_SETTINGS_OPTION, array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Site settings', 'pamoja' ); ?></h1>
		<?php if ( isset( $_GET['settings-updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Saved.', 'pamoja' ); ?></p></div>
		<?php endif; ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'pamoja_settings' ); ?>
			<div class="pamoja-settings-section" style="margin-top:20px">
				<table class="form-table" role="presentation">
					<?php foreach ( pamoja_settings_schema() as $key => $field ) : ?>
						<?php
						$name  = PAMOJA_SETTINGS_OPTION . '[' . $key . ']';
						$id    = 'pamoja-settings-' . $key;
						$value = $saved[ $key ] ?? '';
						?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
							<td>
								<?php if ( 'html' === $field['type'] ) : ?>
									<?php
									wp_editor(
										$value,
										$id,
										array(
											'textarea_name' => $name,
											'textarea_rows' => 6,
											'media_buttons' => false,
											'quicktags'     => true,
											'tinymce'       => array(
												'toolbar1' => 'bold,italic,link,unlink,removeformat,undo,redo',
												'toolbar2' => '',
											),
										)
									);
									?>
								<?php elseif ( 'textarea' === $field['type'] ) : ?>
									<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" rows="3" class="large-text" placeholder="<?php echo esc_attr( $field['default'] ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
								<?php elseif ( 'image' === $field['type'] ) : ?>
									<?php if ( function_exists( 'pamoja_render_field' ) ) : ?>
										<?php pamoja_render_field( array( 'type' => 'image' ), $value, $name, $id ); ?>
									<?php else : ?>
										<input type="number" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="small-text" />
										<p class="description"><?php esc_html_e( 'Media library attachment ID.', 'pamoja' ); ?></p>
									<?php endif; ?>
								<?php else : ?>
									<input type="<?php echo 'email' === $field['type'] ? 'email' : 'text'; ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" placeholder="<?php echo esc_attr( $field['default'] ); ?>" />
								<?php endif; ?>
								<?php if ( ! empty( $field['help'] ) ) : ?>
									<p class="description"><?php echo esc_html( $field['help'] ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button(); ?>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Warn until the contact inbox is set.
 */
function pamoja_settings_notice() {
	if ( ! current_user_can( 'manage_options' ) || is_email( pamoja_setting( 'contact_email' ) ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && str_ends_with( (string) $screen->id, 'pamoja-settings' ) ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( 'Pamoja: no contact email is set yet, so the site shows the administrator address and inquiries go there.', 'pamoja' ),
		esc_url( admin_url( 'admin.php?page=pamoja-settings' ) ),
		esc_html__( 'Set the contact email →', 'pamoja' )
	);
}
add_action( 'admin_notices', 'pamoja_settings_notice' );
