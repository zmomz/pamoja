<?php
/**
 * The inquiry form (brief §5.6): relationship-first, not a booking form.
 * Submissions are stored as "inquiry" posts (Pamoja → Inquiries) and
 * emailed to the contact inbox. No third-party form service.
 *
 * A second, tiny form — "Tell me when it's announced" on an upcoming event —
 * lands in the same inbox as a "keep me posted" inquiry.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The contact inbox: the theme's Site Settings value, else the admin email.
 */
function pamoja_contact_email(): string {
	$settings = (array) get_option( 'pamoja_settings', array() );
	$email    = isset( $settings['contact_email'] ) ? sanitize_email( $settings['contact_email'] ) : '';
	if ( ! $email || ! is_email( $email ) ) {
		$email = (string) get_option( 'admin_email' );
	}
	return (string) apply_filters( 'pamoja_contact_email', $email );
}

/**
 * Who is writing. Mirrors the three groups on the home page.
 *
 * @return array<string, string>
 */
function pamoja_inquiry_roles(): array {
	return array(
		'neighbour' => __( 'A neighbour', 'pamoja' ),
		'connector' => __( 'A connector or partner', 'pamoja' ),
		'organizer' => __( 'An organizer', 'pamoja' ),
	);
}

/**
 * The form's field definitions, shared by the theme's form and the handler.
 *
 * @return array<string, array{label:string, required:bool, type:string, hint?:string, rows?:int, autocomplete?:string, options?:array}>
 */
function pamoja_inquiry_fields(): array {
	return array(
		'role'         => array(
			'label'    => __( 'I’m writing as', 'pamoja' ),
			'required' => false,
			'type'     => 'choice',
			'options'  => pamoja_inquiry_roles(),
		),
		'name'         => array(
			'label'        => __( 'Your name', 'pamoja' ),
			'required'     => true,
			'type'         => 'text',
			'autocomplete' => 'name',
		),
		'organization' => array(
			'label'        => __( 'Your organization (if any)', 'pamoja' ),
			'required'     => false,
			'type'         => 'text',
			'autocomplete' => 'organization',
			'hint'         => __( 'A university, a municipal department, a non-profit, a team — or just yourself.', 'pamoja' ),
		),
		'email'        => array(
			'label'        => __( 'Email', 'pamoja' ),
			'required'     => true,
			'type'         => 'email',
			'autocomplete' => 'email',
		),
		'build'        => array(
			'label'    => __( 'What are you imagining?', 'pamoja' ),
			'required' => true,
			'type'     => 'textarea',
			'rows'     => 4,
		),
		'bringing'     => array(
			'label'    => __( 'What do you bring?', 'pamoja' ),
			'required' => false,
			'type'     => 'textarea',
			'rows'     => 3,
			'hint'     => __( 'Space, knowledge, people, relationships, time, money — the Dish With One Spoon is shared by everything placed in it, not only budgets.', 'pamoja' ),
		),
		'timeline'     => array(
			'label'    => __( 'What timeline are you working with?', 'pamoja' ),
			'required' => false,
			'type'     => 'text',
			'hint'     => __( '"This fall," "next year," "no fixed date" — all honest answers.', 'pamoja' ),
		),
	);
}

/**
 * Where the forms post to.
 */
function pamoja_inquiry_action_url(): string {
	return admin_url( 'admin-post.php' );
}

/**
 * Where to send a visitor back after a failed submission: the page the
 * form was on, if it is one of ours.
 */
