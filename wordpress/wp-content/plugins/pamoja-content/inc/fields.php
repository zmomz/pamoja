<?php
/**
 * A small field framework for meta boxes and settings pages: render one
 * field from a definition array, and sanitize one submitted value.
 *
 * Field definition keys:
 *   key, label, type, description, options (select), post_type (posts /
 *   post_select), default, placeholder, show_when ("field=value").
 * Types: text, url, email, date, number, textarea, lines, select, checkbox,
 *   wysiwyg, posts, post_select, gallery, image.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render one field's control (no wrapper). $name is the form input name.
 */
function pamoja_render_field( array $field, $value, string $name, string $id = '' ) {
	$id   = $id ?: sanitize_key( str_replace( array( '[', ']' ), array( '-', '' ), $name ) );
	$type = $field['type'] ?? 'text';

	switch ( $type ) {
		case 'textarea':
			printf(
				'<textarea id="%s" name="%s" rows="%d" class="large-text" placeholder="%s">%s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				(int) ( $field['rows'] ?? 4 ),
				esc_attr( $field['placeholder'] ?? '' ),
				esc_textarea( (string) $value )
			);
			break;

		case 'lines':
			$text = is_array( $value ) ? implode( "\n", $value ) : (string) $value;
			printf(
				'<textarea id="%s" name="%s" rows="%d" class="large-text" placeholder="%s">%s</textarea><p class="description">%s</p>',
				esc_attr( $id ),
				esc_attr( $name ),
				(int) ( $field['rows'] ?? 5 ),
				esc_attr( $field['placeholder'] ?? '' ),
				esc_textarea( $text ),
				esc_html__( 'One item per line.', 'pamoja' )
			);
			break;

		case 'select':
			printf( '<select id="%s" name="%s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( (array) ( $field['options'] ?? array() ) as $opt_value => $label ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $opt_value ),
					selected( (string) $value, (string) $opt_value, false ),
					esc_html( $label )
				);
			}
			echo '</select>';
			break;

		case 'checkbox':
			printf(
				'<label><input type="checkbox" id="%s" name="%s" value="1"%s /> %s</label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( (bool) $value, true, false ),
				esc_html( $field['checkbox_label'] ?? $field['label'] ?? '' )
			);
			break;

		case 'wysiwyg':
			wp_editor(
				(string) $value,
				$id,
				array(
					'textarea_name' => $name,
					'textarea_rows' => (int) ( $field['rows'] ?? 8 ),
					'media_buttons' => false,
					'teeny'         => false,
					'quicktags'     => true,
					'tinymce'       => array(
						'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink,removeformat,undo,redo',
						'toolbar2' => '',
						'block_formats' => 'Paragraph=p;Heading 3=h3;Heading 4=h4;Quote=blockquote',
					),
				)
			);
			break;

		case 'posts':
			$posts = get_posts(
				array(
					'post_type'      => $field['post_type'],
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
					'post_status'    => 'publish',
				)
			);
			$value = array_map( 'intval', (array) $value );
			if ( ! $posts ) {
				printf( '<p class="description">%s</p>', esc_html( $field['empty'] ?? __( 'Nothing to choose from yet.', 'pamoja' ) ) );
			}
			echo '<ul class="pamoja-checklist">';
			foreach ( $posts as $p ) {
				printf(
					'<li><label><input type="checkbox" name="%s[]" value="%d"%s /> %s</label></li>',
					esc_attr( $name ),
					$p->ID,
					checked( in_array( $p->ID, $value, true ), true, false ),
					esc_html( get_the_title( $p ) )
				);
			}
			echo '</ul>';
			// Keep the key present so an empty selection still saves.
			printf( '<input type="hidden" name="%s[]" value="" />', esc_attr( $name ) );
			break;

		case 'post_select':
			$posts = get_posts(
				array(
					'post_type'      => $field['post_type'],
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
					'post_status'    => 'publish',
				)
			);
			printf( '<select id="%s" name="%s"><option value="">%s</option>', esc_attr( $id ), esc_attr( $name ), esc_html__( '— none —', 'pamoja' ) );
			foreach ( $posts as $p ) {
				printf(
					'<option value="%d"%s>%s</option>',
					$p->ID,
					selected( (int) $value, $p->ID, false ),
					esc_html( get_the_title( $p ) )
				);
			}
			echo '</select>';
			break;

		case 'gallery':
			pamoja_render_gallery_field( $name, array_map( 'intval', array_filter( (array) $value ) ) );
			break;

		case 'image':
			$image_id = (int) $value;
			echo '<div class="pamoja-image-field">';
			printf( '<input type="hidden" name="%s" value="%d" class="pamoja-image-id" />', esc_attr( $name ), $image_id );
			echo '<div class="pamoja-image-preview">';
			if ( $image_id ) {
				echo wp_get_attachment_image( $image_id, 'medium' );
			}
			echo '</div>';
			printf(
				'<button type="button" class="button pamoja-image-pick">%s</button> <button type="button" class="button-link pamoja-image-clear"%s>%s</button>',
				esc_html__( 'Choose image', 'pamoja' ),
				$image_id ? '' : ' hidden',
				esc_html__( 'Remove', 'pamoja' )
			);
			echo '</div>';
			break;

		case 'date':
		case 'number':
		case 'url':
		case 'email':
		case 'text':
		default:
			$input_type = in_array( $type, array( 'date', 'number', 'url', 'email' ), true ) ? $type : 'text';
			printf(
				'<input type="%s" id="%s" name="%s" value="%s" class="%s" placeholder="%s"%s />',
				esc_attr( $input_type ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( (string) $value ),
				'number' === $input_type ? 'small-text' : 'regular-text',
				esc_attr( $field['placeholder'] ?? '' ),
				'number' === $input_type ? ' min="0" step="1"' : ''
			);
			break;
	}

	if ( ! empty( $field['description'] ) && 'lines' !== $type ) {
		printf( '<p class="description">%s</p>', wp_kses_post( $field['description'] ) );
	}
}

