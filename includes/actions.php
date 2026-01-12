<?php
/**
 *
 *  @package Main\Includes\Actions
 * 	@since 1.1.0
 *
 *
 */

 if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
add_action('init', 'hreflang_pro_textdomain');
add_action('admin_init', 'hreflang_pro_register_settings');
add_action('admin_menu', 'hreflang_pro_admin_actions');
add_action('wp_head','add_hreflang_pro_to_head');
add_action( 'save_post', 'hreflang_pro_save_meta_data' );
add_action('create_term', 'href_save_term_meta_data');
add_action('edit_term', 'href_save_term_meta_data');
add_action( 'admin_enqueue_scripts', 'hreflang_pro_bulk_enqueue' );
add_action( 'admin_enqueue_scripts', 'hreflang_pro_enqueue' );
add_action( 'wp_enqueue_scripts', 'hreflang_pro_enqueue' );
add_action('add_meta_boxes', 'add_hreflang_pro_meta_box');
add_action( 'wp_ajax_hreflang_pro_save_from_bulk_editor', 'hreflang_pro_save_from_bulk_editor' );
add_action( 'wp_ajax_hreflang_pro_delete_from_bulk_editor', 'hreflang_pro_delete_from_bulk_editor' );
add_action( 'wp_ajax_validate_hreflang_tags', 'validate_hreflang_tags' );
add_action('load-hreflang-pro_page_hreflang_pro_bulk_editor', 'hreflang_tags_pro_bulk_editor_screen_options');
add_action('load-hreflang-pro_page_hreflang_pro_validator', 'hreflang_tags_pro_validator_screen_options');
add_action( 'plugins_loaded', 'hreflang_tags_pro_taxonomy_forms');
add_action( 'admin_init' , 'hreflang_tags_pro_version_fix');
add_action( 'wp_ajax_hreflang_pro_delete_entry_from_bulk_editor', 'hreflang_pro_delete_entry_from_bulk_editor' );
add_action( 'wp_ajax_hreflang_pro_delete_html_entry_from_bulk_editor', 'hreflang_pro_delete_html_entry_from_bulk_editor' );
add_action( 'wp_ajax_hreflang_pro_bulk_get_all_pages', 'hreflang_pro_bulk_get_all_pages' );
add_action( 'wp_ajax_hreflang_pro_do_sitemap', 'hreflang_pro_do_sitemap' );
add_action( 'rest_api_init', 'hreflang_pro_register_rest_fields' );
