<?php
/**
 * The photo-consent rule (brief §9.3).
 *
 * Every image in the media library carries a "Consent confirmed" flag. The
 * theme never outputs a gallery image or featured image unless the flag is
 * set AND the image has alt text — so nothing unconsented can reach the
 * public site, whatever the post's status. The edit screen warns loudly
 * about any selected image that is being held back.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PAMOJA_CONSENT_META = '_pamoja_consent';
const PAMOJA_CONSENT_SOURCE_META = '_pamoja_consent_source';

/**
 * Record consent on an image, with where it came from.
 */
function pamoja_set_consent( int $attachment_id, bool $on, string $source = '' ) {
	update_post_meta( $attachment_id, PAMOJA_CONSENT_META, $on ? '1' : '' );
	if ( $source ) {
		update_post_meta( $attachment_id, PAMOJA_CONSENT_SOURCE_META, $source );
	}
}

/**
 * Where an image's consent was recorded, if noted.
 */
function pamoja_consent_source( int $attachment_id ): string {
	return (string) get_post_meta( $attachment_id, PAMOJA_CONSENT_SOURCE_META, true );
}

/**
 * Consent checkbox on every image in the media library (modal and edit screen).
 */
function pamoja_attachment_consent_field( array $fields, WP_Post $post ) {
	if ( ! wp_attachment_is_image( $post ) ) {
		return $fields;
	}
	$checked = '1' === get_post_meta( $post->ID, PAMOJA_CONSENT_META, true );
	$source  = pamoja_consent_source( $post->ID );
	$fields['pamoja_consent'] = array(
		'label' => __( 'Consent confirmed', 'pamoja' ),
		'input' => 'html',
		'html'  => sprintf(
			'<label for="attachments-%1$d-pamoja_consent"><input type="checkbox" id="attachments-%1$d-pamoja_consent" name="attachments[%1$d][pamoja_consent]" value="1"%2$s /> %3$s</label>',
			$post->ID,
			checked( $checked, true, false ),
			esc_html__( 'Consent is on file for every identifiable person in this photo.', 'pamoja' )
		),
		'helps' => __( 'Required before the photo can appear anywhere on the site. Never publish children\'s faces without explicit parental consent. When in doubt, leave the photo out.', 'pamoja' ) . ( $source ? ' ' . $source : '' ),
	);
	return $fields;
}
add_filter( 'attachment_fields_to_edit', 'pamoja_attachment_consent_field', 10, 2 );

function pamoja_attachment_consent_save( array $post, array $attachment ) {
	pamoja_set_consent( (int) $post['ID'], ! empty( $attachment['pamoja_consent'] ) );
	return $post;
}
add_filter( 'attachment_fields_to_save', 'pamoja_attachment_consent_save', 10, 2 );

/**
 * Status of one image: publishable or not, and why.
 *
 * @return array{ok: bool, consent: bool, alt: bool, label: string}
 */
function pamoja_image_status( int $attachment_id ): array {
	$consent = '1' === get_post_meta( $attachment_id, PAMOJA_CONSENT_META, true );
	$alt     = '' !== trim( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );
	$ok      = $consent && $alt;
	if ( $ok ) {
		$label = __( 'Ready', 'pamoja' );
	} elseif ( ! $consent && ! $alt ) {
		$label = __( 'Needs consent and alt text', 'pamoja' );
	} elseif ( ! $consent ) {
		$label = __( 'Needs consent', 'pamoja' );
	} else {
		$label = __( 'Needs alt text', 'pamoja' );
	}
	return compact( 'ok', 'consent', 'alt', 'label' );
}

/**
 * True when an image may be shown on the public site.
 */
function pamoja_image_is_publishable( int $attachment_id ): bool {
	if ( ! $attachment_id || ! wp_attachment_is_image( $attachment_id ) ) {
		return false;
	}
	return pamoja_image_status( $attachment_id )['ok'];
}

/**
 * All gallery ids saved on a post (events and albums).
 *
 * @return int[]
 */
function pamoja_get_gallery_ids( int $post_id ): array {
	$ids = get_post_meta( $post_id, '_pamoja_gallery', true );
	return array_values( array_filter( array_map( 'intval', (array) $ids ) ) );
}

/**
 * Gallery ids that pass the consent rule — the only ones the theme renders.
 *
 * @return int[]
 */
function pamoja_get_publishable_gallery( int $post_id ): array {
	return array_values( array_filter( pamoja_get_gallery_ids( $post_id ), 'pamoja_image_is_publishable' ) );
}

/**
 * Featured image id only when it passes the consent rule.
 */
function pamoja_get_publishable_thumbnail_id( int $post_id ): int {
	$id = (int) get_post_thumbnail_id( $post_id );
	return $id && pamoja_image_is_publishable( $id ) ? $id : 0;
}

/**
 * Expose consent + alt to the media modal so the gallery picker can show
 * status as soon as a photo is chosen.
 */
