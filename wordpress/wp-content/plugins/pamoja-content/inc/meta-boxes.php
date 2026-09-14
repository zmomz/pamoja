<?php
/**
 * Meta boxes for events, albums, services, partners and inquiries, all
 * driven by one schema so the fields, their rendering and their saving stay
 * in step. Values are stored as post meta under _pamoja_{key}.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, array{title:string, post_types:string[], context?:string, priority?:string, fields:array<int,array>}>
 */
function pamoja_meta_box_schema(): array {
	return array(
		'event_details' => array(
			'title'      => __( 'Event details', 'pamoja' ),
			'post_types' => array( 'event' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'fields'     => array(
				array(
					'key'         => 'date_display',
					'label'       => __( 'Date or date range (as displayed)', 'pamoja' ),
					'type'        => 'text',
					'placeholder' => __( 'e.g. October 2026, or 2023–2026', 'pamoja' ),
					'description' => __( 'Exactly as it should read on the site.', 'pamoja' ),
				),
				array(
					'key'         => 'start_date',
					'label'       => __( 'Start date', 'pamoja' ),
					'type'        => 'date',
					'description' => __( 'Sorts the archive (most recent first).', 'pamoja' ),
				),
				array(
					'key'   => 'end_date',
					'label' => __( 'End date (optional)', 'pamoja' ),
					'type'  => 'date',
				),
				array(
					'key'         => 'location',
					'label'       => __( 'Location', 'pamoja' ),
					'type'        => 'text',
					'placeholder' => __( 'Park, venue or neighbourhood', 'pamoja' ),
					'description' => __( 'Leave blank for just "Hamilton".', 'pamoja' ),
				),
				array(
					'key'         => 'status',
					'label'       => __( 'Status', 'pamoja' ),
					'type'        => 'select',
					'options'     => array(
						'past'      => __( 'Past', 'pamoja' ),
						'upcoming'  => __( 'Upcoming', 'pamoja' ),
						'ongoing'   => __( 'Ongoing (recurring)', 'pamoja' ),
						'handed-on' => __( 'Handed on', 'pamoja' ),
					),
					'default'     => 'past',
					'description' => __( 'Upcoming events appear as fruit on the tree and under "Coming up". "Ongoing" is for a recurring gathering. "Handed on" means the work now belongs to someone else — the site says so plainly, neither claiming it nor deleting it.', 'pamoja' ),
				),
				array(
					'key'         => 'handed_on_to',
					'label'       => __( 'Handed on to', 'pamoja' ),
					'type'        => 'post_select',
					'post_type'   => 'partner',
					'show_when'   => 'status=handed-on',
					'description' => __( 'The partner who now owns and runs this work.', 'pamoja' ),
				),
				array(
					'key'         => 'partners',
					'label'       => __( 'Partners and funders credited', 'pamoja' ),
					'type'        => 'posts',
					'post_type'   => 'partner',
					'empty'       => __( 'Add partners under Pamoja → Partners & funders first.', 'pamoja' ),
					'description' => __( 'Name everyone who co-created, hosted, or funded. Credit is shared publicly — a commitment, not a courtesy.', 'pamoja' ),
				),
				array(
					'key'         => 'pull_quote',
					'label'       => __( 'Pull quote (optional)', 'pamoja' ),
					'type'        => 'textarea',
					'rows'        => 2,
					'description' => __( 'One line that carries the gathering.', 'pamoja' ),
				),
				array(
					'key'         => 'video_url',
					'label'       => __( 'Video (optional)', 'pamoja' ),
					'type'        => 'url',
					'placeholder' => 'https://www.youtube.com/watch?v=…',
					'description' => __( 'A YouTube or Vimeo link. It plays in place on the event page.', 'pamoja' ),
				),
				array(
					'key'            => 'featured',
					'label'          => __( 'Featured', 'pamoja' ),
					'type'           => 'checkbox',
					'checkbox_label' => __( 'Show under "How we\'ve come together so far" on the home page (the four most recent featured events, by their Order)', 'pamoja' ),
				),
			),
		),
		'event_gallery' => array(
			'title'      => __( 'Photo gallery', 'pamoja' ),
			'post_types' => array( 'event' ),
			'context'    => 'normal',
			'priority'   => 'default',
			'fields'     => array(
				array(
					'key'   => 'gallery',
					'label' => '',
					'type'  => 'gallery',
				),
			),
		),
		'album_details' => array(
			'title'      => __( 'Album', 'pamoja' ),
			'post_types' => array( 'album' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'fields'     => array(
				array(
					'key'   => 'gallery',
					'label' => __( 'Photos', 'pamoja' ),
					'type'  => 'gallery',
				),
				array(
					'key'         => 'video_url',
					'label'       => __( 'Video (optional)', 'pamoja' ),
					'type'        => 'url',
					'placeholder' => 'https://www.youtube.com/watch?v=…',
					'description' => __( 'A YouTube or Vimeo link. It is embedded above the photos.', 'pamoja' ),
				),
				array(
					'key'         => 'event',
					'label'       => __( 'From which event?', 'pamoja' ),
					'type'        => 'post_select',
					'post_type'   => 'event',
					'description' => __( 'Optional. Links the album and the event to each other.', 'pamoja' ),
				),
				array(
					'key'         => 'date_display',
					'label'       => __( 'Date (as displayed)', 'pamoja' ),
					'type'        => 'text',
					'placeholder' => __( 'e.g. September 2025', 'pamoja' ),
					'description' => __( 'Optional. Shown instead of the publish date.', 'pamoja' ),
				),
			),
		),
		'service_fields' => array(
			'title'      => __( 'The eight fixed sections', 'pamoja' ),
			'post_types' => array( 'service' ),
			'context'    => 'normal',
			'priority'   => 'high',
			'fields'     => array(
				array(
					'key'         => 'summary',
					'label'       => __( '1. Summary', 'pamoja' ),
					'type'        => 'textarea',
					'rows'        => 3,
					'description' => __( 'One sentence, used on the card.', 'pamoja' ),
				),
				array(
					'key'   => 'what_it_is',
					'label' => __( '2. What it is', 'pamoja' ),
					'type'  => 'textarea',
					'rows'  => 5,
				),
				array(
					'key'         => 'what_happens',
					'label'       => __( '3. What actually happens', 'pamoja' ),
					'type'        => 'wysiwyg',
					'rows'        => 6,
					'description' => __( 'The shape of the session, honestly.', 'pamoja' ),
				),
				array(
					'key'         => 'what_to_expect',
					'label'       => __( '4. What to expect', 'pamoja' ),
					'type'        => 'wysiwyg',
					'rows'        => 6,
					'description' => __( 'Include the discomfort; it is part of the offer.', 'pamoja' ),
				),
				array(
					'key'         => 'need_from_you',
					'label'       => __( '5. What we need from you', 'pamoja' ),
					'type'        => 'lines',
					'description' => __( 'Space, time, budget, participation.', 'pamoja' ),
				),
				array(
					'key'         => 'protocols',
					'label'       => __( '6. Cultural protocols', 'pamoja' ),
					'type'        => 'wysiwyg',
					'rows'        => 6,
					'description' => __( 'How to engage respectfully with this specific practice. Any paragraph that starts with "[" is treated as a draft note and is not shown on the site.', 'pamoja' ),
				),
				array(
					'key'         => 'what_this_is_not',
					'label'       => __( '7. What this is not', 'pamoja' ),
					'type'        => 'lines',
					'description' => __( 'The short list that prevents wrong inquiries.', 'pamoja' ),
				),
				array(
					'key'         => 'lead_time',
					'label'       => __( '8. Time and lead time', 'pamoja' ),
					'type'        => 'text',
					'description' => __( 'How long, and how far ahead to ask.', 'pamoja' ),
				),
			),
		),
		'partner_details' => array(
			'title'      => __( 'Partner details', 'pamoja' ),
			'post_types' => array( 'partner' ),
			'context'    => 'side',
			'priority'   => 'high',
			'fields'     => array(
				array(
					'key'     => 'partner_type',
					'label'   => __( 'Type', 'pamoja' ),
					'type'    => 'select',
					'options' => array(
						'partner'           => __( 'Partner', 'pamoja' ),
						'ally'              => __( 'Ally', 'pamoja' ),
						'funder'            => __( 'Funder', 'pamoja' ),
						'settlement-sector' => __( 'Settlement sector', 'pamoja' ),
					),
					'default' => 'partner',
				),
				array(
					'key'         => 'url',
					'label'       => __( 'Website (optional)', 'pamoja' ),
					'type'        => 'url',
					'placeholder' => 'https://',
				),
			),
		),
	);
}

/**
 * Meta key for a field.
 */
function pamoja_meta_key( string $key ): string {
	return '_pamoja_' . $key;
}

/**
 * Read one field value with its default.
 */
function pamoja_get_meta( int $post_id, string $key, $default = '' ) {
	$value = get_post_meta( $post_id, pamoja_meta_key( $key ), true );
	return ( '' === $value || null === $value ) ? $default : $value;
}

function pamoja_add_meta_boxes() {
	foreach ( pamoja_meta_box_schema() as $box_id => $box ) {
		foreach ( $box['post_types'] as $post_type ) {
			add_meta_box(
				'pamoja-' . str_replace( '_', '-', $box_id ),
				$box['title'],
				'pamoja_render_meta_box',
				$post_type,
				$box['context'] ?? 'normal',
				$box['priority'] ?? 'default',
				array( 'box_id' => $box_id )
			);
		}
	}

	add_meta_box( 'pamoja-inquiry', __( 'Message', 'pamoja' ), 'pamoja_render_inquiry_box', 'inquiry', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'pamoja_add_meta_boxes' );

function pamoja_render_meta_box( WP_Post $post, array $args ) {
	$box_id = $args['args']['box_id'];
	$box    = pamoja_meta_box_schema()[ $box_id ];
	wp_nonce_field( 'pamoja_save_' . $box_id, 'pamoja_nonce_' . $box_id );

	echo '<div class="pamoja-fields">';
	foreach ( $box['fields'] as $field ) {
		$value = pamoja_get_meta( $post->ID, $field['key'], $field['default'] ?? '' );
		$name  = 'pamoja_' . $field['key'];
		$attrs = '';
		if ( ! empty( $field['show_when'] ) ) {
			list( $dep, $dep_value ) = explode( '=', $field['show_when'], 2 );
			$attrs = sprintf( ' data-show-when="pamoja_%s" data-show-value="%s"', esc_attr( $dep ), esc_attr( $dep_value ) );
		}
		printf( '<div class="pamoja-field pamoja-field--%s"%s>', esc_attr( $field['type'] ), $attrs ); // $attrs escaped above.
		if ( '' !== $field['label'] && 'checkbox' !== $field['type'] ) {
			printf( '<label class="pamoja-field-label" for="%s">%s</label>', esc_attr( $name ), esc_html( $field['label'] ) );
		}
		pamoja_render_field( $field, $value, $name, $name );
		echo '</div>';
	}
	echo '</div>';
}

function pamoja_save_meta_boxes( int $post_id, WP_Post $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( pamoja_meta_box_schema() as $box_id => $box ) {
		if ( ! in_array( $post->post_type, $box['post_types'], true ) ) {
			continue;
		}
		$nonce = 'pamoja_nonce_' . $box_id;
		if ( ! isset( $_POST[ $nonce ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ $nonce ] ) ), 'pamoja_save_' . $box_id ) ) {
			continue;
		}
		foreach ( $box['fields'] as $field ) {
			$name = 'pamoja_' . $field['key'];
			$raw  = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitized below per type.
			$clean = pamoja_sanitize_field( $field, $raw );
			if ( '' === $clean || array() === $clean ) {
				delete_post_meta( $post_id, pamoja_meta_key( $field['key'] ) );
			} else {
				update_post_meta( $post_id, pamoja_meta_key( $field['key'] ), $clean );
			}
		}
	}
}
add_action( 'save_post', 'pamoja_save_meta_boxes', 10, 2 );

/**
 * Inquiries are read-only in the admin.
 */
function pamoja_render_inquiry_box( WP_Post $post ) {
	$rows = array(
		__( 'Kind', 'pamoja' )                 => pamoja_get_meta( $post->ID, 'inquiry_kind' ),
		__( 'Writing as', 'pamoja' )           => pamoja_get_meta( $post->ID, 'inquiry_role' ),
		__( 'About the event', 'pamoja' )      => pamoja_get_meta( $post->ID, 'inquiry_event' ),
		__( 'Name', 'pamoja' )                 => pamoja_get_meta( $post->ID, 'inquiry_name' ),
		__( 'Organization', 'pamoja' )         => pamoja_get_meta( $post->ID, 'inquiry_organization' ),
		__( 'Email', 'pamoja' )                => pamoja_get_meta( $post->ID, 'inquiry_email' ),
		__( 'Hoping to build', 'pamoja' )      => pamoja_get_meta( $post->ID, 'inquiry_build' ),
		__( 'Who else is involved', 'pamoja' ) => pamoja_get_meta( $post->ID, 'inquiry_involved' ),
		__( 'Timeline', 'pamoja' )             => pamoja_get_meta( $post->ID, 'inquiry_timeline' ),
		__( 'Bringing besides money', 'pamoja' ) => pamoja_get_meta( $post->ID, 'inquiry_bringing' ),
		__( 'Received', 'pamoja' )             => get_the_date( 'j F Y, H:i', $post ),
	);
	echo '<table class="widefat striped pamoja-inquiry-table"><tbody>';
	foreach ( $rows as $label => $value ) {
		if ( '' === $value ) {
			continue;
		}
		if ( __( 'Email', 'pamoja' ) === $label ) {
			$value = sprintf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $value ) );
		} else {
			$value = nl2br( esc_html( $value ) );
		}
		printf( '<tr><th scope="row" style="width:12em">%s</th><td>%s</td></tr>', esc_html( $label ), $value ); // $value escaped above.
	}
	echo '</tbody></table>';
}
