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
	private bool $avif_on_upload;
	private bool $jalali_converter;
	private bool $modern_template;
	private bool $external_featured_image_enabled = false; // Initialize with default value
	private string $default_external_image = 'https://bostak1337.ir/wp-content/uploads/2025/10/shakes-image.webp'; // Default external image URL

	public function register_hooks(): void {
		// Load settings first before initializing any features
		$this->load_settings();

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'edit_form_after_editor', [ $this, 'inject_uploader_view' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_menu', [ $this, 'nixfile_uploader_menu' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		add_action( 'admin_bar_menu', [ $this, 'maybe_add_admin_bar_item' ], 100 );

		// Initialize external featured image after settings are loaded
		$this->init_external_featured_image();

		add_filter( 'get_the_date', [ $this, 'convert_to_jalali' ], 10, 3 );
		add_filter( 'get_the_time', [ $this, 'convert_to_jalali' ], 10, 3 );
		add_filter( 'get_comment_date', [ $this, 'convert_to_jalali' ], 10, 3 );
		add_filter( 'get_post_time', [ $this, 'convert_to_jalali' ], 10, 3 );
	}

	// Initialize External Featured Image functionality
	private function init_external_featured_image() {
		// Only initialize if the feature is enabled
		if ( ! $this->external_featured_image_enabled ) {
			return;
		}

		// Add field to featured image box
		add_action( 'add_meta_boxes', [ $this, 'add_external_featured_image_meta_box' ] );

		// Save external featured image URL
		add_action( 'save_post', [ $this, 'save_external_featured_image' ] );

		// Replace featured image with external URL
		add_filter( 'post_thumbnail_html', [ $this, 'replace_featured_image_with_external' ], 10, 5 );
		add_filter( 'get_post_metadata', [ $this, 'fake_thumbnail_id' ], 10, 4 );
		add_filter( 'wp_get_attachment_image_src', [ $this, 'fake_attachment_image_src' ], 10, 4 );

		// Social media meta tags
		add_action( 'wp_head', [ $this, 'add_social_media_meta_tags' ], 5 );

		// Rank Math integration
		add_filter( 'rank_math/opengraph/facebook/image', [ $this, 'rank_math_image_override' ] );
		add_filter( 'rank_math/opengraph/twitter/image', [ $this, 'rank_math_image_override' ] );

		// Enqueue admin script for live preview
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_external_featured_image_script' ] );

		// Add external featured image to REST API
		add_action( 'rest_api_init', [ $this, 'add_external_featured_image_to_rest_api' ] );
	}

	// Add external featured image to REST API
	public function add_external_featured_image_to_rest_api() {
		register_rest_field(
			['post', 'page'],
			'external_featured_image',
			[
				'get_callback'    => [ $this, 'get_external_featured_image_for_rest' ],
				'update_callback' => null,
				'schema'          => [
					'description' => __( 'External featured image URL', 'nixfile-uploader' ),
					'type'        => 'string',
					'format'      => 'uri',
				],
			]
		);
	}

	// Get external featured image for REST API
	public function get_external_featured_image_for_rest( $post ) {
		if ( ! $this->external_featured_image_enabled ) {
			return null;
		}

		$external_url = get_post_meta( $post['id'], '_external_featured_image_url', true );
		return $external_url ? $external_url : null;
	}

	// Add external featured image meta box
	public function add_external_featured_image_meta_box() {
		add_meta_box(
			'external_featured_image',
			__( 'تصویر شاخص خارجی', 'nixfile-uploader' ),
			[ $this, 'render_external_featured_image_meta_box' ],
			[ 'post', 'page' ],
			'side',
			'low'
		);
	}

	// Render external featured image meta box
	public function render_external_featured_image_meta_box( $post ) {
		$external_url = get_post_meta( $post->ID, '_external_featured_image_url', true );
		$url = ! empty( $external_url ) ? $external_url : $this->default_external_image;

		?>
        <div class="external-featured-image-wrapper">
            <p>
                <label for="external_featured_image_url"><?php _e( 'آدرس تصویر خارجی', 'nixfile-uploader' ); ?></label>
            </p>
            <input type="url" id="external_featured_image_url" name="external_featured_image_url"
                   value="<?php echo esc_attr( $url ); ?>"
                   placeholder="https://example.com/image.jpg"
                   style="width: 100%; margin-bottom: 10px;"/>
            <div id="external-image-preview"
                 style="display: <?php echo ! empty( $external_url ) ? 'block' : 'none'; ?>; border: 1px solid #ddd; padding: 8px; border-radius: 4px; background: #f9f9f9;">
                <img src="<?php echo esc_url( $url ); ?>" alt="<?php _e( 'پیش‌نمایش', 'nixfile-uploader' ); ?>"
                     style="max-width: 100%; height: auto; display: block;"/>
            </div>
            <p class="description"><?php _e( 'آدرس تصویر خارجی را برای استفاده به عنوان تصویر شاخص وارد کنید. این تصویر جایگزین تصویر آپلود شده خواهد شد.', 'nixfile-uploader' ); ?></p>
        </div>
		<?php
	}

	// Save external featured image URL
	public function save_external_featured_image( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['external_featured_image_url'] ) ) {
			return;
		}

		$url = sanitize_text_field( $_POST['external_featured_image_url'] );

		if ( ! empty( $url ) && filter_var( $url, FILTER_VALIDATE_URL ) ) {
			// Only save if it's different from the default URL or if the default URL is empty
			if ( $url !== $this->default_external_image || empty( $this->default_external_image ) ) {
				update_post_meta( $post_id, '_external_featured_image_url', esc_url_raw( $url ) );
			}
		} else {
			delete_post_meta( $post_id, '_external_featured_image_url' );
		}
	}

	// Enqueue script for live preview
	public function enqueue_external_featured_image_script( $hook ) {
		if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ] ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->base, [ 'post', 'page' ] ) ) {
			return;
		}

		$script = <<<JS
        (function($) {
            $(document).ready(function() {
                var input = $('#external_featured_image_url');
                var preview = $('#external-image-preview');
                var previewImg = preview.find('img');
                var defaultUrl = '{$this->default_external_image}';
                
                function updatePreview() {
                    var url = input.val().trim();
                    if (url) {
                        previewImg.attr('src', url);
                        preview.show();
                    } else {
                        preview.hide();
                    }
                }
                
                input.on('input', updatePreview);
                updatePreview();
            });
        })(jQuery);
        JS;

		wp_add_inline_script( 'jquery', $script );
	}

	// Replace featured image with external URL
	public function replace_featured_image_with_external( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		if ( ! $this->external_featured_image_enabled ) {
			return $html;
		}

		$external_url = get_post_meta( $post_id, '_external_featured_image_url', true );
		if ( empty( $external_url ) ) {
			return $html; // Don't use default image automatically
		}

		$default_attr = [
			'class' => 'attachment-' . $size . ' size-' . $size . ' external-featured-image',
			'alt'   => get_the_title( $post_id ),
		];
		$attr         = wp_parse_args( $attr, $default_attr );

		return sprintf(
			'<img src="%s"%s />',
			esc_url( $external_url ),
			$this->get_attr_html( $attr )
		);
	}

	// Fake thumbnail ID for external images
	public function fake_thumbnail_id( $value, $object_id, $meta_key, $single ) {
		if ( ! $this->external_featured_image_enabled || $meta_key !== '_thumbnail_id' ) {
			return $value;
		}

		$external_url = get_post_meta( $object_id, '_external_featured_image_url', true );
		if ( ! empty( $external_url ) ) {
			return $single ? - 1 : [ - 1 ];
		}

		return $value;
	}

	// Fake attachment image source for external images
	public function fake_attachment_image_src( $image, $attachment_id, $size, $icon ) {
		if ( ! $this->external_featured_image_enabled || $attachment_id !== - 1 ) {
			return $image;
		}

		$post = get_post();
		if ( ! $post ) {
			return $image;
		}

		$external_url = get_post_meta( $post->ID, '_external_featured_image_url', true );
		if ( ! empty( $external_url ) ) {
			return [ $external_url, 0, 0, false ];
		}

		return $image;
	}

	// Add social media meta tags
	public function add_social_media_meta_tags() {
		if ( ! is_singular() || ! $this->external_featured_image_enabled ) {
			return;
		}

		$post_id      = get_queried_object_id();
		$external_url = get_post_meta( $post_id, '_external_featured_image_url', true );

		if ( ! empty( $external_url ) ) {
			echo '<meta property="og:image" content="' . esc_url( $external_url ) . '" />' . "\n";
			echo '<meta name="twitter:image" content="' . esc_url( $external_url ) . '" />' . "\n";
			echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		}
	}

	// Rank Math integration
	public function rank_math_image_override( $image ) {
		if ( ! is_singular() || ! $this->external_featured_image_enabled ) {
			return $image;
		}

		$post_id      = get_queried_object_id();
		$external_url = get_post_meta( $post_id, '_external_featured_image_url', true );

		if ( ! empty( $external_url ) ) {
			return $external_url;
		}

		return $image;
	}

	// Helper function to generate attribute HTML
	private function get_attr_html( $attr ) {
		$html = '';
		foreach ( $attr as $name => $value ) {
			$html .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
		}

		return $html;
	}

	// Convert Gregorian date to Jalali (Persian calendar)
	public function convert_to_jalali( $date, $format = '', $timestamp = null, $gmt = false ): string {
		// Check if Jalali converter is enabled
		if ( ! $this->jalali_converter ) {
			return $date;
		}

		$persian_months = [
			'ژانویه'  => 'January',
			'فوریه'   => 'February',
			'مارس'    => 'March',
			'آوریل'   => 'April',
			'مه'      => 'May',
			'می'      => 'May',
			'ژوئن'    => 'June',
			'ژوئیه'   => 'July',
			'جولای'   => 'July',
			'اوت'     => 'August',
			'سپتامبر' => 'September',
			'اکتبر'   => 'October',
			'نوامبر'  => 'November',
			'دسامبر'  => 'December',
		];

		foreach ( $persian_months as $fa => $en ) {
			if ( strpos( $date, $fa ) !== false ) {
				$date = str_replace( $fa, $en, $date );
				break;
			}
		}

		if ( ! $timestamp || ! is_numeric( $timestamp ) ) {
			$timestamp = strtotime( $date );
		}

		if ( ! $timestamp ) {
			return $date;
		}

		try {
			// Check if Morilog\Jalali\Jalalian class exists
			if ( class_exists( 'Morilog\\Jalali\\Jalalian' ) ) {
				return \Morilog\Jalali\Jalalian::forge( $timestamp )->format( '%A، %d %B %Y' );
			}

			// Fallback to a simple conversion if the class doesn't exist
			return $this->simple_jalali_conversion( $timestamp );
		} catch ( \Exception $e ) {
			return $date;
		}
	}

	// Simple Jalali conversion as fallback
	private function simple_jalali_conversion( $timestamp ) {
		$g_d = date( 'j', $timestamp );
		$g_m = date( 'n', $timestamp );
		$g_y = date( 'Y', $timestamp );

		$g_days_in_month = array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 );
		$j_days_in_month = array( 31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29 );

		$gy = $g_y - 1600;
		$gm = $g_m - 1;
		$gd = $g_d - 1;

		$g_day_no = 365 * $gy + intval( ( $gy + 3 ) / 4 ) - intval( ( $gy + 99 ) / 100 ) + intval( ( $gy + 399 ) / 400 );

		for ( $i = 0; $i < $gm; ++ $i ) {
			$g_day_no += $g_days_in_month[ $i ];
		}

		$g_day_no += $gd;

		$j_day_no = $g_day_no - 79;

		$j_np     = intval( $j_day_no / 12053 );
		$j_day_no %= 12053;

		$jy       = 979 + 33 * $j_np + 4 * intval( $j_day_no / 1461 );
		$j_day_no %= 1461;

		if ( $j_day_no >= 366 ) {
			$jy       += intval( ( $j_day_no - 1 ) / 365 );
			$j_day_no = ( $j_day_no - 1 ) % 365;
		}

		for ( $i = 0; $i < 11 && $j_day_no >= $j_days_in_month[ $i ]; ++ $i ) {
			$j_day_no -= $j_days_in_month[ $i ];
		}

		$jm = $i + 1;
		$jd = $j_day_no + 1;

		$j_month_name = array(
			'',
			'فروردین',
			'اردیبهشت',
			'خرداد',
			'تیر',
			'مرداد',
			'شهریور',
			'مهر',
			'آبان',
			'آذر',
			'دی',
			'بهمن',
			'اسفند'
		);
		$j_day_name   = array( 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه', 'شنبه' );
		$day_of_week  = date( 'w', $timestamp );

		return $j_day_name[ $day_of_week ] . '، ' . $jd . ' ' . $j_month_name[ $jm ] . ' ' . $jy;
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

		register_rest_route( $namespace, '/avif-upload', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_avif_upload_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
			'args'                => [],
		] );

		// New route for Jalali converter
		register_rest_route( $namespace, '/jalali-converter', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_jalali_converter_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
			'args'                => [],
		] );

		register_rest_route( $namespace, '/modern-template', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_modern_template_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
			'args'                => [],
		] );

		// New route for external featured image
		register_rest_route( $namespace, '/external-featured-image', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'set_external_featured_image_rest' ],
			'permission_callback' => [ $this, 'check_manage_options_permission' ],
			'args'                => [],
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
				'jalali_converter'     => [
					'type' => 'boolean',
				],
				'modern_template'      => [
					'type' => 'boolean',
				],
				'external_featured_image' => [
					'type' => 'boolean',
				],
				'default_external_image' => [
					'type'              => 'string',
					'sanitize_callback' => 'esc_url_raw',
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
		$this->avif_on_upload       = (bool) get_option( 'nixfile_uploader_avif_on_upload', false );
		$this->jalali_converter     = (bool) get_option( 'nixfile_uploader_jalali_converter', false );
		$this->modern_template      = (bool) get_option( 'nixfile_uploader_modern_template', false );
		$this->external_featured_image_enabled = (bool) get_option( 'nixfile_uploader_external_featured_image', false );
		$this->default_external_image = get_option( 'nixfile_uploader_default_external_image', 'https://bostak1337.ir/wp-content/uploads/2025/10/shakes-image.webp' );
	}

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
		$compress_webp              = $request->get_param( 'compress_webp_upload' );
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

	public function set_avif_upload_rest( WP_REST_Request $request ): WP_REST_Response {
		$avif = get_option( "nixfile_uploader_avif_on_upload" );
		update_option( 'nixfile_uploader_avif_on_upload', ! $avif );

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Avif upload setting updated successfully.', 'nixfile-uploader' ),
			'data'    => []
		], 200 );
	}

	// New method for Jalali converter
	public function set_jalali_converter_rest( WP_REST_Request $request ): WP_REST_Response {
		$jalali = get_option( "nixfile_uploader_jalali_converter", false );
		update_option( 'nixfile_uploader_jalali_converter', ! $jalali );
		$this->jalali_converter = ! $jalali;

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Jalali converter setting updated successfully.', 'nixfile-uploader' ),
			'data'    => [
				'jalali_converter' => get_option( "nixfile_uploader_jalali_converter" )
			]
		], 200 );
	}

	public function set_modern_template_rest( WP_REST_Request $request ): WP_REST_Response {
		$modern = get_option( "nixfile_uploader_modern_template", false );
		update_option( 'nixfile_uploader_modern_template', ! $modern );
		$this->modern_template = ! $modern;

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Modern template setting updated successfully.', 'nixfile-uploader' ),
			'data'    => [
				'modern_template' => get_option( "nixfile_uploader_modern_template" )
			]
		], 200 );
	}

	// New method for external featured image
	public function set_external_featured_image_rest( WP_REST_Request $request ): WP_REST_Response {
		$external_featured_image = get_option( "nixfile_uploader_external_featured_image", false );
		update_option( 'nixfile_uploader_external_featured_image', ! $external_featured_image );
		$this->external_featured_image_enabled = ! $external_featured_image;

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'External featured image setting updated successfully.', 'nixfile-uploader' ),
			'data'    => [
				'external_featured_image' => get_option( "nixfile_uploader_external_featured_image" )
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
				'jalali_converter'     => $this->jalali_converter,
				'modern_template'      => $this->modern_template,
				'external_featured_image' => $this->external_featured_image_enabled,
				'default_external_image' => $this->default_external_image,
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
			'jalali_converter'     => 'nixfile_uploader_jalali_converter',
			'modern_template'      => 'nixfile_uploader_modern_template',
			'external_featured_image' => 'nixfile_uploader_external_featured_image',
		];
		foreach ( $boolean_settings as $param_name => $option_name ) {
			if ( $request->has_param( $param_name ) ) {
				$value             = $request->get_param( $param_name );
				$this->$param_name = $value;
				update_option( $option_name, $value );
				$updated_settings[ $param_name ] = $value;
			}
		}

		// Handle default external image URL
		if ( $request->has_param( 'default_external_image' ) ) {
			$default_image = $request->get_param( 'default_external_image' );
			if ( ! empty( $default_image ) && filter_var( $default_image, FILTER_VALIDATE_URL ) ) {
				$this->default_external_image = $default_image;
				update_option( 'nixfile_uploader_default_external_image', $default_image );
				$updated_settings['default_external_image'] = $default_image;
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
					'jalali_converter'     => $this->jalali_converter,
					'modern_template'      => $this->modern_template,
					'external_featured_image' => $this->external_featured_image_enabled,
					'default_external_image' => $this->default_external_image,
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

	// New getter for Jalali converter
	public function is_jalali_converter_enabled(): bool {
		return $this->jalali_converter;
	}

	public function is_modern_template_enabled(): bool {
		return $this->modern_template;
	}

	// New getter for external featured image
	public function is_external_featured_image_enabled(): bool {
		return $this->external_featured_image_enabled;
	}

	// New getter for default external image
	public function get_default_external_image(): string {
		return $this->default_external_image;
	}

	public function nixfile_uploader_menu(): void {
		add_submenu_page(
			'upload.php',
			'رسانه نیکس فایل',
			'رسانه نیکس فایل',
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
			if ( $this->is_modern_template_enabled() ) {
				wp_enqueue_style(
					'modern-file-manager-style',
					plugin_dir_url( __DIR__ ) . 'assets/css/modern-file-manager.css',
					[],
					time()
				);
			} else {
				wp_enqueue_style(
					'nixfile-uploader-page-style',
					plugin_dir_url( __DIR__ ) . 'assets/css/nix-file-page.css',
					[],
					time()
				);
			}
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
					'avif_on_upload'       => $this->avif_on_upload,
					'jalali_converter'     => $this->jalali_converter,
					'modern_template'      => $this->modern_template,
					'external_featured_image' => $this->external_featured_image_enabled,
					'default_external_image' => $this->default_external_image,
				],
				'action'           => [
					'token'                => 'token',
					'email'                => 'email',
					'daily_backup'         => 'daily-backup',
					'show_status_navbar'   => 'show-status-navbar',
					'compress_upload'      => 'compress-upload',
					'compress_webp_upload' => 'compress-webp-upload',
					'avif_on_upload'       => 'avif-upload',
					'jalali_converter'     => 'jalali-converter',
					'modern_template'      => 'modern-template',
					'external_featured_image' => 'external-featured-image',
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
					'avif_on_upload'       => $this->avif_on_upload,
					'jalali_converter'     => $this->jalali_converter,
					'modern_template'      => $this->modern_template,
					'external_featured_image' => $this->external_featured_image_enabled,
					'default_external_image' => $this->default_external_image,
				],
				'action'           => [
					'token'                => 'token',
					'email'                => 'email',
					'daily_backup'         => 'daily-backup',
					'show_status_navbar'   => 'show-status-navbar',
					'compress_upload'      => 'compress-upload',
					'compress_webp_upload' => 'compress-webp-upload',
					'avif_on_upload'       => 'avif-upload',
					'jalali_converter'     => 'jalali-converter',
					'modern_template'      => 'modern-template',
					'external_featured_image' => 'external-featured-image',
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
			if ( $this->is_modern_template_enabled() ) {
				wp_enqueue_style(
					'modern-file-manager-style',
					plugin_dir_url( __DIR__ ) . 'assets/css/modern-file-manager.css',
					[],
					time()
				);
			} else {
				wp_enqueue_style(
					'nixfile-uploader-page-style',
					plugin_dir_url( __DIR__ ) . 'assets/css/nix-file-page.css',
					[],
					time()
				);
			}
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
					'avif_on_upload'       => $this->avif_on_upload,
					'jalali_converter'     => $this->jalali_converter,
					'modern_template'      => $this->modern_template,
					'external_featured_image' => $this->external_featured_image_enabled,
					'default_external_image' => $this->default_external_image,
				],
				'action'           => [
					'token'                => 'token',
					'email'                => 'email',
					'daily_backup'         => 'daily-backup',
					'show_status_navbar'   => 'show-status-navbar',
					'compress_upload'      => 'compress-upload',
					'compress_webp_upload' => 'compress-webp-upload',
					'avif_on_upload'       => 'avif-upload',
					'jalali_converter'     => 'jalali-converter',
					'modern_template'      => 'modern-template',
					'external_featured_image' => 'external-featured-image',
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
			'url'              => "https://api.naring.ir/v1/",
			'nonce'            => wp_create_nonce( 'wp_rest' ),
			'current_settings' => [
				'token'                => $this->token,
				'email'                => $this->email,
				'daily_backup'         => $this->daily_backup,
				'show_status_navbar'   => $this->show_status_navbar,
				'compress_upload'      => $this->compress_upload,
				'compress_webp_upload' => $this->compress_webp_upload,
				'jalali_converter'     => $this->jalali_converter,
				'modern_template'      => $this->modern_template,
				'external_featured_image' => $this->external_featured_image_enabled,
				'default_external_image' => $this->default_external_image,
			],
			'action'           => [
				'token'                => 'token',
				'email'                => 'email',
				'daily_backup'         => 'daily-backup',
				'show_status_navbar'   => 'show-status-navbar',
				'compress_upload'      => 'compress-upload',
				'compress_webp_upload' => 'compress-webp-upload',
				'jalali_converter'     => 'jalali-converter',
				'modern_template'      => 'modern-template',
				'external_featured_image' => 'external-featured-image',
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
		$response = wp_remote_get( "https://api.naring.ir/v1/upload-stats/?domain_id={$this->token}", [
			'headers' => [
				'Accept' => 'application/json',
			],
			'timeout' => 5,
		] );

		if ( is_wp_error( $response ) ) {
			$title = '❌  عدم اتصال به سرور';
		} else {
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			if (
				isset( $body['data']['uploaded'], $body['data']['capacity'] ) && $body['data']['capacity'] > 0
			) {
				$percent  = intval( number_format( (float) ( ( $body['data']['uploaded'] * 100 ) / $body['data']['capacity'] ), 2 ) );
				$duration = isset( $body['data']['duration'] ) ? (int) $body['data']['duration'] : null;
				$title    = " حجم مصرفی: {$percent}%" .
				            " | ";
				if ( ! is_null( $duration ) ) {
					$title .= "انقضا: {$duration} روز";
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