function pamoja_attachment_js_fields( array $response, WP_Post $attachment ) {
	if ( wp_attachment_is_image( $attachment ) ) {
		$status                    = pamoja_image_status( $attachment->ID );
		$response['pamojaStatus']  = $status['label'];
		$response['pamojaOk']      = $status['ok'];
		$response['pamojaEditUrl'] = get_edit_post_link( $attachment->ID, 'raw' );
	}
	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'pamoja_attachment_js_fields', 10, 2 );

/**
 * Edit-screen warning listing any selected image the site is holding back.
 */
function pamoja_consent_admin_notice() {
	$screen = get_current_screen();
	if ( ! $screen || 'post' !== $screen->base || ! in_array( $screen->post_type, array( 'event', 'album', 'post' ), true ) ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $post_id ) {
		return;
	}
	$blocked = array();
	$ids     = pamoja_get_gallery_ids( $post_id );
	$thumb   = (int) get_post_thumbnail_id( $post_id );
	if ( $thumb ) {
		$ids[] = $thumb;
	}
	// Photographs placed in the body of the post are held to the same rule.
	$post = get_post( $post_id );
	foreach ( pamoja_content_image_tags( $post ? $post->post_content : '' ) as $tag ) {
		$id = pamoja_content_image_id( $tag );
		if ( $id ) {
			$ids[] = $id;
		} else {
			$blocked[] = esc_html__( 'An image in the text that is not in the media library — it will not be shown. Upload it, tick "Consent confirmed" and give it alt text.', 'pamoja' );
		}
	}
	foreach ( array_unique( $ids ) as $id ) {
		$status = pamoja_image_status( (int) $id );
		if ( ! $status['ok'] ) {
			$blocked[] = sprintf(
				'<a href="%s">%s</a> — %s',
				esc_url( get_edit_post_link( $id ) ),
				esc_html( get_the_title( $id ) ?: "#$id" ),
				esc_html( $status['label'] )
			);
		}
	}
	$blocked = array_values( array_unique( $blocked ) );
	if ( ! $blocked ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p><strong>%s</strong> %s</p><ul style="list-style:disc;padding-left:1.5em">%s</ul></div>',
		esc_html__( 'Some photos are being held back.', 'pamoja' ),
		esc_html__( 'They will not appear on the site until each one has alt text and "Consent confirmed" ticked:', 'pamoja' ),
		'<li>' . implode( '</li><li>', $blocked ) . '</li>' // Already escaped above.
	);
}
add_action( 'admin_notices', 'pamoja_consent_admin_notice' );

/**
 * The attachment id an <img> tag refers to, or 0 when it cannot be resolved.
 */
function pamoja_content_image_id( string $tag ): int {
	if ( preg_match( '/wp-image-(\d+)/', $tag, $m ) ) {
		return (int) $m[1];
	}
	if ( preg_match( '/\ssrc=["\']([^"\']+)["\']/i', $tag, $m ) ) {
		return (int) attachment_url_to_postid( $m[1] );
	}
	return 0;
}

/**
 * True when an <img> in post content may not be shown. An image whose
 * attachment cannot be resolved counts as held back: the rule is "when in
 * doubt, leave the photo out", and the edit screen lists every one of these.
 */
function pamoja_content_image_is_blocked( string $tag ): bool {
	$id = pamoja_content_image_id( $tag );
	return ! $id || ! pamoja_image_is_publishable( $id );
}

/**
 * Every <img> in a chunk of HTML.
 *
 * @return string[]
 */
function pamoja_content_image_tags( string $html ): array {
	return preg_match_all( '#<img\b[^>]*>#i', $html, $m ) ? $m[0] : array();
}

/**
 * The consent rule applied to the body of a post, not only to the featured
 * image and the gallery field. An editor who drops a photograph straight into
 * the editor is held to the same rule as everywhere else: no consent or no
 * alt text, and it does not reach the public site.
 */
function pamoja_filter_content_images( $html ) {
	$html = (string) $html;
	if ( is_admin() || ! str_contains( $html, '<img' ) ) {
		return $html;
	}
	// Innermost figures first, so a gallery loses only the photos it must.
	$html = preg_replace_callback(
		'#<figure\b[^>]*>(?:(?!<figure\b).)*?</figure>#is',
		static function ( array $m ): string {
			foreach ( pamoja_content_image_tags( $m[0] ) as $tag ) {
				if ( pamoja_content_image_is_blocked( $tag ) ) {
					return '';
				}
			}
			return $m[0];
		},
		$html
	);
	return preg_replace_callback(
		'#<img\b[^>]*>#i',
		static fn( array $m ): string => pamoja_content_image_is_blocked( $m[0] ) ? '' : $m[0],
		$html
	);
}
add_filter( 'the_content', 'pamoja_filter_content_images', 20 );
add_filter( 'the_excerpt', 'pamoja_filter_content_images', 20 );
