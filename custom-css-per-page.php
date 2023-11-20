<?php
/**
 * Plugin Name: Custom CSS Per Page
 * Description: Add custom CSS to specific pages.
 * Version: 1.0
 * Author: Balázs Piller
 * Author URI: https://webwizwork.com
 * Text Domain: custom-css-per-page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Enqueue JavaScript for Quick Edit
function ccp_enqueue_quick_edit_script( $hook ) {
	if ( 'edit.php' !== $hook ) {
		return;
	}
	wp_enqueue_script( 'custom-css-quick-edit', plugins_url( '/custom-css-quick-edit.js', __FILE__ ), array( 'jquery' ), '1.0', true );

	// Localize the script with new data
	$translation_array = array(
		'nonce' => wp_create_nonce( 'ccp_custom_css_nonce_action' )
	);
	wp_localize_script( 'custom-css-quick-edit', 'CCP_CustomCSS', $translation_array );
}
add_action( 'admin_enqueue_scripts', 'ccp_enqueue_quick_edit_script' );

// Add metabox for custom CSS
function ccp_add_custom_css_metabox() {
	add_meta_box( 'ccp_custom_css', __( 'Custom CSS', 'custom-css-per-page' ), 'ccp_custom_css_callback', 'post', 'side' );
}
add_action( 'add_meta_boxes', 'ccp_add_custom_css_metabox' );

// Metabox callback function
function ccp_custom_css_callback( $post ) {
	// Add nonce for security
	wp_nonce_field( 'ccp_custom_css_nonce_action', 'ccp_custom_css_nonce' );

	// Get existing CSS if any
	$custom_css = get_post_meta( $post->ID, '_ccp_custom_css', true );

	echo '<textarea style="width:100%;" rows="5" name="ccp_custom_css">' . esc_textarea( $custom_css ) . '</textarea>';
}

// Save custom CSS
function ccp_save_custom_css( $post_id ) {
	// Check nonce
	if ( ! isset( $_POST['ccp_custom_css_nonce'] ) || ! wp_verify_nonce( $_POST['ccp_custom_css_nonce'], 'ccp_custom_css_nonce_action' ) ) {
		return;
	}

	// Check user permission
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Update custom CSS
	if ( isset( $_POST['ccp_custom_css'] ) ) {
		update_post_meta( $post_id, '_ccp_custom_css', sanitize_textarea_field( $_POST['ccp_custom_css'] ) );
	}
}
add_action( 'save_post', 'ccp_save_custom_css' );

// Enqueue custom CSS in the front end
function ccp_enqueue_custom_css() {
	if ( is_singular() ) {
		global $post;
		$custom_css = get_post_meta( $post->ID, '_ccp_custom_css', true );
		if ( ! empty( $custom_css ) ) {
			echo '<style type="text/css">' . esc_html( $custom_css ) . '</style>';
		}
	}
}
add_action( 'wp_head', 'ccp_enqueue_custom_css' );

// Add a new column for custom CSS (invisible)
function ccp_add_custom_css_column( $columns ) {
	$columns['ccp_custom_css'] = 'Custom CSS';
	return $columns;
}
add_filter( 'manage_posts_columns', 'ccp_add_custom_css_column' );

// Populate the custom CSS column with data
function ccp_custom_css_column_content( $column_name, $post_id ) {
	if ( 'ccp_custom_css' === $column_name ) {
		$custom_css = get_post_meta( $post_id, '_ccp_custom_css', true );
		if ( ! empty( $custom_css ) ) {
			echo '<div class="ccp-custom-css-yes"><i class="dashicons dashicons-yes"></i></div>';
		}
		echo '<div class="ccp-custom-css-data" style="display: none;">' . esc_html( $custom_css ) . '</div>';
	}
}
add_action( 'manage_posts_custom_column', 'ccp_custom_css_column_content', 10, 2 );
