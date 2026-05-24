<?php
/**
 * Plugin Name: TS Tyre Outlet Finder
 * Description: Interactive outlet finder with Leaflet map. Manage outlets and styling from wp-admin. Shortcode: <code>[ts_tyre_outlets]</code>
 * Version:     1.0.2
 * Author:      Your Name
 * Text Domain: ts-tof
 * Domain Path: /languages
 *
 * @package TS_Tyre_Outlet_Finder
 */

if (!defined('ABSPATH')) {
    exit;
}

define('TS_TOF_VERSION', '1.0.2');
define('TS_TOF_FILE', __FILE__);
define('TS_TOF_PATH', plugin_dir_path(__FILE__));
define('TS_TOF_URL', plugin_dir_url(__FILE__));

require_once TS_TOF_PATH . 'includes/class-post-type.php';
require_once TS_TOF_PATH . 'includes/class-settings.php';
require_once TS_TOF_PATH . 'includes/class-shortcode.php';
require_once TS_TOF_PATH . 'lib/plugin-update-checker.php';

new TS_TOF_Post_Type();
new TS_TOF_Shortcode();

if (is_admin()) {
    new TS_TOF_Settings();

    $updateChecker = \YahnisElsts\PluginUpdateChecker\v5p5\PucFactory::buildUpdateChecker(
        'https://github.com/lynesslim/supercraft-outlet-finder-plugin',
        TS_TOF_FILE,
        'ts-tyre-outlet-finder'
    );

    if ($updateChecker && method_exists($updateChecker, 'getVcsApi') && $updateChecker->getVcsApi()) {
        $updateChecker->getVcsApi()->enableReleaseAssets();
    }
}
