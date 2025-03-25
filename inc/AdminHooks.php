<?php

namespace NixFileUploader;
defined( 'ABSPATH' ) || exit;

class AdminHooks {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'edit_form_after_editor', [ $this, 'inject_uploader_view' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
	}

	public function inject_uploader_view(): void {
		/*$screen = get_current_screen();
		die($screen->post_type);
		if ( $screen->post_type === 'post' || $screen->post_type === 'page' ) {
			die("dasdas");

		}*/
	}

	public function enqueue_admin_assets( $hook ): void {
		$screen = get_current_screen();
		/*if ( isset( $_GET['action'] ) && $_GET['action'] === 'elementor' ) {
			die($screen->base);
			wp_enqueue_style(
				'nixfile-uploader-admin-style',
				plugin_dir_url( __DIR__ ) . 'assets/css/admin.css',
				[],
				time()
			);

			wp_enqueue_script(
				'nixfile-uploader-admin-script',
				plugin_dir_url( __DIR__ ) . 'assets/js/admin.js',
				[ 'jquery' ],
				time(),
				true
			);

			return;
		}
		*/

		if ( ! in_array( $screen->base, [ 'post', 'page' ] ) ) {
			return;
		}
		wp_enqueue_style(
			'nixfile-uploader-admin-style',
			plugin_dir_url( __DIR__ ) . 'assets/css/admin.css',
			[],
			time()
		);
		include plugin_dir_path( __DIR__ ) . 'view/uploader.php';
		wp_enqueue_script(
			'nixfile-uploader-admin-script',
			plugin_dir_url( __DIR__ ) . 'assets/js/admin.js',
			[ 'jquery' ],
			time(),
			true
		);
	}
}
