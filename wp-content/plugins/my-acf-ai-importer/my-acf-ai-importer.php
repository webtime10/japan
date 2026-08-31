<?php
/**
 * Plugin Name: My ACF AI Importer
 * Description: Admin JSON importer for ACF Flexible Content fields.
 * Version: 1.0.14
 * Author: Custom
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }

    $importer_js = plugin_dir_path(__FILE__) . 'js/acf-ai-importer.js';
    $importer_ver = is_readable($importer_js) ? (string) filemtime($importer_js) : '1';

    wp_enqueue_script(
        'my-acf-ai-importer-script',
        plugin_dir_url(__FILE__) . 'js/acf-ai-importer.js',
        ['jquery'],
        $importer_ver,
        true
    );
}, 100);

