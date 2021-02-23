<?php

/**
 * Fired during plugin activation
 *
 * @link       https://tonicpow.com
 * @since      1.0.0
 *
 * @package    Tonicpow
 * @subpackage Tonicpow/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tonicpow
 * @subpackage Tonicpow/includes
 * @author     TonicPow <support@tonicpow.com>
 */
class Tonicpow_Activator
{



	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		$pluginlog = '/var/www/html/wp-content/plugins/tonicpow/debug.log'; // plugin_dir_path(__FILE__).'debug.log';
		$message = 'WC-TONICPOW-ADMIN activating' . PHP_EOL;
		error_log($message, 3, $pluginlog);
	}
}
