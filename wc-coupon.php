<?php
/*
Plugin Name: WC Coupon shortcode
Description: A plugin that adds a shortcode to display the WooCommerce coupon input field anywhere [coupon-input]
Version: 1.0.5
Author: Bizurk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


if (!defined('ABSPATH')) {
    exit;
}



class eplugins_WCCouponInputShortcode {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'eplugins_enqueue_scripts'));
        add_shortcode('coupon-input', array($this, 'eplugins_coupon_input_shortcode'));        

        add_action('admin_menu', array($this, 'eplugins_add_settings_page'));
        add_action('admin_init', array($this, 'eplugins_register_settings'));

        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'eplugins_add_action_links' ));
    }

    function eplugins_add_action_links ( $actions ) {
      $mylinks = array(
      '<a href="' . admin_url( 'options-general.php?page=wc-coupon-settings' ) . '">Settings</a>',
      );
      return array_merge( $actions, $mylinks );
    }

    // Enqueue necessary JavaScript files with prefix
    public function eplugins_enqueue_scripts() {
		  $plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
      $plugin_version = $plugin_data['Version'];
		  wp_enqueue_style(
        'eplugins-wc-coupon-oop-css', 
        plugins_url('assets/css/coupon-input.css', __FILE__), 
        array(), 
        $plugin_version 
      );
      wp_enqueue_script(
        'eplugins-wc-coupon-oop-js', 
        plugins_url('assets/js/wc-coupon-script.js', __FILE__), 
        array('jquery'), 
        $plugin_version, // Use the retrieved version for cache busting
        true // Load script in footer
      );
       
		  wp_localize_script( 'eplugins-wc-coupon-oop-js', 'eplugins_coupon', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'loading_gif' => plugin_dir_url( __FILE__ ) . 'assets/images/loader.gif', 
        'apply_coupon_nonce' => wp_create_nonce('apply-coupon')
		  ));
		
    }

    // Shortcode function to display coupon input form with prefix
    public function eplugins_coupon_input_shortcode() {
        ob_start(); 
        $placeholder_text = get_option('eplugins_coupon_placeholder_text');
        if (empty($placeholder_text)) {
            $placeholder_text = 'Coupon code';
        }
    ?>
        <div class="coupon">
    			<input type="text" class="input-text" id="eplugins_coupon_code" name="coupon_code" placeholder="<?php echo esc_attr($placeholder_text); ?>">
    			<button class="grve-btn grve-coupon-btn"  id="eplugins_apply_coupon"><?php esc_html_e('Apply Coupon', 'wc-coupon'); ?></button>
    			<div id="eplugins_coupon_message"></div>
    		</div>
        <?php
        return ob_get_clean();
    }

    public function eplugins_add_settings_page() {
        add_options_page(
            'WC Coupon Settings',  // Page title
            'WC Coupon Settings',   // Menu title
            'manage_options',       // Capability
            'wc-coupon-settings',   // Menu slug
            array($this, 'eplugins_render_settings_page') // Callback function
        );
    }

    // Register settings
    public function eplugins_register_settings() {
        register_setting('eplugins_settings_group', 'eplugins_coupon_placeholder_text', array($this, 'eplugins_sanitize_placeholder_text'));

    }

    // Render settings page
    public function eplugins_render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Custom Placeholder Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('eplugins_settings_group');
                do_settings_sections('eplugins_settings_group');
                ?>
                <table class="form-table">
                    <tr>
                      <td colspan="2">Coupon input shortcode: [coupon-input]</td> 
                    </tr>  
                    <tr valign="top">
                        <th scope="row">Placeholder Text</th>
                        <td>
                            <input type="text" name="eplugins_coupon_placeholder_text" value="<?php echo esc_attr(get_option('eplugins_coupon_placeholder_text')); ?>" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    public function eplugins_sanitize_placeholder_text($value) {
      if (empty($value)) {
          delete_option('eplugins_coupon_placeholder_text');
          return '';
      }
      return sanitize_text_field($value);
    }

}


// Initialize the plugin
new eplugins_WCCouponInputShortcode();
