<?php

namespace NixFileUploader;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;
use JetBrains\PhpStorm\NoReturn;

defined( 'ABSPATH' ) || exit;

class AdminHooks {
	private string $token;
	private string $email;
	private bool $daily_backup;
	private bool $show_status_navbar;
	private bool $compress_upload;
	private bool $compress_webp_upload;

	public function register_hooks(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'edit_form_after_editor', [ $this, 'inject_uploader_view' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_menu', [ $this, 'nixfile_uploader_menu' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		add_action( 'admin_bar_menu', [ $this, 'maybe_add_admin_bar_item' ], 100 );
		$this->load_settings();
	}

	public function register_rest_routes(): void {
		$namespace = 'nixfile/v1';
		register_rest_route( $namespace, '/token', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_token_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
			'args'                => [
				'token' => [
					'required'          => true,
					'type'              => 'string',
					'validate_callback' => function ( $param ) {
						return ! empty( trim( $param ) );
					},
					'sanitize_callback' => 'sanitize_text_field',
				],
			],
		] );
		register_rest_route( $namespace, '/email', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'store_email_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
			'args'                => [
				'email' => [
					'required'          => true,
					'type'              => 'string',
					'validate_callback' => function ( $param ) {
						return is_email( $param );
					},
					'sanitize_callback' => 'sanitize_email',
				],
			],
		] );
		register_rest_route( $namespace, '/daily-backup', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_daily_backup_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
			'args'                => [
				'daily_backup' => [
					'required'          => true,
					'type'              => 'boolean',
					'validate_callback' => function ( $param ) {
						return is_bool( $param );
					},
				],
			],
		] );
		register_rest_route( $namespace, '/show-status-navbar', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_show_status_navbar_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
			'args'                => [],
		] );
		register_rest_route( $namespace, '/compress-upload', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_compress_upload_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
			'args'                => [],
		] );
		register_rest_route( $namespace, '/compress-webp-upload', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_compress_webp_upload_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
			'args'                => [
				'compress_webp_upload' => [
					'required'          => true,
					'type'              => 'boolean',
					'validate_callback' => function ( $param ) {
						return is_bool( $param );
					},
				],
			],
		] );
		register_rest_route( $namespace, '/settings', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_all_settings_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
		] );
		register_rest_route( $namespace, '/settings', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'update_multiple_settings_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
			'args'                => [
				'token'                => [
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				],
				'email'                => [
					'type'              => 'string',
					'validate_callback' => function ( $param ) {
						return empty( $param ) || is_email( $param );
					},
					'sanitize_callback' => 'sanitize_email',
				],
				'daily_backup'         => [
					'type' => 'boolean',
				],
				'show_status_navbar'   => [
					'type' => 'boolean',
				],
				'compress_upload'      => [
					'type' => 'boolean',
				],
				'compress_webp_upload' => [
					'type' => 'boolean',
				],
			],
		] );
	}

	public function check_manage_options_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	private function load_settings(): void {
		$this->token                = get_option( 'nixfile_uploader_token', '' );
		$this->email                = get_option( 'nixfile_uploader_email', '' );
		$this->daily_backup         = (bool) get_option( 'nixfile_uploader_daily_backup', false );
		$this->show_status_navbar   = (bool) get_option( 'nixfile_uploader_show_status_navbar', false );
		$this->compress_upload      = (bool) get_option( 'nixfile_uploader_compress_upload', false );
		$this->compress_webp_upload = (bool) get_option( 'nixfile_uploader_compress_webp_upload', false );
	}

	// REST API Route Handlers

	public function set_token_rest( WP_REST_Request $request ): WP_REST_Response {
		$token = $request->get_param( 'token' );

		$this->token = $token;
		update_option( 'nixfile_uploader_token', $token );

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Token set successfully.', 'nixfile-uploader' ),
			'data'    => [
				'token' => $token
			]
		], 200 );
	}

	public function store_email_rest( WP_REST_Request $request ): WP_REST_Response {
		$email = $request->get_param( 'email' );

		$this->email = $email;
		update_option( 'nixfile_uploader_email', $email );

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Email stored successfully.', 'nixfile-uploader' ),
			'data'    => [
				'email' => $email
			]
		], 200 );
	}

	public function set_daily_backup_rest( WP_REST_Request $request ): WP_REST_Response {
		$daily_backup = $request->get_param( 'daily_backup' );

		$this->daily_backup = $daily_backup;
		update_option( 'nixfile_uploader_daily_backup', $daily_backup );

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Daily backup setting updated successfully.', 'nixfile-uploader' ),
			'data'    => [
				'daily_backup' => $daily_backup
			]
		], 200 );
	}

	public function set_show_status_navbar_rest( WP_REST_Request $request ): WP_REST_Response {
		$status = (bool) get_option( 'nixfile_uploader_show_status_navbar', false );
		update_option( 'nixfile_uploader_show_status_navbar', ! $status );

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Show status navbar setting updated successfully.', 'nixfile-uploader' ),
			'data'    => [
				'show_status_navbar' => get_option( "nixfile_uploader_show_status_navbar" )
			]
		], 200 );
	}

	public function set_compress_upload_rest( WP_REST_Request $request ): WP_REST_Response {
		update_option( 'nixfile_uploader_compress_upload', ! get_option( "nixfile_uploader_compress_upload", false ) );

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Compress upload setting updated successfully.', 'nixfile-uploader' ),
			'data'    => [
				'compress_upload' => get_option( "nixfile_uploader_compress_upload", false )
			]
		], 200 );
	}

	public function set_compress_webp_upload_rest( WP_REST_Request $request ): WP_REST_Response {
		$compress_webp = $request->get_param( 'compress_webp_upload' );
		$this->compress_webp_upload = $compress_webp;
		update_option( 'nixfile_uploader_compress_webp_upload', $compress_webp );
		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Compress WebP upload setting updated successfully.', 'nixfile-uploader' ),
			'data'    => [
				'compress_webp_upload' => $compress_webp
			]
		], 200 );
	}

	public function get_all_settings_rest( WP_REST_Request $request ): WP_REST_Response {
		return new WP_REST_Response( [
			'success' => true,
			'data'    => [
				'token'                => $this->token,
				'email'                => $this->email,
				'daily_backup'         => $this->daily_backup,
				'show_status_navbar'   => $this->show_status_navbar,
				'compress_upload'      => $this->compress_upload,
				'compress_webp_upload' => $this->compress_webp_upload,
			]
		], 200 );
	}

	public function update_multiple_settings_rest( WP_REST_Request $request ): WP_REST_Response {
		$updated_settings = [];
		if ( $request->has_param( 'token' ) ) {
			$token = $request->get_param( 'token' );
			if ( ! empty( trim( $token ) ) ) {
				$this->token = $token;
				update_option( 'nixfile_uploader_token', $token );
				$updated_settings['token'] = $token;
			}
		}
		if ( $request->has_param( 'email' ) ) {
			$email = $request->get_param( 'email' );
			if ( is_email( $email ) ) {
				$this->email = $email;
				update_option( 'nixfile_uploader_email', $email );
				$updated_settings['email'] = $email;
			}
		}
		$boolean_settings = [
			'daily_backup'         => 'nixfile_uploader_daily_backup',
			'show_status_navbar'   => 'nixfile_uploader_show_status_navbar',
			'compress_upload'      => 'nixfile_uploader_compress_upload',
			'compress_webp_upload' => 'nixfile_uploader_compress_webp_upload',
		];
		foreach ( $boolean_settings as $param_name => $option_name ) {
			if ( $request->has_param( $param_name ) ) {
				$value             = $request->get_param( $param_name );
				$this->$param_name = $value;
				update_option( $option_name, $value );
				$updated_settings[ $param_name ] = $value;
			}
		}
		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Settings updated successfully.', 'nixfile-uploader' ),
			'data'    => [
				'updated_settings' => $updated_settings,
				'all_settings'     => [
					'token'                => $this->token,
					'email'                => $this->email,
					'daily_backup'         => $this->daily_backup,
					'show_status_navbar'   => $this->show_status_navbar,
					'compress_upload'      => $this->compress_upload,
					'compress_webp_upload' => $this->compress_webp_upload,
				]
			]
		], 200 );
	}
	public function get_token(): string {
		return $this->token;
	}

	public function get_email(): string {
		return $this->email;
	}

	public function is_daily_backup_enabled(): bool {
		return $this->daily_backup;
	}

	public function is_show_status_navbar_enabled(): bool {
		return $this->show_status_navbar;
	}

	public function is_compress_upload_enabled(): bool {
		return $this->compress_upload;
	}

	public function is_compress_webp_upload_enabled(): bool {
		return $this->compress_webp_upload;
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
				'rest_url'         => rest_url( 'nixfile/v1/' ),
				'nonce'            => wp_create_nonce( 'wp_rest' ),
				'current_settings' => [
					'token'                => $this->token,
					'email'                => $this->email,
					'daily_backup'         => $this->daily_backup,
					'show_status_navbar'   => $this->show_status_navbar,
					'compress_upload'      => $this->compress_upload,
					'compress_webp_upload' => $this->compress_webp_upload,
				],
				'action'           => [
					'token'                => 'token',
					'email'                => 'email',
					'daily_backup'         => 'daily-backup',
					'show_status_navbar'   => 'show-status-navbar',
					'compress_upload'      => 'compress-upload',
					'compress_webp_upload' => 'compress-webp-upload',
					'all_settings'         => 'settings',
				],
				'images_url'       => plugin_dir_url( __DIR__ ) . 'assets/images/',
			] );
			wp_localize_script( 'nixfile-uploader-page-v2-script', 'nixfile_ajax_data', [
				'rest_url'         => rest_url( 'nixfile/v1/' ),
				'nonce'            => wp_create_nonce( 'wp_rest' ),
				'current_settings' => [
					'token'                => $this->token,
					'email'                => $this->email,
					'daily_backup'         => $this->daily_backup,
					'show_status_navbar'   => $this->show_status_navbar,
					'compress_upload'      => $this->compress_upload,
					'compress_webp_upload' => $this->compress_webp_upload,
				],
				'action'           => [
					'token'                => 'token',
					'email'                => 'email',
					'daily_backup'         => 'daily-backup',
					'show_status_navbar'   => 'show-status-navbar',
					'compress_upload'      => 'compress-upload',
					'compress_webp_upload' => 'compress-webp-upload',
					'all_settings'         => 'settings',
				],
				'images_url'       => plugin_dir_url( __DIR__ ) . 'assets/images/',
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
			wp_enqueue_script( 'html2canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js', [], null, true );
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
				'rest_url'         => rest_url( 'nixfile/v1/' ),
				'nonce'            => wp_create_nonce( 'wp_rest' ),
				'current_settings' => [
					'token'                => $this->token,
					'email'                => $this->email,
					'daily_backup'         => $this->daily_backup,
					'show_status_navbar'   => $this->show_status_navbar,
					'compress_upload'      => $this->compress_upload,
					'compress_webp_upload' => $this->compress_webp_upload,
				],
				'action'           => [
					'token'                => 'token',
					'email'                => 'email',
					'daily_backup'         => 'daily-backup',
					'show_status_navbar'   => 'show-status-navbar',
					'compress_upload'      => 'compress-upload',
					'compress_webp_upload' => 'compress-webp-upload',
					'all_settings'         => 'settings',
				],
				'images_url'       => plugin_dir_url( __DIR__ ) . 'assets/images/',
			] );
			add_filter( 'script_loader_tag', static function ( $tag, $handle, $src ) {
				if ( $handle === 'nixfile-uploader-page-script' || $handle === 'nixfile-uploader-page-v2-script' ) {
					return '<script type="module" src="' . esc_url( $src ) . '"></script>';
				}

				return $tag;
			}, 10, 3 );
		}

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
		wp_localize_script( 'nixfile-uploader-admin-script', 'nixfile_ajax_data', [
			'rest_url'         => rest_url( 'nixfile/v1/' ),
			'nonce'            => wp_create_nonce( 'wp_rest' ),
			'current_settings' => [
				'token'                => $this->token,
				'email'                => $this->email,
				'daily_backup'         => $this->daily_backup,
				'show_status_navbar'   => $this->show_status_navbar,
				'compress_upload'      => $this->compress_upload,
				'compress_webp_upload' => $this->compress_webp_upload,
			],
			'action'           => [
				'token'                => 'token',
				'email'                => 'email',
				'daily_backup'         => 'daily-backup',
				'show_status_navbar'   => 'show-status-navbar',
				'compress_upload'      => 'compress-upload',
				'compress_webp_upload' => 'compress-webp-upload',
				'all_settings'         => 'settings',
			],
		] );
	}

	public function maybe_add_admin_bar_item(): void {
		if ( ! is_admin_bar_showing() ) {
			return;
		}

		$show = get_option( 'nixfile_uploader_show_status_navbar', false );
		if ( ! $show ) {
			return;
		}
		$response = wp_remote_get( "http://192.168.0.244:7000/v1/upload-stats/?domain_id={$this->token}", [
			'headers' => [
				'Accept' => 'application/json',
			],
			'timeout' => 5,
		] );

		if ( is_wp_error( $response ) ) {
			$title = '❌ اتصال به سرور انجام نشد';
		} else {
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			if (
				isset( $body['data']['uploaded'], $body['data']['capacity'] ) && $body['data']['capacity'] > 0
			) {
				$percent  = number_format( (float) ( ( $body['data']['uploaded'] * 100 ) / $body['data']['capacity'] ), 2 );
				$duration = isset( $body['data']['duration'] ) ? (int) $body['data']['duration'] : null;
				$title    = "📦 استفاده ‌شده: {$percent}%";
				if ( ! is_null( $duration ) ) {
					$title .= " | ⏳ {$duration} روز مانده";
				}
			} else {
				$title = '⚠️ اطلاعات ناقص';
			}
		}

		global $wp_admin_bar;

		$wp_admin_bar->add_node( [
			'id'    => 'nixfile_status_bar',
			'title' => $title,
			'href'  => admin_url( 'upload.php?page=nixfile-file-manager' ),
			'style' => 'border: 1px solid red; padding: 3px 6px; border-radius: 5px; display: inline-block;',
			'meta'  => [
				'class' => 'nixfile-status-item',
				'html'  => '',
				'title' => 'وضعیت آپلود',
				'style' => 'border: 1px solid red; padding: 3px 6px; border-radius: 5px; display: inline-block;',
			]
		] );
	}

}