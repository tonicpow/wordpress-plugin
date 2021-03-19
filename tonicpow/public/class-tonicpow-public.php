<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://tonicpow.com
 * @since      1.0.0
 *
 * @package    Tonicpow
 * @subpackage Tonicpow/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tonicpow
 * @subpackage Tonicpow/public
 * @author     TonicPow <support@tonicpow.com>
 */
class Tonicpow_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tonicpow_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tonicpow_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/tonicpow-public.css', array(), $this->version, 'all');
	}

	public function init()
	{

		$pluginlog = plugin_dir_path(__FILE__) . 'debug.log';

		if (isset($_GET["tncpw_session"])) {

			TONICPOW()->session("tncpw_session", sanitize_text_field($_GET["tncpw_session"]));

			// Set Woo Commerce session
			// TODO: Check woo commerce is installed (make plugin a requirement?)
			WC()->session->set('tncpw_session', TONICPOW()->session()["tncpw_session"]);

			// todo: remove error logging
			$message = "WP_LOADED_TONICPOW" . $_SERVER['REQUEST_URI'] . " - TNCPW_SESSION: " . TONICPOW()->session()["tncpw_session"] . PHP_EOL;
			error_log($message, 3, $pluginlog);
		}
	}

	public function GetCallingMethodName()
	{
		$e     = new Exception();
		$trace = $e->getTrace();
		//position 0 would be the line that called this function so we ignore it
		$last_call = $trace[1];

		return $last_call['function'];
	}

	// define the dynamic hook callback
	public function dynamicHook($arg1, $arg2 = [], $arg3 = [], $arg4 = [], $arg5 = [], $arg6 = [])
	{
		// TODO: get the arguments no matter how many parameters are set on the register hook. Tried to use splat operator w no success so far

		// set up log
		$pluginlog = plugin_dir_path(__FILE__) . 'debug.log';

		$goal_name         = TONICPOW()->session()["available_goals"][get_option("tonicpow_goal_name")[0]];
		$amount            = get_option("tonicpow_amount");
		$delay_in_minutes  = get_option("tonicpow_delay_in_minutes");
		$custom_dimensions = get_option("tonicpow_custom_dimensions");

		$trace  = debug_backtrace();
		$caller = $trace[3];

		$actionName = $caller['args'][0];

		$callerTrace  = $trace[4];
		$callerTrace2 = $trace[2];

		//string(32) "c20ad4d76fe97759aa27a0c99bff6710"
		// Array ( [file] => /var/www/html/wp-includes/class-wp-hook.php [line] => 311 [function] => apply_filters [class] => WP_Hook [type] => -> [args] => Array ( [0] => [1] => Array ( [0] => c20ad4d76fe97759aa27a0c99bff6710 [1] => 12 [2] => 1 [3] => 0 [4] => Array ( ) [5] => Array ( ) ) ) )
		// string(0) ""

		switch ($actionName) {
			case 'woocommerce_payment_complete':
				$order   = wc_get_order($arg1);
				$message = esc_html(__('Payment complete:', TONICPOW)) . " " . $order->{'total'} . PHP_EOL; // $cart_item_id, $product_id, $quantity, $variation_id, $variation, $cart_item_data "

				// TODO: remove error logs
				error_log($message, 3, $pluginlog);

				return $this->trigger_conversion($goal_name, $delay_in_minutes, $custom_dimensions, $order->{'total'});
				break;
			case 'woocommerce_add_to_cart':
				// $cart = json_encode($callerTrace);
				// $thing = json_encode($callerTrace2);
				// $arg = json_decode($arg6);
				// // $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data
				// $message = "Add to cart: " . $thing . ' - ' . $cart . ' ' . $arg . PHP_EOL; // $cart_item_id, $product_id, $quantity, $variation_id, $variation, $cart_item_data "
				$cartItem = WC()->cart->get_cart_item($arg1);

				// TODO: get the amount somehow  (remove error logs)
				$message = esc_html(__('cart item', TONICPOW)) . " " . json_encode($cartItem) . '' . esc_html(__('request:', TONICPOW)) . ' ' . $_REQUEST . PHP_EOL;
				error_log($message, 3, $pluginlog);

				$amount = 100;

				return $this->trigger_conversion($goal_name, $delay_in_minutes, $custom_dimensions, $amount);
				break;
			case "wp_login":
			case "comment_post":
			case 'woocommerce_payment_complete':
			case "media_upload_video":
			case "media_upload_file":
			case "media_upload_image":
			default:
				$message = "" . esc_html(__('Unrecognized method name:', TONICPOW)) . " " . $actionName . ' ' . esc_html(__('caller trace:', TONICPOW)) . ' ' . json_encode($callerTrace2) . PHP_EOL; // $cart_item_id, $product_id, $quantity, $variation_id, $variation, $cart_item_data "
				error_log($message, 3, $pluginlog);
				$amount = 100;

				return true;
		}

		// todo: commented because it's unreachable code
		// $message = "DYNAMIC HOOK! " . json_encode($arg6) . " caller: " . $actionName . " " . PHP_EOL; // $cart_item_id, $product_id, $quantity, $variation_id, $variation, $cart_item_data "
		// error_log($message, 3, $pluginlog);

		// $message = "Triggered Conversion from dynamic hook! " . $goal_name . PHP_EOL; // $cart_item_id, $product_id, $quantity, $variation_id, $variation, $cart_item_data "
		// error_log($message, 3, $pluginlog);
	}

	public function trigger_conversion($goal_name, $delay_in_minutes, $custom_dimensions, $amount = 0)
	{
		// Set up log
		$pluginlog = plugin_dir_path(__FILE__) . 'debug.log';
		$api_key   = get_option("tonicpow_api_key");

		// Get the tncpw_session
		// todo: check if set, otherwise empty string?
		$tncpw_session = TONICPOW()->session()["tncpw_session"]; // WC()->session->get('tncpw_session');

		// Trigger tonicpow conversion
		$base_api_url = get_option("tonicpow_base_api_url");
		$url          = $base_api_url . "conversions";
		$ch           = curl_init($url);

		// TODO: Delay in minutes is false here if not set. Should always be 0
		if (!$delay_in_minutes) {
			$delay_in_minutes = 0;
		}

		$payload = json_encode(array(
			"amount"            => $amount,
			"name"              => $goal_name,
			"tncpw_session"     => $tncpw_session,
			"delay_in_minutes"  => $delay_in_minutes,
			"custom_dimensions" => $custom_dimensions
		));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'api_key:' . $api_key));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		error_log($payload, 3, $pluginlog);

		//$result = curl_exec($ch);

		$args = array(
			'body'        => $payload,
			'timeout'     => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array('Content-Type' => 'application/json', 'api_key' => $api_key),
			'cookies'     => array(),
		);

		$response     = wp_remote_post($url, $args);
		$responseCode = wp_remote_retrieve_response_code($response);
		$result       = wp_remote_retrieve_body($response);

		// Check HTTP status code
		if (empty($response->errors)) {
			switch ($http_code = $responseCode) {
				case 201:  # CREATED
					$message = esc_html(__('TONICPOW CONVERSION TRIGGERED:', TONICPOW)) . " " . $api_key . PHP_EOL;
					break;
				case 401: # UNAUTHORIZED
					$message = esc_html(__('Unable to authenticate. Check your API key. Code:', TONICPOW)) . " $http_code " . $api_key . " " . PHP_EOL;
					break;
				case 422: # Unprocessable Entity
					$message = esc_html(__('Make sure the params are valid. Amount:', TONICPOW)) . " $amount Api Key: " . $api_key . " Name:" . $goal_name . " Session" . $tncpw_session . " S2 " . TONICPOW()->session()["tncpw_session"] . " Delay: " . $delay_in_minutes . " Custom Dimensions: $custom_dimensions" . " " . PHP_EOL;
					break;
				case 0:
					$message = esc_html(__('There was a connection error. Curl returned a status code of 0', TONICPOW)) . PHP_EOL;
					break;
				default:
					$message = esc_html(__('Unable to trigger conversion. This could mean the tncpw_session is not valid or the campaign is otherwise unable to pay out. Code:', TONICPOW)) . " $http_code " . $api_key . " " . var_dump($result) . PHP_EOL;
					break;
			}
			error_log($message, 3, $pluginlog);
		}

		//curl_close($ch);
		return true;
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tonicpow_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tonicpow_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tonicpow-public.js', array('jquery'), $this->version, false);
	}
}
