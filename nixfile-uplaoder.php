<?php
/**
 * Plugin Name: آپلود فایل نیکس‌فایل
 * Description: فایل های شما را به صورت مستقیم، سریع و امن در فضای ابری نیکس‌ فایل ذخیره می ‌کند. (امنیت بالا، سرعت انتقال و کاربری آسان از ویژگی‌های کلیدی این ابزار است)
 * Version: 1.0.0
 * Author: نیکس فایل
 * Text Domain: nixfile-uploader
 * Plugin URI: https://www.nixfile.com
 * Author URI: https://www.nixfile.com
 * Requires PHP: 8.0
 */

defined('ABSPATH') || exit;

// Define plugin constants
define('NIXFILE_UPLOADER_VERSION', '1.0.0');
define('NIXFILE_UPLOADER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NIXFILE_UPLOADER_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once NIXFILE_UPLOADER_PLUGIN_DIR . 'inc/AdminHooks.php';

use NixFileUploader\AdminHooks;

// Initialize the plugin
function nixfile_uploader_init(): void
{
    $admin_hooks = new AdminHooks();
    $admin_hooks->register_hooks();
}
add_action('plugins_loaded', 'nixfile_uploader_init');

// Add plugin action links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'nixfile_uploader_plugin_action_links');
function nixfile_uploader_plugin_action_links($links)
{
    $settings_link = sprintf('<a href="%1$s">%2$s</a>', admin_url('upload.php?page=nixfile-file-manager'), esc_html__('Settings', 'nixfile-uploader'));
    array_unshift($links, $settings_link);

    $go_pro_text = esc_html__("خرید سرویس", 'nixfile-uploader');
    $links['go_pro'] = sprintf('<a href="%1$s" target="_blank" class="elementor-plugins-gopro">%2$s</a>', 'https://nixfile.com/', $go_pro_text);

    return $links;
}

// Activation hook - schedule daily backup if already enabled
register_activation_hook(__FILE__, 'nixfile_uploader_activate');
function nixfile_uploader_activate() {
    // Check if daily backup is already enabled and schedule the cron job if needed
    if (get_option('nixfile_uploader_daily_backup', false)) {
        if (!wp_next_scheduled('nixfile_daily_backup_event')) {
            wp_schedule_event(time(), 'daily', 'nixfile_daily_backup_event');
        }
        // Trigger an immediate backup on activation if the feature is already enabled
        wp_schedule_single_event(time(), 'nixfile_immediate_backup_event');
    }
}

// Deactivation hook - clean up scheduled tasks
register_deactivation_hook(__FILE__, 'nixfile_uploader_deactivate');
function nixfile_uploader_deactivate() {
    // Clear the scheduled daily backup
    $timestamp = wp_next_scheduled('nixfile_daily_backup_event');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'nixfile_daily_backup_event');
    }

    // Clear any immediate backup event
    $timestamp = wp_next_scheduled('nixfile_immediate_backup_event');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'nixfile_immediate_backup_event');
    }
}