=== Plugin Name ===
Donate link: https://tonicpow.com
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: MIT
License URI: https://github.com/tonicpow/wordpress-plugin/blob/master/LICENSE

Triggers conversions for when an order or action is completed. Passes TonicPow sessions to metadata field of woocommerce orders for connection to Zapier. Includes a Widget for displaying ads.

== Description ==

See more information at: https://github.com/tonicpow/wordpress-plugin


== Installation ==

1. Upload `tonicpow.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates


== Changelog ==

= 1.0 =
* Trigger conversion
* Basic widget display
* Capture tncpw_session