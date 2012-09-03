<?php
/*
Plugin Name: Smugmug Insert
Plugin URI: http://mpriess.de
Description: Browse photos from SmugMug and add them to post/pages.
Version: 1.0
Author: Marten Prieß
Author URI: http://mpriess.de
*/

define( 'SMUGINS_VERSION', '0.5.0' );
define( 'SMUGINS_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'SMUGINS_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) );

/* Required Files */
require_once('functions/function.inc.php');
require_once('classes/class.admin.php');

/* Instantiate the admin class class */
$admin = new SMUGINS_Admin();

/* Wordpress Activate/Deactivate */
register_activation_hook( __FILE__, array( $admin, 'smugins_activate' ) );
register_deactivation_hook( __FILE__, array( $admin, 'smugins_deactivate' ) );

/* Required action filters */
add_action( 'admin_menu', array( $admin, 'smugins_admin_menu' ) );
add_action( 'media_buttons', array( $admin, 'smugins_media_button'), 20 );add_action( 'media_upload_'.md5('smugins'), 'smugins_media_upload' );

function smugins_media_upload() {
	// we do not need default script for media_upload
	wp_deregister_script('swfupload-all');
	wp_deregister_script('swfupload-handlers');
	wp_deregister_script('image-edit');
	wp_deregister_script('set-post-thumbnail' );
	wp_deregister_script('imgareaselect');
	wp_deregister_script('utils');
	wp_deregister_script('plupload');
	
	wp_enqueue_script('smugins-dialog', plugins_url('/script/dialog.js', __FILE__), array('jquery'));

	wp_register_style( 'smugins-style', plugins_url('/script/dialog.css', __FILE__), false, '1.0', 'all'  );
	wp_enqueue_style( 'smugins-style' );	
	
	wp_iframe('smugins_iframe');
}
function smugins_iframe() {
  global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types;
  wp_head();
  include SMUGINS_PLUGIN_DIR.'/pages/gallery.php';
}