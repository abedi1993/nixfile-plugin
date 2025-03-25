<?php
/**
 * Plugin Name: NixFile Uploader
 * Description: Adds custom functionality when creating or editing posts/pages.
 * Version: 2.0.2
 * Author: Ali
 * Text Domain: nixfile-uploader
 */

defined( 'ABSPATH' ) || exit;

require_once plugin_dir_path( __FILE__ ) . 'inc/AdminHooks.php';

use NixFileUploader\AdminHooks;

function nixfile_uploader_init(): void {
	$admin_hooks = new AdminHooks();
//	$admin_hooks->register_hooks();
}

add_action( 'plugins_loaded', 'nixfile_uploader_init' );