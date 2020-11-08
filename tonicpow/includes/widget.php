<?php
/*
Plugin Name: TonicPow
Plugin URI: http:/wordpress.tonicpow.com/
Description: This plugin adds a custom widget.
Version: 1.0
Author: Luke Rohenaz
Author URI: http://www.wpexplorer.com/create-widget-plugin-wordpress/
License: GPL2
*/

// The widget class
class TonicPow_Widget extends WP_Widget
{

  // Main constructor
  public function __construct()
  {
    parent::__construct(
      'tonicpow_widget',
      __('TonicPow Widget', 'text_domain'),
      array(
        'customize_selective_refresh' => true,
      )
    );
  }

  // The widget form (for the backend )
  public function form($instance)
  {

    // Set widget defaults
    $defaults = array(
      'title'    => '',
      'address'     => '',
      'rate'     => '',
      'adUnitID'     => '',
      'dimensions' => '',
      // 'textarea' => '',
      // 'checkbox' => '',
      // 'select'   => '',
    );

    // Parse current settings with defaults
    extract(wp_parse_args((array) $instance, $defaults)); ?>

    <?php // Widget Title 
    ?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Widget Title', 'text_domain'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>

    <?php // Address Field 
    ?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('address')); ?>"><?php _e('Payout Address:', 'text_domain'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('address')); ?>" name="<?php echo esc_attr($this->get_field_name('address')); ?>" type="text" value="<?php echo esc_attr($address); ?>" />
    </p>


    <?php // Rate Field 
    ?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('rate')); ?>"><?php _e('Rate:', 'text_domain'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('rate')); ?>" name="<?php echo esc_attr($this->get_field_name('rate')); ?>" type="text" value="<?php echo esc_attr($rate); ?>" />
    </p>


    <?php // Ad Unit ID Field 
    ?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('adUnitID')); ?>"><?php _e('Ad Unit ID:', 'text_domain'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('adUnitID')); ?>" name="<?php echo esc_attr($this->get_field_name('adUnitID')); ?>" type="text" value="<?php echo esc_attr($adUnitID); ?>" />
    </p>

    <!--     
    <?php // Textarea Field 
    ?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('textarea')); ?>"><?php _e('Textarea:', 'text_domain'); ?></label>
      <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('textarea')); ?>" name="<?php echo esc_attr($this->get_field_name('textarea')); ?>"><?php echo wp_kses_post($textarea); ?></textarea>
    </p>

    <?php // Checkbox 
    ?>
    <p>
      <input id="<?php echo esc_attr($this->get_field_id('checkbox')); ?>" name="<?php echo esc_attr($this->get_field_name('checkbox')); ?>" type="checkbox" value="1" <?php checked('1', $checkbox); ?> />
      <label for="<?php echo esc_attr($this->get_field_id('checkbox')); ?>"><?php _e('Checkbox', 'text_domain'); ?></label>
    </p> -->

    <?php // Dropdown 
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('dimensions'); ?>"><?php _e('Dimensions', 'text_domain'); ?></label>
      <select name="<?php echo $this->get_field_name('dimensions'); ?>" id="<?php echo $this->get_field_id('dimensions'); ?>" class="widefat">
        <?php
        // Your options array
        $options = array(
          ''        => __('Dimensions', 'text_domain'),
          'd120x60' => __('120x60', 'text_domain'),
          'd120x600' => __('120x600', 'text_domain'),
          'd125x125' => __('125x125', 'text_domain'),
          'd160x600' => __('160x600', 'text_domain'),
          'd200x200' => __('200x200', 'text_domain'),
          'd240x400' => __('240x400', 'text_domain'),
          'd250x250' => __('250x250', 'text_domain'),
          'd300x250' => __('300x250', 'text_domain'),
          'd300x600' => __('300x600', 'text_domain'),
          'd320x50' => __('320x50', 'text_domain'),
          'd320x100' => __('320x100', 'text_domain'),
          'd336x280' => __('336x280', 'text_domain'),
          'd468x60' => __('468x60', 'text_domain'),
          'd728x90' => __('728x90', 'text_domain'),
          'd970x90' => __('970x90', 'text_domain'),
          'd970x250' => __('970x250', 'text_domain'),
        );

        // Loop through options and add each one to the select dropdown
        foreach ($options as $key => $name) {
          echo '<option value="' . esc_attr($key) . '" id="' . esc_attr($key) . '" ' . selected($dimensions, $key, false) . '>' . $name . '</option>';
        } ?>
      </select>
    </p>

<?php }

  // Update widget settings
  public function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title']    = isset($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : '';
    $instance['address']     = isset($new_instance['address']) ? wp_strip_all_tags($new_instance['address']) : '';
    $instance['rate']     = isset($new_instance['rate']) ? wp_strip_all_tags($new_instance['rate']) : '';
    $instance['adUnitID']     = isset($new_instance['adUnitID']) ? wp_strip_all_tags($new_instance['adUnitID']) : '';
    $instance['dimensions']   = isset($new_instance['dimensions']) ? wp_strip_all_tags($new_instance['dimensions']) : '';

    return $instance;
  }

  // Display the widget
  public function widget($args, $instance)
  {

    extract($args);

    // Check the widget options
    $title    = isset($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
    $address     = isset($instance['address']) ? $instance['address'] : '';
    $rate     = isset($instance['rate']) ? $instance['rate'] : '';
    $adUnitID     = isset($instance['adUnitID']) ? $instance['adUnitID'] : '';
    // $textarea = isset($instance['textarea']) ? $instance['textarea'] : '';
    $dimensions   = isset($instance['dimensions']) ? $instance['dimensions'] : '';
    list($width, $height) = explode('x', $dimensions);
    $width = substr($width, 1);

    // WordPress core before_widget hook (always include )
    echo $before_widget;

    // Display the widget
    echo '<div class="widget-text wp_widget_plugin_box">';

    // Display widget title if defined
    if ($title) {
      echo $before_title . $title . $after_title;
    }

    // TonicPow ad-unit widget
    echo '<div class="tonic" data-address="' . $address . '" data-rate="' . $rate . '" data-unit-id="' . $adUnitID . '" data-width="' . $width . '" data-height="' . $height . '"></div>';


    echo '</div>';

    // WordPress core after_widget hook (always include )
    echo $after_widget;
  }
}

// Register the widget
function register_tonicpow_widget()
{
  register_widget('TonicPow_Widget');
}
add_action('widgets_init', 'register_tonicpow_widget');
