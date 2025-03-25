<?php

namespace NixFileUploader;
defined( 'ABSPATH' ) || exit;

class AdminHooks {
	public function register_hooks(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'edit_form_after_editor', [ $this, 'inject_uploader_view' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_menu', [ $this, 'nixfile_uploader_menu' ] );
	}

	public function nixfile_uploader_menu(): void {
		add_submenu_page(
			'upload.php',
			'رسانه نیکس فایل',
			'رسانه نیکس فایل',
			'manage_options',
			'custom-media-submenu',
			[ $this, 'nixfile_uploader_page' ]
		);

	}

	public function nixfile_uploader_page(): void {
		include plugin_dir_path( __DIR__ ) . 'view/page.php';
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
		if ( $screen->base === 'media_page_custom-media-submenu' ) {
			wp_enqueue_style(
				'nixfile-uploader-page-style',
				plugin_dir_url( __DIR__ ) . 'assets/css/nix-file-page.css',
				[],
				time()
			);
			wp_enqueue_script(
				'nixfile-uploader-admin-script',
				plugin_dir_url( __DIR__ ) . 'assets/js/nix-file-page.js',
				[ 'jquery' ],
				time(),
				true
			);
		}
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
