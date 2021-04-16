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
     * Whether to show all the hooks available, or just the curated selecetion
     * This will initialize the select2 library for easier selection
    */
	private $showAllHooks = false;

    private $authErrors = [];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		if (get_option('tonicpow_advanced_settings')) {
		    $this->showAllHooks = true;
		}
	}

	public function init()
	{
		$pluginlog = plugin_dir_path(__FILE__) . 'debug.log';

		TONICPOW()->session("available_goals", array());
	}

	public function isValidJson($strJson)
	{
		json_decode($strJson);

		return (json_last_error() === JSON_ERROR_NONE);
	}

	public function campaignListOutput()
	{
		$campaignListResponse = TONICPOW()->session()["campaign_result"];
		if ($this->isValidJson($campaignListResponse)) {
			$obj       = json_decode($campaignListResponse);
			$campaigns = $obj->{'campaigns'};
			if (empty($campaigns)) {
				$campaigns = [];
			}

			echo '
			<ul>';
			foreach ($campaigns as $campaign) {
				$goals = $campaign->{'goals'};

				if (is_array($goals) || is_object($goals)) {
					if (count(array($goals)) > 0) {
						echo '<li style="border: 1px solid indianred; border-radius: 6px; margin-bottom: .25rem; padding: .25rem;">
					<h4 style="margin: 0;">' . $campaign->{'title'} . '</h4>' .
							$campaign->{'description'} . '
					<br />';

						foreach ($goals as $goal) {
							// Add the goal to the goal selector
							echo '<strong>' . esc_html(__('Goal Name:', TONICPOW)) . ' ' . $goal->{'name'} . '</strong><br />' . PHP_EOL;
							echo ' (' . esc_html(__('rate:', TONICPOW)) . ' ' . $goal->{'payout_rate'} . ' ' . $campaign->{'currency'} . ' ' . esc_html(__('payout type:', TONICPOW)) . ' ' . $goal->{'payout_type'} . ' ' . esc_html(__('max_per_visitor:', TONICPOW)) . ' ' . $goal->{'max_per_visitor'} . ' ' . esc_html(__('max_per_promoter:', TONICPOW)) . ' ' . $goal->{'max_per_promoter'} . ' ' . esc_html(__('payouts:', TONICPOW)) . ' ' . $goal->{'payouts'} . ')<br />' . PHP_EOL;
							if (!empty($goal->{'funding_Address'})) {
								echo '' . esc_html(__('Funding address:', TONICPOW)) . ' ' . $goal->{'funding_Address'} . ' ' . PHP_EOL;
							}
						}
						// todo: fix the url below to be dynamic
						echo '<a target="_blank" href="https://tonicpow.com/app/dashboard/ad-profiles/' . $campaign->{'advertiser_profile_id'} . '/campaigns/' . $campaign->{'id'} . '/goals">' . esc_html(__('Edit', TONICPOW)) . '</a>' . PHP_EOL;
						echo '</li>' . PHP_EOL;
					} else {
						echo '<li style="border: 1px solid indianred; border-radius: 6px; margin-bottom: .25rem; padding: .25rem;"><strong>' . $campaign->{'title'} . '</strong> ' . esc_html(__('has no conversion goals.', TONICPOW)) . ' <a target="_blank" href="https://tonicpow.com/app/dashboard/ad-profiles/' . $campaign->{'advertiser_profile_id'} . '/campaigns/' . $campaign->{'id'} . '/goals">' . esc_html(__('Set one up', TONICPOW)) . '</a></li>' . PHP_EOL;
					}
				}
			}
			echo '</ul>
			<br />' . PHP_EOL;

			// todo: fix the url below to be dynamic
			echo '<span style="color: #333;">' . esc_html(__('Visit', TONICPOW)) . ' <a href="https://tonicpow.com" target="_blank" />' . esc_html(__('TonicPow', TONICPOW)) . '</a> ' . esc_html(__('to create & update campaigns.', TONICPOW)) . '</span>' . PHP_EOL;
		} else {
			echo esc_html(__('Invalid response JSON', TONICPOW));
		}
	}

	public function loggedInOutput()
	{

		$accountResponse = TONICPOW()->session()["account"];
		if ($this->isValidJson($accountResponse)) {
			$this->campaignListOutput();
		} else {
			echo esc_html(__('invalid account json', TONICPOW));
		}
	}

	public function output()
	{ ?>
		<div class="wrap">
			<h2><?= esc_html(__('TonicPow Settings', TONICPOW)) ?></h2>

			<form method="POST" action="options.php">
				<?php
				if (!empty(TONICPOW()->session()["logged_in"]) && TONICPOW()->session()["logged_in"] == true) {
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
		<h1>' . esc_html(__('TonicPow for WooCommerce', TONICPOW)) . '</h1>
		<p>' . esc_html(__('TonicPow session token will be available in the order metadata. You can use this to trigger events from Zapier.', TONICPOW)) . '</p>
		';
	}

	public function section_callback($arguments)
	{
		switch ($arguments['id']) {
			case 'app_section':
				echo esc_html(__('Your TonicPow app configuration', TONICPOW));
				break;
			case 'conversion_section':
                if (!TONICPOW()->session()["logged_in"]) {
                    echo "<b>You are not logged in yet. Fill in a valid API key, save and your goals should be shown</b><br/>";
                    if ($this->authErrors && count($this->authErrors > 0)) {
                        echo "Errors: <br/>";
                        echo " - " . implode($this->authErrors, '<br/>');
                        echo "<br/><br/>";
                    }
                } else {
    				echo esc_html(__('Configure goal conversion events', TONICPOW));
                }
				break;
		}
	}

	public function get_option_value($id, $goal, $goal_variable) {
	    if ($goal && $goal_variable) {
	        $goals = get_option('tonicpow_goals');
	        return @$goals[$goal][$goal_variable];
	    }

	    return get_option($id);
	}

	public function field_callback($arguments)
	{
		$value = $this->get_option_value($arguments['uid'], $arguments['goal'], $arguments['goal_variable']); // Get the current value, if there is one
		if (!$value) { // If no value exists
			$value = $arguments['default']; // Set to our default
		}

		// Check which type of field we want
		switch ($arguments['type']) {
			case 'select': // If it is a text field

				if (!empty($arguments['options']) && is_array($arguments['options'])) {
					$attributes     = '';
					$options_markup = '';
					foreach ($arguments['options'] as $key => $label) {
						$options_markup .= sprintf('<option value="%s" %s>%s</option>', $key, selected($value, $key, false), $label);
					}
					if ($arguments['type'] === 'multiselect') {
						$attributes = ' multiple="multiple" ';
					}
					printf('<select name="%1$s" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup);
				}
				break;
			case 'text': // If it is a text field
				printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value);
				break;
			case 'checkbox': // If it is a checkbox field
				printf('<input name="%1$s" id="%1$s" type="checkbox" value="1" %2$s />', $arguments['uid'], ($value ? 'checked' : ''));
				break;
            case 'fieldset':
				printf('<fieldset>');
				foreach($arguments['fields'] as $field) {
    				printf('<div class="fieldset_field">');
				    if ($field['label']) {
				        printf('<div class="sub-field-label">%1$s</div>', $field['label']);
				    }
				    $this->field_callback($field);
    				printf('</div>');
				}
				printf('<input type="button" name="test" class="button button-secondary" value="Test goal trigger" id="trigger_' . $arguments['goal'] . '" onClick="tonicpow_test_trigger(\'' . $arguments['goal'] . '\');">');
				printf('</fieldset>');
				break;
		}

		// If there is help text
		if ($helper = $arguments['helper']) {
			printf('<span class="helper"> %s</span>', $helper); // Show it
		}

		// If there is supplemental text
		if ($supplemental = $arguments['supplemental']) {
			printf('<p class="description">%s</p>', $supplemental); // Show it
		}
	}


	public function auth()
	{
		$api_key      = get_option("tonicpow_api_key");
		$base_api_url = get_option("tonicpow_base_api_url") ?: 'https://api.tonicpow.com/v1/';

		if (strlen($api_key) === 32) {

			// CHECK THE KEY
			$url = $base_api_url . "users/account";

			$args          = array(
				'headers' => array('Content-Type' => 'application/json', 'api_key' => $api_key)
			);
			$response      = wp_remote_get($url, $args);
			$responseCode  = wp_remote_retrieve_response_code($response);
			$accountResult = wp_remote_retrieve_body($response);

			$account = json_decode($accountResult);

			TONICPOW()->session('logged_in', false);

			// Check HTTP status code
			if (empty($response->errors)) {
				switch ($http_code = $responseCode) {
					case 200:  # CREATED
						TONICPOW()->session("logged_in", true);
						TONICPOW()->session("account", $accountResult);
						$this->authErrors = [];
						break;
					case 401:
						TONICPOW()->session("logged_in", false);
						TONICPOW()->session("account", false);
						//echo esc_html(__("You are not logged in.", TONICPOW));
						$this->authErrors[] = "You are not logged in.";
						break;
					default:
						//echo '' . esc_html(__('Unexpected HTTP code:', TONICPOW)) . ' ', $http_code, "\n<br />\n";
						$this->authErrors[] = "Unexpected HTTP code: " . $http_code;
				}
			}

			if (TONICPOW()->session()["logged_in"] == true) {
				foreach ($account->{'advertiser_profiles'} as $advertiserProfile) {
					// Get advertiser campaign list
					// Ex. advertiser_campaigns?current_page=null&results_per_page=20&sort_by=created_at&direction=desc
					$url = $base_api_url . 'advertisers/campaigns/' . $advertiserProfile->{'id'};

					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						'Content-Type:application/json',
						'api_key:' . $api_key
					));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

					//$campaignResult = curl_exec($ch);

					$args = array(
						'headers' => array('Content-Type' => 'application/json', 'api_key' => $api_key)
					);

					$response       = wp_remote_get($url, $args);
					$responseCode   = wp_remote_retrieve_response_code($response);
					$campaignResult = wp_remote_retrieve_body($response);

					// Check HTTP status code
					if (empty($response->errors)) {
						switch ($http_code = $responseCode) {
							case 200:  # OK
								# Print response.
								TONICPOW()->session("campaign_result", $campaignResult);

								if ($this->isValidJson($campaignResult)) {
									$obj = json_decode($campaignResult);

									$campaigns = $obj->{'campaigns'};
									if (empty($campaigns)) {
										$campaigns = [];
									}

									// Check if $myList is indeed an array or an object.
									if (is_array($campaigns) || is_object($campaigns)) {
										foreach ($campaigns as $campaign) {
											if (count(array($campaign->{'goals'})) > 0) {
												$goals = $campaign->{'goals'};
												if (is_array($goals) || is_object($goals)) {
													foreach ($goals as $goal) {
														// Add the goal to the goal selector
														if (!in_array($goal->{'name'}, TONICPOW()->session()["available_goals"])) {
															$goals = TONICPOW()->session()["available_goals"];
															array_push($goals, $goal->{'name'});
															TONICPOW()->session("available_goals", $goals);
														}
													}
												}
											}
										}
									}
								}
								break;
							default:
								TONICPOW()->session("campaign_result", false);
						}
					}
					//curl_close($ch);
				}
			}
		}
	}

	public function setup_fields()
	{
		$this->auth();

		global $wp_filter;

        $curatedHooks = [
            "woocommerce_payment_complete",
            "woocommerce_add_to_cart",
            "wp_login",
            "media_upload_image",
            "media_upload_file",
            "media_upload_video",
            "comment_approved_comment",
            "comment_post"
        ];

		$toc = [
		    '' => '',
		    'wp_load' => 'wp_load'
		];
		foreach ($wp_filter as $key => $val) {
			if ($this->showAllHooks || in_array($key, $curatedHooks)) {
				$toc[$key] = $key;
			}
		}
		ksort($toc);

		$goals = TONICPOW()->session()["available_goals"];

		$fields = array(
			array(
				'uid'          => 'tonicpow_api_key',
				'label'        => esc_html(__('API Key', TONICPOW)),
				'section'      => 'app_section',
				'type'         => 'text',
				'options'      => false,
				'placeholder'  => esc_html(__('placeholder', TONICPOW)),
				'helper'       => esc_html(__('Obtained from TonicPow', TONICPOW)),
				'supplemental' => '',
				'default'      => ''
			),
			array(
				'uid'          => 'tonicpow_advanced_settings',
				'label'        => esc_html(__('Advanced settings', TONICPOW)),
				'section'      => 'app_section',
				'type'         => 'checkbox',
				'helper'       => esc_html(__('Check this to show advanced settings and all hooks', TONICPOW)),
                'supplemental' => esc_html(__('You need to save before the changes are visible.', TONICPOW)),
				'default'      => false
			),
		);

        if (get_option('tonicpow_advanced_settings')) {
        	$fields[] = array(
                'uid'          => 'tonicpow_base_api_url',
                'label'        => esc_html(__('API URL', TONICPOW)),
                'section'      => 'app_section',
                'type'         => 'text',
                'options'      => false,
                'placeholder'  => esc_html(__('placeholder', TONICPOW)),
                'helper'       => esc_html(__('Including version and trailing slash', TONICPOW)),
                'supplemental' => esc_html(__('The base url for all tonicpow API calls.', TONICPOW)),
                'default'      => 'https://api.tonicpow.com/v1/'
            );
            $error_log_location = ini_get('error_log');
        	$fields[] = array(
                'uid'          => 'tncpw_debug',
                'label'        => esc_html(__('Debugging on', TONICPOW)),
                'section'      => 'app_section',
                'type'         => 'checkbox',
                'helper'       => esc_html(__('Check this to enable debugging on tonic pow requests', TONICPOW)),
                'supplemental' => esc_html(__("Debug information is written to the error_log at $error_log_location.", TONICPOW)),
                'default'      => false
            );
        }

        foreach($goals as $goal) {
            $goalId = preg_replace("/[^A-Za-z0-9-]/", '_', $goal);
            $fields[] = array(
                'uid'          => 'tonicpow_goal_' . $goalId,
                'label'        => esc_html(__('Goal: ' . $goal, TONICPOW)),
                'section'      => 'conversion_section',
                'goal'         => $goal,
                'type' => 'fieldset',
                'fields' => array(
                    array(
                        'uid'          => 'tonicpow_goals[' . $goal . '][hook_name]',
                        'goal'         => $goal,
                        'goal_variable'=> 'hook_name',
                        'label'        => esc_html(__('Hook Name', TONICPOW)),
                        'section'      => 'conversion_section',
                        'type'         => 'select',
                        'options'      => $toc,
                        'placeholder'  => esc_html(__('order', TONICPOW)),
                        'helper'       => esc_html(__('Triggers a conversion', TONICPOW)),
                        'supplemental' => esc_html(__('Contact us to request support for new hooks.', TONICPOW)),
                        'default'      => array(0 => 'woocommerce_payment_complete')
                    ),
                    array(
                        'uid'          => 'tonicpow_goals[' . $goal . '][delay_in_minutes]',
                        'goal'         => $goal,
                        'goal_variable'=> 'delay_in_minutes',
                        'label'        => esc_html(__('Delay', TONICPOW)),
                        'section'      => 'conversion_section',
                        'type'         => 'text',
                        'options'      => false,
                        'placeholder'  => esc_html(__('0', TONICPOW)),
                        'helper'       => esc_html(__('in minutes', TONICPOW)),
                        'supplemental' => esc_html(__('How long to wait before triggering the conversion payout.', TONICPOW)),
                        'default'      => '0'
                    ),
                    array(
                        'uid'          => 'tonicpow_goals[' . $goal . '][custom_dimensions]',
                        'goal'         => $goal,
                        'goal_variable'=> 'custom_dimensions',
                        'label'        => esc_html(__('Custom Dimensions', TONICPOW)),
                        'section'      => 'conversion_section',
                        'type'         => 'text',
                        'options'      => false,
                        'placeholder'  => esc_html(__('', TONICPOW)),
                        'helper'       => esc_html(__('Optional', TONICPOW)),
                        'supplemental' => esc_html(__('Custom data for analytics', TONICPOW)),
                        'default'      => ''
                    )
                )
            );
        }

		foreach ($fields as $field) {
			$pluginlog = plugin_dir_path(__FILE__) . 'debug.log';

			add_settings_field($field['uid'], $field['label'], array(
				$this,
				'field_callback'
			), 'tonicpow_settings', $field['section'], $field);

			if (strpos($field['uid'], '[') === false) {
    			register_setting('tonicpow_settings', $field['uid']);
			}
		}
        register_setting('tonicpow_settings', 'tonicpow_goals');
	}

	public function setup_sections()
	{
		add_settings_section('app_section', 'Application', array($this, 'section_callback'), 'tonicpow_settings');
		add_settings_section('conversion_section', 'Conversions', array(
			$this,
			'section_callback'
		), 'tonicpow_settings');
	}

	/**
	 * Add "TonicPow" to the WooCommerce Dashboard menu.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function admin_menu()
	{

		// Add the options menu item and page
		$menu_title = esc_html(__('TonicPow', TONICPOW));
		$page_title = esc_html(__('TonicPow Settings', TONICPOW));
		$capability = 'manage_options';
		$slug       = 'tonicpow';
		$callback   = array($this, 'output');
		$icon_url   = '/wp-content/plugins/tonicpow/assets/icon-20.png';
		add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon_url);
	}

	public function test_goal_trigger()
	{
	    $goal              = $_POST['goal'];
	    $hook_name         = $_POST['hook_name'];
	    $delay_in_minutes  = $_POST['delay_in_minutes'] ?: 0;
	    $custom_dimensions = $_POST['custom_dimensions'] ?: '';

	    if (!$goal) {
            wp_send_json( Array(
                'error' => 'No goal selected',
                'data' => $_POST
            ) );
            wp_die(); // All ajax handlers die when finished
	    }

	    if (!$hook_name) {
            wp_send_json( Array(
                'error' => 'No hook selected',
                'data' => $_POST
            ) );
            wp_die(); // All ajax handlers die when finished
	    }

		$api_key       = get_option("tonicpow_api_key");
		$tncpw_session = 'test_goal_trigger-00000000000000';

		// Trigger tonicpow conversion
		$base_api_url = get_option("tonicpow_base_api_url") ?: 'https://api.tonicpow.com/v1/';
		$url          = $base_api_url . "conversions";

		$payload = json_encode(array(
			"name"              => $goal,
			"tncpw_session"     => $tncpw_session,
			"delay_in_minutes"  => $delay_in_minutes,
			"custom_dimensions" => $custom_dimensions,
			"amount"            => 0
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

	    $response = [];

        $post_response = wp_remote_post($url, $args);
        if (is_wp_error($post_response)) {
            $message = esc_html(__('HTTP ERROR: ' . $post_response->get_error_message())) . PHP_EOL;

            $response['error'] = $message;
            $response['data'] = $_POST;
        } else {
            $http_code = wp_remote_retrieve_response_code($post_response);
            $result    = wp_remote_retrieve_body($post_response);
            $resultObj = null;
    		if ($this->isValidJson($result)) {
			    $resultObj = json_decode($result);
            }

            if ($http_code === 200 || $http_code === 201) {
                $response['message'] = 'Your hook is working correctly';
            } else {
                $response['error'] = (is_object($resultObj) ? $resultObj->message  : 'An unknown error occurred' ) . ' (' . $http_code . ')';
                $response['data'] = $resultObj ?: $result;
            }
        }

        wp_send_json( $response );
        wp_die(); // All ajax handlers die when finished
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
        if ($this->showAllHooks) {
            wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
        }
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

        if ($this->showAllHooks) {
        	wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );
        }
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/tonicpow-admin.js', array('jquery'), $this->version, true);
	}
}
