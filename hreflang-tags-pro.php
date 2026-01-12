<?php
/*
Plugin Name: Hreflang Tags WP
Plugin URI: https://github.com/skynetsfr/hreflang-tags-wordpress
Description: Smart implementation of HREFLANG meta tags into the head section of your WordPress site.
Version: 2.0.1
Author: Skynets
Author URI: https://github.com/skynetsfr
License: GPLv2 or later
Text Domain: hreflang-tags-pro
GitHub Plugin URI: skynetsfr/hreflang-tags-wordpress
Domain Path: /languages

    Copyright 2018-2026  Skynets

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'HREFLANG_PRO_VERSION', '2.0.1' );
define( 'HREFLANG_PRO_PLUGIN_FILE', __FILE__ );
define( 'HREFLANG_PRO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HREFLANG_PRO_PLUGIN_MAIN_PATH', plugin_dir_path( __FILE__ ) ); // Backward compatibility
define( 'HREFLANG_PRO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_NAME', 'Hreflang Tags WP' );
define( 'PLUGIN_URI', 'https://github.com/skynetsfr/hreflang-tags-wordpress' );

// Load core files (order matters - functions must be loaded before variables)
require_once HREFLANG_PRO_PLUGIN_DIR . 'includes/functions.php';
require_once HREFLANG_PRO_PLUGIN_DIR . 'includes/variables.php';
require_once HREFLANG_PRO_PLUGIN_DIR . 'includes/actions.php';
require_once HREFLANG_PRO_PLUGIN_DIR . 'includes/class-bulk-list-table.php';
require_once HREFLANG_PRO_PLUGIN_DIR . 'includes/class-validator-table.php';

// Activation hook
register_activation_hook( __FILE__, 'hreflang_pro_admin_actions' );

// Settings link on plugins page
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'hreflang_pro_plugin_settings_link' );

// Category hooks if enabled
$hreflang_post_types = get_option( 'hreflang_post_types' );
if ( is_array( $hreflang_post_types ) && in_array( 'categories', $hreflang_post_types ) ) {
	add_action( 'category_add_form_fields', 'add_hreflang_pro_to_category_form', 99 );
	add_action( 'category_edit_form_fields', 'add_hreflang_pro_to_category_edit_form', 10, 1 );
}
