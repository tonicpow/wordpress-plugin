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
	    if (is_admin()) {
	        return false;
	    }

        $user_id = get_current_user_id();
        $tncpw_session = null;

        if (array_key_exists("tncpw_session", $_GET) || array_key_exists('tncpw_session', $_COOKIE)) {
            if (array_key_exists("tncpw_session", $_GET)) {
                $tncpw_session = sanitize_text_field($_GET["tncpw_session"]);
            } else {
                $tncpw_session = sanitize_text_field($_COOKIE['tncpw_session']);
            }

			TONICPOW()->session("tncpw_session", $tncpw_session);

			if ($user_id) {
                update_user_meta(
                    $user_id,
                    'tncpw_session',
                    $tncpw_session
                );
			}
		} elseif ($user_id) {
            $tncpw_session = get_user_meta($user_id, 'tncpw_session', true);
            if ($tncpw_session) {
                TONICPOW()->session("tncpw_session", $tncpw_session);
            }
		}

		if (!$tncpw_session && $_SESSION && array_key_exists("tncpw_session", $_SESSION)) {
		    $tncpw_session = $_SESSION['tncpw_session'];
		    TONICPOW()->session("tncpw_session", $tncpw_session);
		}

        $_SESSION['tncpw_session'] = $tncpw_session;
        if (class_exists( 'WooCommerce' ) && WC()->session) {
            WC()->session->set( 'tncpw_session', $tncpw_session);
        }
	}

	public function woocommerce_checkout_create_order($order) {
        if (class_exists( 'WooCommerce' ) && WC()->session) {
            $tncpw_session = WC()->session->get( 'tncpw_session' ) ?: TONICPOW()->session()["tncpw_session"];

            if ( $tncpw_session ) {
                // add the tonicpow session id to the order
                // this allows triggers in the admin to process the tonicpow stuff properly
                $order->update_meta_data( 'tncpw_session', $tncpw_session );
            }
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
	public function dynamicHook()
	{
	    if (is_admin()) {
	        // do not trigger anything when on admin pages
	        return;
	    }

        $args = func_get_args();

		$trace  = debug_backtrace();
		$caller = $trace[3];
		$actionName = $caller['args'][0];
		$callerTrace  = $trace[4];
		$callerTrace2 = $trace[2];

        $goals = get_option('tonicpow_goals');

		// Get the tncpw_session
		$tncpw_session = TONICPOW()->session()["tncpw_session"] ?: '';
        if (!$tncpw_session && class_exists( 'WooCommerce' ) && WC()->session) {
            $tncpw_session = WC()->session->get( 'tncpw_session' );
        }

        if (TONICPOW_DEBUG === true) {
			error_log("TONICPOW - Dynamic hook called from $actionName on session $tncpw_session");
        }

        foreach($goals as $goal_id => $goal) {
            if ($goal['hook_name'] && $goal['hook_name'] === $actionName) {
                $delay_in_minutes  = $goal["delay_in_minutes"];
                $custom_dimensions = $goal["custom_dimensions"];

                switch ($actionName) {
                    case 'woocommerce_payment_complete':
                        list($order_id) = $args; // first argument is order_id

                        $order    = wc_get_order($order_id);
                        if (!$tncpw_session && class_exists( 'WooCommerce' ) && $order) {
                            $tncpw_session = $order->get_meta( 'tncpw_session' );
                        }

                        return $this->trigger_conversion($tncpw_session, $actionName, $goal_id, $delay_in_minutes, $custom_dimensions, $order->get_total());
                        break;
                    case 'woocommerce_checkout_create_order':
                        list($order, $data) = $args; // first argument is order

                        if (!$tncpw_session && class_exists( 'WooCommerce' ) && $order) {
                            $tncpw_session = $order->get_meta( 'tncpw_session' );
                        }

                        return $this->trigger_conversion($tncpw_session, $actionName, $goal_id, $delay_in_minutes, $custom_dimensions, $order->get_total());
                        break;
                    case 'woocommerce_add_to_cart':
                        list($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) = $args;

                        $cartItem       = WC()->cart->get_cart_item($cart_item_key);
                        $cartItemPrice	= $cartItem->get_price();
                        $amount         = $cartItemPrice * $quantity;

                         return $this->trigger_conversion($tncpw_session, $actionName, $goal_id, $delay_in_minutes, $custom_dimensions, $amount);
                        break;
                    default:
                        return $this->trigger_conversion($tncpw_session, $actionName, $goal_id, $delay_in_minutes, $custom_dimensions, 0);
                }
            }
        }
	}

	public function trigger_conversion($tncpw_session, $actionName, $goal_id, $delay_in_minutes, $custom_dimensions, $amount = 0)
	{
        if (!$tncpw_session) {
            error_log("TONICPOW - Trigger hook called on empty session for $actionName on $goal_id");
            return false;
        }

		// Set up log
		$api_key   = get_option("tonicpow_api_key");

		// Trigger tonicpow conversion
		$base_api_url = get_option("tonicpow_base_api_url") ?: 'https://api.tonicpow.com/v1/';
		$url          = $base_api_url . "conversions";

		$payload = json_encode(array(
			"name"              => $goal_id,
			"tncpw_session"     => $tncpw_session,
			"delay_in_minutes"  => $delay_in_minutes ?: 0,
			"custom_dimensions" => $custom_dimensions,
			"amount"            => $amount ?: 0
		));

		$args = array(
			'body'        => $payload,
			'timeout'     => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array('Content-Type' => 'application/json', 'api_key' => $api_key),
			'cookies'     => array(),
		);

        if (TONICPOW_DEBUG === true) {
			error_log("TONICPOW - Doing TonicPow request to $url with " . json_encode($args));
        }
		$response     = wp_remote_post($url, $args);

		if (is_wp_error($response)) {
            $message = 'HTTP ERROR: ' . $response->get_error_message();
			error_log("TONICPOW - $message");
		} else {
    		$http_code = wp_remote_retrieve_response_code($response);
	    	$result       = wp_remote_retrieve_body($response);
		    switch ($http_code) {
				case 201:  # CREATED
					// don't log successes
					break;
				case 401: # UNAUTHORIZED
					$message = "Unable to authenticate. Check your API key. Code: $http_code - Api Key:  $api_key";
					break;
				case 422: # Unprocessable Entity
					$message = "Make sure the params are valid. Amount: $amount - Api Key: $api_key - Name: $goal_id - Session: $tncpw_session - Delay: $delay_in_minutes - Custom Dimensions: $custom_dimensions";
					break;
				case 0:
					$message = "There was a connection error. wp_remote_post returned a status code of 0";
					break;
				default:
					$message = "Unable to trigger conversion. This could mean the tncpw_session is not valid or the campaign is otherwise unable to pay out. Code: $http_code - Api Key: $api_key - " . var_dump($result);
					break;
			}

			if ($message) {
			    error_log("TONICPOW - $message");
			}
		}

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