function pamoja_inquiry_back_url( string $fallback ): string {
	$ref = isset( $_POST['pamoja_back'] ) ? esc_url_raw( wp_unslash( $_POST['pamoja_back'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( $ref && str_starts_with( $ref, home_url( '/' ) ) ) {
		return $ref;
	}
	return $fallback;
}

/**
 * Shared bot and rate checks. Returns '' when the submission may proceed,
 * else the redirect to send the visitor to.
 */
function pamoja_inquiry_gate( string $back ): string {
	// Honeypot: real people leave it empty. Bots get a silent "thank you".
	if ( ! empty( $_POST['bot-field'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return pamoja_inquiry_thank_you_url();
	}
	// Forms filled in under three seconds are not people either.
	$started = isset( $_POST['pamoja_t'] ) ? (int) $_POST['pamoja_t'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( ! $started || ( time() - $started ) < 3 ) {
		return add_query_arg( 'inquiry', 'error', $back );
	}
	// One submission per address per minute.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'pamoja_inquiry_' . md5( $ip );
	if ( $ip && get_transient( $key ) ) {
		return add_query_arg( 'inquiry', 'error', $back );
	}
	return '';
}

function pamoja_inquiry_mark_sent() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( $ip ) {
		set_transient( 'pamoja_inquiry_' . md5( $ip ), 1, MINUTE_IN_SECONDS );
	}
}

/**
 * Store an inquiry and email the inbox. Returns the post id (0 on failure).
 *
 * @param array<string, string> $data  Sanitized field values (label keys).
 * @param array<string, string> $lines Label => value pairs for the email.
 */
function pamoja_inquiry_store( string $kind, string $title, array $data, array $lines, string $subject, string $reply_to = '' ): int {
	$id = wp_insert_post(
		array(
			'post_type'    => 'inquiry',
			'post_status'  => 'publish',
			'post_title'   => wp_strip_all_tags( $title ),
			'post_content' => '',
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		$id = 0;
	} else {
		update_post_meta( $id, '_pamoja_inquiry_kind', $kind );
		foreach ( $data as $key => $value ) {
			if ( '' !== $value ) {
				update_post_meta( $id, '_pamoja_inquiry_' . $key, $value );
			}
		}
	}
	$body = array();
	foreach ( $lines as $label => $value ) {
		if ( '' !== $value ) {
			$body[] = $label . "\n" . $value . "\n";
		}
	}
	if ( $id ) {
		$body[] = __( 'Saved in WordPress:', 'pamoja' ) . ' ' . admin_url( 'post.php?post=' . $id . '&action=edit' );
	}
	$headers = $reply_to ? array( 'Reply-To: ' . $reply_to ) : array();
	wp_mail( pamoja_contact_email(), $subject, implode( "\n", $body ), $headers );
	return (int) $id;
}

function pamoja_handle_inquiry() {
	$fields = pamoja_inquiry_fields();
	$back   = pamoja_inquiry_back_url( (string) apply_filters( 'pamoja_conversation_url', home_url( '/engage/#conversation' ) ) );

	$gate = pamoja_inquiry_gate( $back );
	if ( $gate ) {
		wp_safe_redirect( $gate );
		exit;
	}

	$data   = array();
	$errors = array();
	foreach ( $fields as $key => $field ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput,WordPress.Security.NonceVerification.Missing -- sanitized just below.
		if ( 'email' === $field['type'] ) {
			$value = sanitize_email( $raw );
			if ( $value && ! is_email( $value ) ) {
				$value = '';
			}
		} elseif ( 'textarea' === $field['type'] ) {
			$value = sanitize_textarea_field( $raw );
		} elseif ( 'choice' === $field['type'] ) {
			$value = sanitize_key( $raw );
			$value = isset( $field['options'][ $value ] ) ? $field['options'][ $value ] : '';
		} else {
			$value = sanitize_text_field( $raw );
		}
		$value = trim( (string) $value );
		if ( $field['required'] && '' === $value ) {
			$errors[] = $key;
		}
		$data[ $key ] = mb_substr( $value, 0, 5000 );
	}

	if ( $errors ) {
		wp_safe_redirect( add_query_arg( array( 'inquiry' => 'error', 'missing' => implode( ',', $errors ) ), $back ) );
		exit;
	}

	$title = $data['name'] . ( $data['organization'] ? ' — ' . $data['organization'] : '' );
	$lines = array();
	foreach ( $fields as $key => $field ) {
		$lines[ $field['label'] ] = $data[ $key ];
	}
	pamoja_inquiry_store(
		'conversation',
		$title,
		$data,
		$lines,
		sprintf( __( '[Pamoja] New inquiry from %s', 'pamoja' ), $title ),
		$data['name'] . ' <' . $data['email'] . '>'
	);
	pamoja_inquiry_mark_sent();

	wp_safe_redirect( pamoja_inquiry_thank_you_url() );
	exit;
}
add_action( 'admin_post_nopriv_pamoja_inquiry', 'pamoja_handle_inquiry' );
add_action( 'admin_post_pamoja_inquiry', 'pamoja_handle_inquiry' );

/**
 * "Tell me when it's announced": an email and the event it is about.
 * Responds with JSON when asked to (the theme's script), else redirects.
 */
function pamoja_handle_keep_posted() {
	$event_id = isset( $_POST['event'] ) ? absint( $_POST['event'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$event    = $event_id ? get_post( $event_id ) : null;
	$back     = pamoja_inquiry_back_url( $event && 'event' === $event->post_type ? (string) get_permalink( $event ) : (string) ( get_post_type_archive_link( 'event' ) ?: home_url( '/events/' ) ) );
	$wants    = isset( $_SERVER['HTTP_ACCEPT'] ) && str_contains( (string) $_SERVER['HTTP_ACCEPT'], 'application/json' );

	$fail = function ( string $message ) use ( $back, $wants ) {
		if ( $wants ) {
			wp_send_json( array( 'ok' => false, 'message' => $message ), 400 );
		}
		wp_safe_redirect( add_query_arg( 'keep', 'error', $back ) );
		exit;
	};

	$gate = pamoja_inquiry_gate( $back );
	if ( $gate ) {
		$fail( __( 'That didn’t go through. Please try again in a moment.', 'pamoja' ) );
	}
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	if ( ! $email || ! is_email( $email ) ) {
		$fail( __( 'Please enter an email address we can write to.', 'pamoja' ) );
	}
	if ( ! $event || 'event' !== $event->post_type || 'publish' !== $event->post_status ) {
		$fail( __( 'That event is no longer listed.', 'pamoja' ) );
	}

	$event_title = get_the_title( $event );
	pamoja_inquiry_store(
		'keep-posted',
		sprintf( __( 'Keep me posted: %1$s — %2$s', 'pamoja' ), $event_title, $email ),
		array( 'email' => $email, 'event' => $event_title ),
		array(
			__( 'Email', 'pamoja' )           => $email,
			__( 'About the event', 'pamoja' ) => $event_title . "\n" . get_permalink( $event ),
		),
		sprintf( __( '[Pamoja] Keep me posted: %s', 'pamoja' ), $event_title ),
		$email
	);
	pamoja_inquiry_mark_sent();

	if ( $wants ) {
		wp_send_json( array( 'ok' => true, 'message' => __( 'Noted. We’ll write when it’s announced.', 'pamoja' ) ) );
	}
	wp_safe_redirect( add_query_arg( 'keep', 'sent', $back ) );
	exit;
}
add_action( 'admin_post_nopriv_pamoja_keep_posted', 'pamoja_handle_keep_posted' );
add_action( 'admin_post_pamoja_keep_posted', 'pamoja_handle_keep_posted' );

/**
 * The thank-you page: the one using the theme's template, else the slug,
 * else home.
 */
function pamoja_inquiry_thank_you_url(): string {
	$url = (string) apply_filters( 'pamoja_thank_you_url', '' );
	if ( $url ) {
		return $url;
	}
	$page = get_page_by_path( 'thank-you' );
	return $page ? (string) get_permalink( $page ) : home_url( '/' );
}
