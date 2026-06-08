<?php
/**
 * Plugin Name: Supercraft Outlet Finder
 * Description: Interactive outlet finder with Leaflet map. Manage outlets and styling from wp-admin. Shortcode: <code>[supercraft_outlets]</code>
 * Version:     1.0.8
 * Author:      Your Name
 * Text Domain: supercraft-of
 * Domain Path: /languages
 *
 * @package Supercraft_Outlet_Finder
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SC_OF_VERSION', '1.0.8');
define('SC_OF_FILE', __FILE__);
define('SC_OF_PATH', plugin_dir_path(__FILE__));
define('SC_OF_URL', plugin_dir_url(__FILE__));

require_once SC_OF_PATH . 'includes/class-post-type.php';
require_once SC_OF_PATH . 'includes/class-settings.php';
require_once SC_OF_PATH . 'includes/class-shortcode.php';
require_once SC_OF_PATH . 'lib/plugin-update-checker.php';

new SC_OF_Post_Type();
new SC_OF_Shortcode();

if (is_admin()) {
    new SC_OF_Settings();

    $updateChecker = \YahnisElsts\PluginUpdateChecker\v5p5\PucFactory::buildUpdateChecker(
        'https://github.com/lynesslim/supercraft-outlet-finder-plugin',
        SC_OF_FILE,
        'supercraft-outlet-finder'
    );

    if ($updateChecker) {
        $updateChecker->setBranch('main');
    }
}

