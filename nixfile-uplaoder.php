<?php
/**
 * Plugin Name: آپلود فایل نیکس‌فایل
 * Description: فایل های شما را به صورت مستقیم، سریع و امن در فضای ابری نیکس‌ فایل ذخیره می ‌کند. (امنیت بالا، سرعت انتقال و کاربری آسان از ویژگی‌های کلیدی این ابزار است)
 * Version: 1.0.0
 * Author: نیکس فایل
 * Text Domain:: نیکس فایل
 * Plugin URI: https://www.nixfile.com
 * Author URI: https://www.nixfile.com
 * Requires PHP: 8.0
 */


defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'inc/AdminHooks.php';

use NixFileUploader\AdminHooks;

function nixfile_uploader_init(): void
{
    $admin_hooks = new AdminHooks();
    $admin_hooks->register_hooks();
}

add_action('plugins_loaded', 'nixfile_uploader_init');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'plugin_action_links');
function plugin_action_links($links)
{
    $settings_link = sprintf('<a href="%1$s">%2$s</a>', admin_url('admin.php?page=' . 'sadasd'), esc_html__('Settings'));
    array_unshift($links, $settings_link);
    $go_pro_text = esc_html__("خرید سرویس");
    $links['go_pro'] = sprintf('<a href="%1$s" target="_blank" class="elementor-plugins-gopro">%2$s</a>', 'https://nixfile.com/', $go_pro_text);
    return $links;
}
