<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://tonicpow.com
 * @since             1.0.0
 * @package           Tonicpow for WooCommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Tonicpow for WooCommerce
 * Plugin URI:        https://github.com/tonicpow/wordpress-plugin
 * Description:       Triggers conversions for when an order or action is completed. Passes TonicPow sessions to metadata field of woocommerce orders for connection to Zapier. Includes a Widget for displaying ads.
 * Version:           1.0.1
 * Author:            TonicPow
 * Author URI:        https://tonicpow.com
 * License:           GPL-2.0+
 * License URI:       https://github.com/tonicpow/wordpress-plugin/blob/master/LICENSE
 * Text Domain:       tonicpow
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('TONICPOW_VERSION', '1.0.0');
define('TONICPOW_DIR', plugin_dir_path(__FILE__));
define('TONICPOW', 'tonicpow');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tonicpow-activator.php
 */
function activate_tonicpow()
{
	// Plugin is active
	require_once plugin_dir_path(__FILE__) . 'includes/class-tonicpow-activator.php';
	Tonicpow_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tonicpow-deactivator.php
 */
function deactivate_tonicpow()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-tonicpow-deactivator.php';
	Tonicpow_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_tonicpow');
register_deactivation_hook(__FILE__, 'deactivate_tonicpow');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-tonicpow.php';

/**
 * Load the widget
 */
require plugin_dir_path(__FILE__) . 'includes/widget.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function TONICPOW()
{
	return Tonicpow::getInstance();
}

TONICPOW()->run();
