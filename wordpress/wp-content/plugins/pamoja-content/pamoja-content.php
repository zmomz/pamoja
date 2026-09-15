<?php
/**
 * Plugin Name: Pamoja Content
 * Plugin URI:  https://github.com/zmomz/pamoja
 * Description: Content types for the Pamoja Cultural Collective site — events, blog, media albums, services, partners — plus the photo-consent rule, the inquiry inbox, and the launch-content importer. The Pamoja theme needs this plugin.
 * Version:     1.2.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author:      Pamoja Cultural Collective
 * License:     GPL-2.0-or-later
 * Text Domain: pamoja
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PAMOJA_CONTENT_VERSION', '1.2.0' );
define( 'PAMOJA_CONTENT_DIR', plugin_dir_path( __FILE__ ) );
define( 'PAMOJA_CONTENT_URL', plugin_dir_url( __FILE__ ) );

require_once PAMOJA_CONTENT_DIR . 'inc/post-types.php';
require_once PAMOJA_CONTENT_DIR . 'inc/fields.php';
require_once PAMOJA_CONTENT_DIR . 'inc/meta-boxes.php';
require_once PAMOJA_CONTENT_DIR . 'inc/consent.php';
require_once PAMOJA_CONTENT_DIR . 'inc/admin.php';
require_once PAMOJA_CONTENT_DIR . 'inc/inquiries.php';
require_once PAMOJA_CONTENT_DIR . 'inc/hardening.php';
require_once PAMOJA_CONTENT_DIR . 'inc/seed.php';

/**
 * Flush rewrite rules once the post types exist.
 */
function pamoja_content_activate() {
	pamoja_register_post_types();
	pamoja_register_taxonomies();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pamoja_content_activate' );

function pamoja_content_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pamoja_content_deactivate' );
