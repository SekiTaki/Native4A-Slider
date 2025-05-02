<?php
/**
 * Plugin Name: Banner Slider
 * Description: 一個簡單的圖片輪播 plugin，支援 shortcode、後台設定與 Bricks 元素。
 * Version: 1.0
 * Author: Taki
 */

// 防止直接存取
defined('ABSPATH') || exit;

// 載入 CSS 和 JS
function banner_slider_enqueue_assets() {
    wp_enqueue_style('banner-slider-style', plugin_dir_url(__FILE__) . 'assets/slider.css');
    wp_enqueue_script('banner-slider-frontend', plugin_dir_url(__FILE__) . 'assets/slider.js', [], false, true);
}
add_action('wp_enqueue_scripts', 'banner_slider_enqueue_assets');

// 註冊 Shortcode
function banner_slider_shortcode($atts = []) {
    $atts = shortcode_atts([
        'autoplay' => get_option('bs_slider_autoplay', 'no'),
        'interval' => get_option('bs_slider_interval', 5000),
    ], $atts);

    // 傳遞給模板使用
    set_query_var('bs_slider_autoplay', $atts['autoplay']);
    set_query_var('bs_slider_interval', $atts['interval']);

    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/slider-template.php';
    return ob_get_clean();
}
add_shortcode('banner_slider', 'banner_slider_shortcode');

// 載入後台與前台功能檔案
require_once plugin_dir_path(__FILE__) . 'includes/frontend.php';
require_once plugin_dir_path(__FILE__) . 'includes/backend.php';

