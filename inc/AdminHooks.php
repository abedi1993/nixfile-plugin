<?php

namespace NixFileUploader;

use JetBrains\PhpStorm\NoReturn;

defined( 'ABSPATH' ) || exit;

class AdminHooks {
	private string $token;

	public function register_hooks(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'edit_form_after_editor', [ $this, 'inject_uploader_view' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_menu', [ $this, 'nixfile_uploader_menu' ] );
		add_action( 'wp_ajax_nixfile_set_token', [ $this, 'nixfile_set_token' ] );
		$this->token = get_option( 'nixfile_uploader_token' );
	}

	#[NoReturn] public function nixfile_set_token(): void {
		check_ajax_referer( 'nixfile_uploader_nonce', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Unauthorized', 403 );
		}
		$token       = $_POST['token'];
		$response    = [
			'success'       => true,
			'message'       => __( "Token Set Successfully.", 'nixfile-uploader' ),
			'received_data' => $token
		];
		$this->token = $token;
		update_option( 'nixfile_uploader_token', $token );
		wp_send_json( $response );
		wp_die();
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
				'nixfile-uploader-page-script',
				plugin_dir_url( __DIR__ ) . 'assets/js/nix-file-page.js',
				[ 'jquery' ],
				time(),
				true
			);
			wp_localize_script( 'nixfile-uploader-page-script', 'nixfile_ajax_data', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'nixfile_uploader_nonce' ),
				'token'    => $this->token,
				'action'   => [
					'set_token' => "nixfile_set_token",
				]
			] );
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
		wp_localize_script( 'nixfile-uploader-admin-script', 'nixfile_setting_data', [
			'token' => $this->token
		] );
	}
}
