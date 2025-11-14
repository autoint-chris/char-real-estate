<?php
/**
 * Plugin Name: Property Listings
 * Plugin URI: https://example.com/property-listings
 * Description: A comprehensive WordPress plugin for managing property listings with image service integration.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: property-listings
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('PROPERTY_LISTINGS_VERSION', '1.0.0');
define('PROPERTY_LISTINGS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PROPERTY_LISTINGS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_property_listings() {
    require_once PROPERTY_LISTINGS_PLUGIN_DIR . 'includes/class-property-listings-activator.php';
    Property_Listings_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_property_listings() {
    require_once PROPERTY_LISTINGS_PLUGIN_DIR . 'includes/class-property-listings-deactivator.php';
    Property_Listings_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_property_listings');
register_deactivation_hook(__FILE__, 'deactivate_property_listings');

/**
 * The core plugin class.
 */
require PROPERTY_LISTINGS_PLUGIN_DIR . 'includes/class-property-listings.php';

/**
 * Begins execution of the plugin.
 */
function run_property_listings() {
    $plugin = new Property_Listings();
    $plugin->run();
}
run_property_listings();
