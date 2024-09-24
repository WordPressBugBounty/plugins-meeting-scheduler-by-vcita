<?php
/**
 *  Add the vCita widget to the "Settings" Side Menu
 */
function wpshd_vcita_admin_actions()
{
  if (function_exists('add_menu_page')) {
    add_menu_page(
      __(WPSHD_VCITA_WIDGET_MENU_NAME, WPSHD_VCITA_WIDGET_MENU_NAME),
      __(WPSHD_VCITA_WIDGET_MENU_NAME, WPSHD_VCITA_WIDGET_SHORTCODE),
      'edit_posts',
      __FILE__,
      'wpshd_vcita_settings_menu',
      plugins_url(WPSHD_VCITA_WIDGET_UNIQUE_ID . '/images/settings.png'));
  }

  add_submenu_page(__FILE__, __('Add to Site', 'meeting-scheduler-by-vcita'), __('Add to Site', 'meeting-scheduler-by-vcita'), 'edit_posts', get_admin_url('','','admin').'admin.php?page='.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/vcita-settings-functions.php&tab=vcita-add-to-site');
  add_submenu_page(__FILE__, __('Custom Implemetation', 'meeting-scheduler-by-vcita'), __('Custom Implemetation', 'meeting-scheduler-by-vcita'), 'edit_posts', get_admin_url('','','admin').'admin.php?page='.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/vcita-settings-functions.php&tab=vcita-custom-impl');
  add_submenu_page(__FILE__, __('Support', 'meeting-scheduler-by-vcita'), __('Support', 'meeting-scheduler-by-vcita'), 'edit_posts', get_admin_url('','','admin').'admin.php?page='.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/vcita-settings-functions.php&tab=vcita-support');
  add_submenu_page(__FILE__, __('Premium', 'meeting-scheduler-by-vcita'), __('Premium', 'meeting-scheduler-by-vcita'), 'edit_posts', get_admin_url('','','admin').'admin.php?page='.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/vcita-settings-functions.php&tab=vcita-premium');
}

function wpshd_vcita_add_plugins_actions($actions, $plugin_file, $plugin_data, $context)
{

  if (strpos($plugin_file, WPSHD_VCITA_WIDGET_UNIQUE_ID) !== false) {
    array_unshift($actions, '<a href="' . get_admin_url('','','admin').'admin.php?page='.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/vcita-settings-functions.php' . '" target="_blank">' . __('Settings') . '</a>');

    $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
    if (!isset($wpshd_vcita_widget['uid'])) $wpshd_vcita_widget['uid'] = '';

    if ($wpshd_vcita_widget['uid'] && isset($_COOKIE['wpshd_is_trial']) && $_COOKIE['wpshd_is_trial'] == '1') {
      array_unshift($actions, '<a style="color:red;font-weight:800" href="' . get_admin_url('','','admin').'admin.php?page='.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/vcita-settings-functions.php&vcita-premium' . '" target="_blank">' . __('Upgrade to pro') . '</a>');
    }
  }

  return $actions;
}
function wpshd_vcita_settings_menu()
{
  if (function_exists('rest_url')) {
    define('WPSHD_VCITA_WIDGET_CALLBACK_URL', rest_url('vcita-wordpress/v1'));
  }

  // Whitelist of valid tabs
  $valid_tabs = array(
    'vcita-add-to-site', 
    'vcita-custom-impl', 
    'vcita-main', 
    'vcita-premium', 
    'vcita-support'
  );

  $tab = '/pages/vcita-main.php'; // Default tab

  if (isset($_GET['tab'])) {
    $requested_tab = sanitize_text_field($_GET['tab']);
    if (in_array($requested_tab, $valid_tabs) && file_exists(dirname(__FILE__) . '/pages/' . $requested_tab . '.php')) {
      $tab = '/pages/' . $requested_tab . '.php';
    }
  }

  require_once WP_PLUGIN_DIR . '/' . WPSHD_VCITA_WIDGET_UNIQUE_ID . $tab;
}