/**
 * The gallery picker: a sortable list of attachment thumbnails, each carrying
 * its consent/alt status, and an "Add photos" button that opens the media
 * library. Consent itself is edited on the attachment (see consent.php).
 */
function pamoja_render_gallery_field( string $name, array $ids ) {
	echo '<div class="pamoja-gallery">';
	printf( '<input type="hidden" class="pamoja-gallery-ids" name="%s" value="%s" />', esc_attr( $name ), esc_attr( implode( ',', $ids ) ) );
	echo '<ul class="pamoja-gallery-list">';
	foreach ( $ids as $id ) {
		pamoja_render_gallery_item( $id );
	}
	echo '</ul>';
	printf(
		'<p><button type="button" class="button pamoja-gallery-add">%s</button></p>',
		esc_html__( 'Add photos', 'pamoja' )
	);
	printf(
		'<p class="description">%s</p>',
		esc_html__( 'Drag to reorder. A photo only appears on the site once it has alt text and consent confirmed on file for every identifiable person — open the photo to tick "Consent confirmed". Never publish children\'s faces without explicit parental consent.', 'pamoja' )
	);
	echo '</div>';
}

function pamoja_render_gallery_item( int $id ) {
	$status = pamoja_image_status( $id );
	$thumb  = wp_get_attachment_image( $id, 'thumbnail' );
	if ( ! $thumb ) {
		return;
	}
	printf(
		'<li data-id="%1$d" class="%2$s">%3$s<span class="pamoja-gallery-status">%4$s</span><span class="pamoja-gallery-actions"><a href="%5$s" target="_blank" rel="noopener">%6$s</a> <button type="button" class="button-link-delete pamoja-gallery-remove" aria-label="%7$s">&times;</button></span></li>',
		$id,
		$status['ok'] ? 'is-ok' : 'is-blocked',
		$thumb,
		esc_html( $status['label'] ),
		esc_url( get_edit_post_link( $id ) ),
		esc_html__( 'Edit', 'pamoja' ),
		esc_attr__( 'Remove from album', 'pamoja' )
	);
}

/**
 * Sanitize a submitted value according to its field definition.
 */
function pamoja_sanitize_field( array $field, $raw ) {
	$type = $field['type'] ?? 'text';
	switch ( $type ) {
		case 'textarea':
			return sanitize_textarea_field( (string) $raw );
		case 'lines':
			$lines = preg_split( '/\r\n|\r|\n/', (string) $raw );
			$lines = array_map( 'sanitize_text_field', $lines );
			return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
		case 'select':
			$raw = (string) $raw;
			return array_key_exists( $raw, (array) ( $field['options'] ?? array() ) ) ? $raw : (string) ( $field['default'] ?? '' );
		case 'checkbox':
			return $raw ? '1' : '';
		case 'wysiwyg':
			return wp_kses_post( (string) $raw );
		case 'posts':
		case 'gallery':
			if ( is_string( $raw ) ) {
				$raw = explode( ',', $raw );
			}
			return array_values( array_filter( array_map( 'absint', (array) $raw ) ) );
		case 'post_select':
		case 'image':
		case 'number':
			return absint( $raw );
		case 'url':
			return esc_url_raw( (string) $raw );
		case 'email':
			return sanitize_email( (string) $raw );
		case 'date':
			$raw = sanitize_text_field( (string) $raw );
			return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ? $raw : '';
		default:
			return sanitize_text_field( (string) $raw );
	}
}
