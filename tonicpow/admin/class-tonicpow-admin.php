<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://tonicpow.com
 * @since      1.0.0
 *
 * @package    Tonicpow
 * @subpackage Tonicpow/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tonicpow
 * @subpackage Tonicpow/admin
 * @author     TonicPow <support@tonicpow.com>
 */
class Tonicpow_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function init()
	{

		$pluginlog = plugin_dir_path(__FILE__) . 'debug.log';

		// if (!isset($_SESSION["tncpw_cookie"])) {
		// 	// CREATE A SESSION
		// 	$api_key = get_option("tonicpow_api_key");
		// 	$base_api_url = get_option("tonicpow_base_api_url");

		// 	$url = $base_api_url . "auth/session";
		// 	$ch = curl_init($url);
		// 	$payload = json_encode(array("api_key" => $api_key));
		// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		// 	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// 	curl_setopt($ch, CURLOPT_HEADER, 1);
		// 	$result = curl_exec($ch);

		// 	// get cookie
		// 	// multi-cookie variant contributed by @Combuster in comments
		// 	preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
		// 	$cookies = array();
		// 	foreach ($matches[1] as $item) {
		// 		parse_str($item, $cookie);
		// 		$cookies = array_merge($cookies, $cookie);
		// 	}


		// 	// Check HTTP status code
		// 	if (!curl_errno($ch)) {
		// 		switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
		// 			case 201:  # CREATED
		// 				$message = "WC_TONICPOW_SESSION_CREATED: " . $cookies["session_token"] . PHP_EOL;
		// 				$_SESSION["tncpw_cookie"] = $cookies["session_token"];
		// 				break;
		// 			default:
		// 				$message = "Unable to authenticate. Check your API key. Code: $http_code " . PHP_EOL;
		// 				break;
		// 		}
		// 		error_log($message, 3, $pluginlog);
		// 	}

		// 	curl_close($ch);
		// }


		$_SESSION["available_goals"] = array();

		if (isset($_GET["tncpw_session"])) {
			$_SESSION["tncpw_session"] = $_GET["tncpw_session"];
			// Set Woo Commerce session
			// TODO: Check woo commerce is installed (make plugin a requirement?)
			WC()->session->set('tncpw_session', $_SESSION["tncpw_session"]);
			$message = "WP_LOADED_TONICPOW" . $_SERVER['REQUEST_URI'] . " - TNCPW_SESSION: " . $_SESSION["tncpw_session"] . PHP_EOL;
			error_log($message, 3, $pluginlog);
		}
	}

	public function isValidJson($strJson)
	{
		json_decode($strJson);
		return (json_last_error() === JSON_ERROR_NONE);
	}

	public function campaignListOutput()
	{
		$campaignListResponse = $_SESSION["campaign_result"];
		if ($this->isValidJson($campaignListResponse)) {
			$obj = json_decode($campaignListResponse);
			$campaigns = $obj->{'campaigns'};
			echo '
			<ul>';
			foreach ($campaigns as $campaign) {
				if (count($campaign->{'goals'}) > 0) {
					echo '<li style="border: 1px solid indianred; border-radius: 6px; margin-bottom: .25rem; padding: .25rem;">
					<h4 style="margin: 0;">' . $campaign->{'title'} . '</h4>' .
						$campaign->{'description'} . '
					<br />';

					foreach ($campaign->{'goals'} as $goal) {
						// Add the goal to the goal selector
						echo '<strong>Goal Name: ' . $goal->{'name'} . '</strong><br />';
						echo ' (rate: ' . $goal->{'payout_rate'} . ' ' . $campaign->{'currency'} . ' payout type: ' . $goal->{'payout_type'} . ' max_per_visitor: ' . $goal->{'max_per_visitor'} . ' max_per_promoter: ' . $goal->{'max_per_promoter'} . ' payouts: ' .  $goal->{'payouts'} . ')';
					}

					echo '</li>';
				} else {
					echo '<li style="border: 1px solid indianred; border-radius: 6px; margin-bottom: .25rem; padding: .25rem;"><strong>' . $campaign->{'title'} . '</strong> has no conversion goals. <a target="_blank" href="https://offers.tonicpow.com/create_campaign/' . $campaign->{'id'} . '">Set one up</a></li>';
				}
			}
			echo '</ul>
			<br />';

			echo '<span style="color: #333;">Visit <a href="https://tonicpow.com" target="_blank" />TonicPow</a> to create & update campaigns.</span>';
		} else {
			echo 'Invalid response JSON';
		}
	}

	public function loggedInOutput()
	{

		$accountResponse = $_SESSION["account"];
		if ($this->isValidJson($accountResponse)) {
			$this->campaignListOutput();
		} else {
			echo 'invalid account json';
		}
	}


	// public function dynamicHook( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
	// {
	// 	$pluginlog = plugin_dir_path(__FILE__) . 'debug.log';
	// 	$message = "DYNAMIC HOOK! $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data " . PHP_EOL;
	// 	error_log($message, 3, $pluginlog);
	// 	echo 'Dynamic hook!' . $cart_item_key;
	// }

	public function output()
	{ ?>
		<div class="wrap">
			<h2>TonicPow Settings</h2>

			<form method="POST" action="options.php">
				<?php
				if ($_SESSION["logged_in"] == true) {
					$this->loggedInOutput();
				}
				settings_fields('tonicpow_settings');
				do_settings_sections('tonicpow_settings');
				submit_button();
				?>
			</form>
		</div>
<?php }

	public function wc_output()
	{
		echo '
		<h1>TonicPow for WooCommerce</h1>
		<p>TonicPow session token will be available in the order metadata. You can use this to trigger events from Zapier.</p>
		';
	}

	public function section_callback($arguments)
	{
		switch ($arguments['id']) {
			case 'app_section':
				echo 'Your TonicPow app configuration';
				break;
			case 'conversion_section':
				echo 'Configure goal conversion events';
				break;
		}
	}

	public function field_callback($arguments)
	{
		$value = get_option($arguments['uid']); // Get the current value, if there is one
		if (!$value) { // If no value exists
			$value = $arguments['default']; // Set to our default
		}

		// Check which type of field we want
		switch ($arguments['type']) {
			case 'select': // If it is a text field

				if (!empty($arguments['options']) && is_array($arguments['options'])) {
					$attributes = '';
					$options_markup = '';
					foreach ($arguments['options'] as $key => $label) {
						$options_markup .= sprintf('<option value="%s" %s>%s</option>', $key, selected($value[array_search($key, $value, true)], $key, false), $label);
					}
					if ($arguments['type'] === 'multiselect') {
						$attributes = ' multiple="multiple" ';
					}
					printf('<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup);
				}
				break;
			case 'text': // If it is a text field
				printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value);
				break;
		}

		// If there is help text
		if ($helper = $arguments['helper']) {
			printf('<span class="helper"> %s</span>', $helper); // Show it
		}

		// If there is supplemental text
		if ($supplimental = $arguments['supplemental']) {
			printf('<p class="description">%s</p>', $supplimental); // Show it
		}
	}


	public function auth()
	{
		$api_key = get_option("tonicpow_api_key");
		$base_api_url = get_option("tonicpow_base_api_url");

		if (strlen($api_key) === 32) {

			// CHECK THE KEY
			$url = $base_api_url . "users/account";

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'api_key:' . $api_key));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$accountResult = curl_exec($ch);
			$account = json_decode($accountResult);

			$_SESSION["logged_in"] = false;

			// Check HTTP status code
			if (!curl_errno($ch)) {
				switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
					case 200:  # CREATED
						# Print response.
						$_SESSION["logged_in"] = true;
						$_SESSION["account"] = $accountResult;
						break;
					case 401:
						$_SESSION["logged_in"] = false;
						$_SESSION["account"] = false;
						echo "You are not logged in.";
						break;
					default:
						echo 'Unexpected HTTP code: ', $http_code, "\n<br />\n";
				}
			}
			curl_close($ch);


			if ($_SESSION["logged_in"] == true) {
				foreach ($account->{'advertiser_profiles'} as $advertiserProfile) {
					// Get advertiser campaign list
					// Ex. advertiser_campaigns?current_page=null&results_per_page=20&sort_by=created_at&direction=desc
					$url = $base_api_url . 'advertisers/campaigns/' . $advertiserProfile->{'id'};

					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',  'api_key:' . $api_key));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$campaignResult = curl_exec($ch);

					// Check HTTP status code
					if (!curl_errno($ch)) {
						switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
							case 200:  # OK
								# Print response.
								$_SESSION["campaign_result"] = $campaignResult;

								if ($this->isValidJson($campaignResult)) {
									$obj = json_decode($campaignResult);

									$campaigns = $obj->{'campaigns'};

									foreach ($campaigns as $campaign) {
										if (count($campaign->{'goals'}) > 0) {
											foreach ($campaign->{'goals'} as $goal) {
												// Add the goal to the goal selector
												if (!in_array($goal->{'name'}, $_SESSION["available_goals"])) {
													array_push($_SESSION["available_goals"], $goal->{'name'});
												}
											}
										}
									}
								}
								break;
							default:
								$_SESSION["campaign_result"] = false;
						}
					}
					curl_close($ch);
				}
			}
		}
	}

	public function setup_fields()
	{
		$this->auth();

		global $wp_filter;
		// $comment_filters = array();
		// foreach ($wp_filter as $key => $val) {
		// 	if (FALSE !== strpos($key, 'comment')) {
		// 		$comment_filters[$key][] = var_export($val, TRUE);
		// 	}
		// }
		$toc = [];
		foreach ($wp_filter as $key => $val) {
			// $out .= "<h2 id=$name>$name</h2><pre>" . implode("\n\n", $arr_vals) . '</pre>';
			if ($key == "woocommerce_payment_complete" || $key == "woocommerce_add_to_cart" || $key == "wp_login" || $key == "media_upoload_image" || $key == "media_upload_file" || $key == "media_upload_video" || $key == "comment_approved_comment" || $key == "comment_post") {
				$toc[$key] = $key;
			}
		}

		ksort($toc);

		$goals = $_SESSION["available_goals"];

		$fields = array(
			array(
				'uid' => 'tonicpow_api_key',
				'label' => 'API Key',
				'section' => 'app_section',
				'type' => 'text',
				'options' => false,
				'placeholder' => 'placeholder',
				'helper' => 'Obtained from ToncPow',
				'supplemental' => '',
				'default' => ''
			),
			array(
				'uid' => 'tonicpow_base_api_url',
				'label' => 'API URL',
				'section' => 'app_section',
				'type' => 'text',
				'options' => false,
				'placeholder' => 'placeholder',
				'helper' => 'Including version and trailing slash',
				'supplemental' => 'The base url for all tonicpow API calls.',
				'default' => 'https://api.staging.tonicpow.com/v1/'
			),
			array(
				'uid' => 'tonicpow_goal_name',
				'label' => 'Goal Name',
				'section' => 'conversion_section',
				'type' => 'select',
				'options' => $goals,
				'placeholder' => 'order',
				'helper' => '',
				'supplemental' => 'Which goal should be converted',
				'default' => array(0 => 'none')
			),
			array(
				'uid' => 'tonicpow_hook_name',
				'label' => 'Hook Name',
				'section' => 'conversion_section',
				'type' => 'select',
				'options' => $toc,
				'placeholder' => 'order',
				'helper' => 'Triggers a conversion',
				'supplemental' => 'Contact us to request support for new hooks.',
				'default' => array(0 => 'woocommerce_payment_complete')
			),
			array(
				'uid' => 'tonicpow_delay',
				'label' => 'Delay',
				'section' => 'conversion_section',
				'type' => 'text',
				'options' => false,
				'placeholder' => '0',
				'helper' => 'in minutes',
				'supplemental' => 'How long to wait before triggering the conversion payout.',
				'default' => '0'
			),
			array(
				'uid' => 'tonicpow_custom_dimensions',
				'label' => 'Custom Dimensions',
				'section' => 'conversion_section',
				'type' => 'text',
				'options' => false,
				'placeholder' => '',
				'helper' => 'Optional',
				'supplemental' => 'Custom data for analytics',
				'default' => ''
			)
		);

		foreach ($fields as $field) {
			$pluginlog = plugin_dir_path(__FILE__) . 'debug.log';
			$message = "FIELD " . $field['section'] . PHP_EOL;
			error_log($message, 3, $pluginlog);
			add_settings_field($field['uid'], $field['label'], array($this, 'field_callback'), 'tonicpow_settings', $field['section'], $field);
			register_setting('tonicpow_settings', $field['uid']);
		}
	}

	public function setup_sections()
	{
		add_settings_section('app_section', 'Application', array($this, 'section_callback'), 'tonicpow_settings');
		add_settings_section('conversion_section', 'Conversions', array($this, 'section_callback'), 'tonicpow_settings');
	}

	/**
	 * Add "TonicPow" to the WooCommerce Dashboard menu.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_menu()
	{

		// Add the options menu item and page
		$menu_title = 'TonicPow';
		$page_title = 'TonicPow Settings';
		$capability = 'manage_options';
		$slug = 'tonicpow';
		$callback = array($this, 'output');
		add_submenu_page('options-general.php', $page_title, $menu_title, $capability, $slug, $callback);

		// Woocommerce submenu and page
		$wc_callback = array($this, 'wc_output');
		$wc_capability = 'manage_woocommerce';
		$wc_menu_title = 'TonicPow';
		$wc_page_title = 'WooCommerce TonicPow';
		$wc_parent = 'woocommerce';
		$wc_slug = "wc_tonicpow";

		add_submenu_page($wc_parent, $wc_page_title, $wc_menu_title, $wc_capability, $wc_slug, $wc_callback);
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/tonicpow-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tonicpow-admin.js', array('jquery'), $this->version, false);
	}
}
