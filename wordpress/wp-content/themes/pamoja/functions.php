<?php
/**
 * Pamoja theme bootstrap.
 *
 * The tree is the map of the site, at three sizes: the home page hero, the
 * menu, and the small "you are here" mark in the header. Five pages —
 * Home, About, Events, Blog, Engage — plus a page per event and post.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PAMOJA_THEME_VERSION', '2.2.0' );
define( 'PAMOJA_THEME_DIR', get_template_directory() );
define( 'PAMOJA_THEME_URI', get_template_directory_uri() );

require_once PAMOJA_THEME_DIR . '/inc/setup.php';
require_once PAMOJA_THEME_DIR . '/inc/copy.php';
require_once PAMOJA_THEME_DIR . '/inc/site-settings.php';
require_once PAMOJA_THEME_DIR . '/inc/content.php';
require_once PAMOJA_THEME_DIR . '/inc/site-map.php';
require_once PAMOJA_THEME_DIR . '/inc/tree.php';
require_once PAMOJA_THEME_DIR . '/inc/template-tags.php';
