<?php
   /*
   Plugin Name: RFP Ads
   Plugin URI: https://github.com/mrlowe/rfp-ads-wp
   Description: Funding ad generator for First Financial RFP
   Version: 0.2.1
   Author: Literate Programmer, LLC
   Author URI: http://literateprogrammer.com
   License: GPL2
   */

/* Plugin Option Initialization */

register_activation_hook( __FILE__, 'activate' );
function activate(){
    add_option('rfp_ads_show_everywhere', false);
    add_option('rfp_ads_custom_term', false);
    add_option('rfp_ads_custom_days', 0);
    add_option('rfp_ads_custom_destination', '');
}

/* Options Menu */

add_action('admin_init', 'plugin_admin_init');
function plugin_admin_init() {
    add_settings_section('rfp-options-block', 'RFP Ads Settings', 'render_section_description', 'rfp-options-section');

    register_setting( 'rfp-options-group', 'rfp_ads_show_everywhere', 'boolean' );
    add_settings_field('rfp-options-show-everywhere', 'Show on Every Page', 'render_show_everywhere', 'rfp-options-section', 'rfp-options-block');

    register_setting( 'rfp-options-group', 'rfp_ads_custom_term', 'boolean' );
    add_settings_field('rfp-options-custom-term', '24-Month Term', 'render_custom_term', 'rfp-options-section', 'rfp-options-block');

    register_setting( 'rfp-options-group', 'rfp_ads_custom_days', 'integer' );
    add_settings_field('rfp-options-custom-days', 'Show Again After # Days', 'render_custom_days', 'rfp-options-section', 'rfp-options-block');

    register_setting( 'rfp-options-group', 'rfp_ads_custom_destination', 'string' );
    add_settings_field('rfp-options-custom-destination', 'Destination URL', 'render_custom_destination', 'rfp-options-section', 'rfp-options-block');
}

add_action( 'admin_menu', 'load_plugin_menu' );
function load_plugin_menu() {
	add_options_page( 'RFP Ads Options', 'RFP Ads', 'manage_options', 'rfp-ads-options', 'render_options_form' );
}

function render_options_form() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    ?>
    <div>
    <form action="options.php" method="post">
    <?php settings_fields('rfp-options-group'); ?>
    <?php do_settings_sections('rfp-options-section'); ?>

    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form>
    </div>
    <?php
}

function render_section_description() {
    ?>
    <p>
        Use these settings to control how your site displays ads for financing
        from First Financial Bank's Retail Finance Program.
    </p>
    <?php
}

function render_show_everywhere() {
    $option = get_option('rfp_ads_show_everywhere');
    $checked = $option ? 'checked' : '';
    echo "<input name='rfp_ads_show_everywhere' type='checkbox' {$checked} />";
}

function render_custom_term() {
    $option = get_option('rfp_ads_custom_term');
    $checked = $option ? 'checked' : '';
    echo "<input name='rfp_ads_custom_term' type='checkbox' {$checked} />";
}

function render_custom_days() {
    $option = get_option('rfp_ads_custom_days');
    echo "<input name='rfp_ads_custom_days' type='number' min='0' max='365' value='{$option}' />";
}

function render_custom_destination() {
    $option = get_option('rfp_ads_custom_destination');
    echo "<input name='rfp_ads_custom_destination' type='text' size='32' value='{$option}' />";
}


/* Script Insertion */

add_action( 'wp_enqueue_scripts', 'prep_scripts' );
function prep_scripts() {
    $show_everywhere = get_option('rfp_ads_show_everywhere');
    if (is_home() || $show_everywhere) {
        wp_enqueue_script( 'rfpAdvertisements', 'https://cdn.rawgit.com/mrlowe/rfp-ads/master/rfpAdvertisements.js', array('jquery') );
    }
}

add_action( 'wp_head', 'render_defaults');
function render_defaults() {
    $custom_term = get_option('rfp_ads_custom_term') ? 24 : 12;
    $custom_days = get_option('rfp_ads_custom_days');
    $custom_destination = get_option('rfp_ads_custom_destination');
    ?>
    <script>
        jQuery(document).ready( function() {
            jQuery(document).rfpAdvertisements({
                term: <?php echo $custom_term ?>,
                hideDays: <?php echo $custom_days ?>,
                destination: <?php echo "'{$custom_destination}'" ?>
            });
        });
    </script>
    <?php
}

?>
