<?php
/*
Plugin Name: Tapform
Description: Connects and integrates Tapform widget to your wordpress website.
Version: 1.0
Author: matej@tapform.io
Author URI: https://tapform.io/
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Requires at least: 5.2
Requires PHP: 7.2
*/

if ( ! defined( 'ABSPATH' ) ) exit;

function tapform_menu() {
  add_menu_page(
    'Tapform', 
    'Tapform', 
    'manage_options', 
    'tapform-settings', 
    'tapform_settings_page',
    'data:image/svg+xml;base64,' . base64_encode(file_get_contents(__DIR__ . '/tf-menu-icon.svg')) // Add the SVG as a base64-encoded image
  );
}
add_action('admin_menu', 'tapform_menu');

function tapform_settings_page() {
  ?>
  <div class="wrap">
      <h2>Tapform Settings</h2>
      <form method="post" action="options.php">
          <?php
              settings_fields('tapform-settings-group');
              do_settings_sections('tapform-settings');
              submit_button();
          ?>
      </form>
  </div>
  <?php
}

function tapform_register_settings() {
  register_setting('tapform-settings-group', 'tapform_identifier');
  add_settings_section('tapform-settings-section', 'Tapform Form Identifier/Token', 'tapform_section_callback', 'tapform-settings');
  add_settings_field('tapform_identifier_field', 'Tapform Form Identifier', 'tapform_identifier_field_callback', 'tapform-settings', 'tapform-settings-section');
}
add_action('admin_init', 'tapform_register_settings');

function tapform_section_callback() {
  echo '<p style="max-width: 645px;">To add your Tapform widget to your website, please find your Tapform token identifier by logging in to your <strong>Tapform Dashboard -> Settings -> Tapform Form Identifier</strong> and copy-paste it here. <a href="https://dashboard.tapform.io" target="_blank">Click here to go to your Tapform dashboard.</a></p>';
}

function tapform_identifier_field_callback() {
  $token = get_option('tapform_identifier');
  echo '<input name="tapform_identifier" type="text" value="'. esc_textarea($token) .'"></input>';
}

function tapform_widget_content() {
  $token = get_option('tapform_identifier');
  if(strlen($token) > 0){
    $escaped_token = esc_attr($token);
    echo '<script 
      title="Tapform Quiz"
      id="tapform-script"
      referrerpolicy="origin-when-cross-origin"
      src="https://apimvp.tapform.io/api/forms/script?form=' . $escaped_token . '"
      form-identifier="' . $escaped_token . '"
    ></script>';
  }
}

function tapform_widget_insert() {
  add_action('wp_footer', 'tapform_widget_content');
}
add_action('init', 'tapform_widget_insert');
?>