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
		add_submenu_page(
			'upload.php',
			'رسانه نیکس فایل ورژن ۲',
			'رسانه نیکس فایل ورژن ۲',
			'manage_options',
			'nixfile-file-manager',
			[ $this, 'nixfile_uploader_v2_page' ]
		);

	}

	public function nixfile_uploader_page(): void {
		include plugin_dir_path( __DIR__ ) . 'view/page.php';
	}

	public function nixfile_uploader_v2_page(): void {
		include plugin_dir_path( __DIR__ ) . 'view/file-manager.php';
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
		if ( in_array( $screen->base, [ 'media_page_custom-media-submenu' ] ) ) {
			wp_enqueue_style(
				'nixfile-uploader-page-style',
				plugin_dir_url( __DIR__ ) . 'assets/css/nix-file-page.css',
				[],
				time()
			);
			wp_enqueue_script(
				'progressbar-js',
				'https://cdn.jsdelivr.net/npm/progressbar.js@1.1.0/dist/progressbar.min.js',
				[],
				'1.1.0',
				true
			);
			wp_enqueue_script(
				'nixfile-uploader-page-script',
				plugin_dir_url( __DIR__ ) . 'assets/js/nix-file-page.js',
				[ 'jquery', 'progressbar-js' ],
				time(),
				true
			);
			wp_enqueue_script(
				'nixfile-uploader-page-v2-script',
				plugin_dir_url( __DIR__ ) . 'assets/js/nix-file-page-v2.js',
				[ 'jquery', 'progressbar-js' ],
				time(),
				true
			);
			wp_localize_script( 'nixfile-uploader-page-script', 'nixfile_ajax_data', [
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'nixfile_uploader_nonce' ),
				'token'      => $this->token,
				'action'     => [
					'set_token' => "nixfile_set_token",
				],
				'images_url' => plugin_dir_url( __DIR__ ) . 'assets/images/',
			] );
			wp_localize_script( 'nixfile-uploader-page-v2-script', 'nixfile_ajax_data', [
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'nixfile_uploader_nonce' ),
				'token'      => $this->token,
				'action'     => [
					'set_token' => "nixfile_set_token",
				],
				'images_url' => plugin_dir_url( __DIR__ ) . 'assets/images/',
			] );
			add_filter( 'script_loader_tag', static function ( $tag, $handle, $src ) {
				if ( $handle === 'nixfile-uploader-page-script' || $handle === 'nixfile-uploader-page-v2-script' ) {
					return '<script type="module" src="' . esc_url( $src ) . '"></script>';
				}

				return $tag;
			}, 10, 3 );
		}
		if ( $screen->base === "media_page_nixfile-file-manager" ) {
			wp_enqueue_script( 'anime-js', 'https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js', [], null, true );
			wp_enqueue_script('html2canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js', [], null, true);
			wp_enqueue_style(
				'nixfile-uploader-page-style',
				plugin_dir_url( __DIR__ ) . 'assets/css/nix-file-page.css',
				[],
				time()
			);
			wp_enqueue_script(
				'progressbar-js',
				'https://cdn.jsdelivr.net/npm/progressbar.js@1.1.0/dist/progressbar.min.js',
				[],
				'1.1.0',
				true
			);
			wp_enqueue_script(
				'nixfile-uploader-page-v2-script',
				plugin_dir_url( __DIR__ ) . 'assets/js/nix-file-page-v2.js',
				[ 'jquery', 'progressbar-js' ],
				time(),
				true
			);
			wp_localize_script( 'nixfile-uploader-page-v2-script', 'nixfile_ajax_data', [
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'nixfile_uploader_nonce' ),
				'token'      => $this->token,
				'action'     => [
					'set_token' => "nixfile_set_token",
				],
				'images_url' => plugin_dir_url( __DIR__ ) . 'assets/images/',
			] );
			add_filter( 'script_loader_tag', static function ( $tag, $handle, $src ) {
				if ( $handle === 'nixfile-uploader-page-script' || $handle === 'nixfile-uploader-page-v2-script' ) {
					return '<script type="module" src="' . esc_url( $src ) . '"></script>';
				}

				return $tag;
			}, 10, 3 );
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
			'token' => $this->token,
		] );
	}
}
