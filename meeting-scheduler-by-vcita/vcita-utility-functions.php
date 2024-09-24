<?php

function wpshd_vcita_init()
{

  if (!function_exists('register_sidebar_widget') || !function_exists('register_widget_control')) {
    return;
  }

  wpshd_vcita_initialize_data();

  wp_register_sidebar_widget('vcita_widget_id', 'vcita Sidebar Widget', 'wpshd_vcita_widget_content');
}

function wpshd_vcita_initialize_data()
{
  $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);

  if (empty($wpshd_vcita_widget) || !isset($wpshd_vcita_widget['vcita_init'])
    || !isset($wpshd_vcita_widget['version']) || $wpshd_vcita_widget['version'] != WPSHD_VCITA_WIDGET_VERSION) {

    $version = isset($wpshd_vcita_widget['version']) ? $wpshd_vcita_widget['version'] : WPSHD_VCITA_WIDGET_VERSION;
    $wpshd_vcita_widget['version'] = WPSHD_VCITA_WIDGET_VERSION;

    if ($version != WPSHD_VCITA_WIDGET_VERSION) {
      $wpshd_vcita_widget['migrated'] = true;
    } else {
      $wpshd_vcita_widget['new_install'] = true;
    }

    if (wpshd_vcita_is_calendar_page_available($wpshd_vcita_widget)) {
      $wpshd_vcita_widget['calendar_page_active'] = 1;
    } else {
      $wpshd_vcita_widget['calendar_page_active'] = 0;
    }

    if (wpshd_vcita_is_contact_page_available($wpshd_vcita_widget)) {
      $wpshd_vcita_widget['contact_page_active'] = 1;
    } else {
      $wpshd_vcita_widget['contact_page_active'] = 0;
    }

    $wpshd_vcita_widget = wpshd_vcita_create_initial_parameters(false, $wpshd_vcita_widget);
    $wpshd_vcita_widget['vcita_init'] = true;
  } else if ($wpshd_vcita_widget['new_install'] != true) {
    $wpshd_vcita_widget['new_install'] = false;
  }

  if (!$wpshd_vcita_widget['calendar_page_active']) {
    wpshd_vcita_trash_current_calendar_page($wpshd_vcita_widget);
  } else {
    wpshd_vcita_make_sure_calendar_page_published($wpshd_vcita_widget);
  }

  if (!$wpshd_vcita_widget['contact_page_active']) {
    wpshd_vcita_trash_contact_page($wpshd_vcita_widget);
  } else {
    wpshd_vcita_make_sure_page_published($wpshd_vcita_widget);
  }

  if (WPSHD_VCITA_ANOTHER_PLUGIN) $wpshd_vcita_widget['migrated_popup_showed'] = true;
  $wpshd_vcita_widget['version'] = WPSHD_VCITA_WIDGET_VERSION;
  update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
}

function wpshd_vcita_get_email($widget_params)
{
  return empty($widget_params['email']) ? get_option('admin_email') : $widget_params['email'];
}

function wpshd_vcita_get_uid()
{
  $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);

  if (!isset($wpshd_vcita_widget['uid']) || empty($wpshd_vcita_widget['uid'])) {
    return WPSHD_VCITA_WIDGET_DEMO_UID;
  } else {
    return $wpshd_vcita_widget['uid'];
  }
}

function wpshd_vcita_check_need_to_reconnect($wpshd_vcita_widget)
{
  $needs_reconnect = false;

  if ($wpshd_vcita_widget['migrated'] &&
    isset($wpshd_vcita_widget['uid']) &&
    $wpshd_vcita_widget['uid'] &&
    (
      !isset($wpshd_vcita_widget['business_id']) ||
      !$wpshd_vcita_widget['business_id'] ||
      $wpshd_vcita_widget['business_id'] != $wpshd_vcita_widget['uid']
    )
  ) {
    $needs_reconnect = true;
  }

  return $needs_reconnect;
}

function wpshd_vcita_is_demo_user()
{
  return (wpshd_vcita_get_uid() == WPSHD_VCITA_WIDGET_DEMO_UID);
}

function wpshd_vcita_get_page_edit_link($page_id)
{
  $page = get_page($page_id);
  return get_edit_post_link($page_id);
}

function wpshd_vcita_clean_expert_data()
{
  return wpshd_vcita_create_initial_parameters(true, (array)get_option(WPSHD_VCITA_WIDGET_KEY));
}

function wpshd_vcita_create_initial_parameters($clean = false, $old_params)
{
  $arr = create_default_widget_data($clean, $old_params);
  foreach ($arr as $key => $val) $old_params[$key] = $val;
  $arr = create_default_settings_data($old_params);
  foreach ($arr as $key => $val) $old_params[$key] = $val;
  return $old_params;
}

function create_default_widget_data($clean = false, array $old_params)
{
  if ($clean) {
    return array(
      'uid' => '',
      'name' => '',
      'email' => '',
      'migrated' => isset($old_params['migrated']) ? $old_params['migrated'] : false,
      'new_install' => false,
      'version' => WPSHD_VCITA_WIDGET_VERSION,
      'success' => false,
      'business_id' => '',
      'wp_id' => isset($old_params['wp_id']) ? $old_params['wp_id'] : '',
      'calendar_page_active' => false,
      'calendar_page_id' => isset($old_params['calendar_page_id']) ? $old_params['calendar_page_id'] : '',
      'contact_page_active' => false,
      'page_id' => isset($old_params['page_id']) ? $old_params['page_id'] : '',
      'start_wizard_clicked' => isset($old_params['start_wizard_clicked']) ? $old_params['start_wizard_clicked'] : 0,
      'migrated_popup_showed' => isset($old_params['migrated_popup_showed']) ? $old_params['migrated_popup_showed'] : false
    );
  } else {
    $migrated = isset($old_params['migrated']) && $old_params['migrated'];

    return array(
      'uid' => $migrated && isset($old_params['uid']) ? $old_params['uid'] : '',
      'name' => $migrated && isset($old_params['first_name']) ? $old_params['first_name'] : '',
      'email' => $migrated && isset($old_params['email']) ? $old_params['email'] : '',
      'migrated' => $migrated,
      'new_install' => isset($old_params['new_install']) ? $old_params['new_install'] : false,
      'version' => WPSHD_VCITA_WIDGET_VERSION,
      'success' => false,
      'business_id' => '',
      'wp_id' => isset($old_params['wp_id']) ? $old_params['wp_id'] : '',
      'calendar_page_active' => isset($old_params['calendar_page_active']) ? $old_params['calendar_page_active'] : false,
      'calendar_page_id' => isset($old_params['calendar_page_id']) ? $old_params['calendar_page_id'] : '',
      'contact_page_active' => isset($old_params['contact_page_active']) ? $old_params['contact_page_active'] : false,
      'page_id' => isset($old_params['page_id']) ? $old_params['page_id'] : '',
      'start_wizard_clicked' => isset($old_params['start_wizard_clicked']) ? $old_params['start_wizard_clicked'] : 0,
      'migrated_popup_showed' => isset($old_params['migrated_popup_showed']) ? $old_params['migrated_popup_showed'] : false
    );
  }
}

function create_default_settings_data(array $old_params)
{
  return array(
    'vcita_design' => isset($old_params['vcita_design']) ? $old_params['vcita_design'] : ($old_params['migrated'] && $old_params['uid'] ? 1 : 0),
    'widget_img' => isset($old_params['widget_img']) ? $old_params['widget_img'] : '',
    'widget_title' => isset($old_params['widget_title']) ? $old_params['widget_title'] : '',
    'show_on_site' => isset($old_params['show_on_site']) ? $old_params['show_on_site'] : 1,
    'widget_show' => isset($old_params['widget_show']) ? $old_params['widget_show'] : 0,
    'btn_text' => isset($old_params['btn_text']) ? $old_params['btn_text'] : '',
    'btn_color' => isset($old_params['btn_color']) ? $old_params['btn_color'] : '#01dcf7',
    'txt_color' => isset($old_params['txt_color']) ? $old_params['txt_color'] : '#ffffff',
    'hover_color' => isset($old_params['hover_color']) ? $old_params['hover_color'] : '#01dcf7',
    'widget_text' => isset($old_params['widget_text']) ? $old_params['widget_text'] : '',
  );

}

function wpshd_vcita_default_if_non($arr_obj, $index, $default_value = '')
{
  return isset($arr_obj) && isset($arr_obj[$index]) ? $arr_obj[$index] : $default_value;
}

function wpshd_vcita_add_contact($atts)
{
  $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
  $id = WPSHD_VCITA_WIDGET_DEMO_UID;

  if (isset($wpshd_vcita_widget['uid']) && $wpshd_vcita_widget['uid']) {
    $id = $wpshd_vcita_widget['uid'];
  }

  extract(shortcode_atts(array(
    'type' => 'contact',
    'width' => '100%',
    'height' => '450px',
  ), $atts));

  return wpshd_vcita_create_embed_code($type, $id, $width, $height);
}

function wpshd_vcita_add_calendar($atts)
{
  $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
  $id = WPSHD_VCITA_WIDGET_DEMO_UID;

  if (isset($wpshd_vcita_widget['uid']) && $wpshd_vcita_widget['uid']) {
    $id = $wpshd_vcita_widget['uid'];
  }

  extract(shortcode_atts(array(
    'type' => 'scheduler',
    'width' => '100%',
    'height' => '500px',
  ), $atts));

  return wpshd_vcita_create_embed_code($type, $id, $width, $height);
}

function wpshd_vcita_add_contact_page($by_ajax = false)
{
  // if (!$by_ajax && (true === DOING_CRON || true === DOING_AJAX)) return;
  if (!$by_ajax && ((defined('DOING_CRON') && DOING_CRON) || (defined('DOING_AJAX') && DOING_AJAX))) return;

  return wp_insert_post(array(
    'post_name' => 'contact-form',
    'post_title' => __('Contact Us', 'meeting-scheduler-by-vcita'),
    'post_type' => 'page',
    'post_status' => 'publish',
    'comment_status' => 'closed',
    'post_content' => '[' . WPSHD_VCITA_WIDGET_SHORTCODE . ']'));
}

function wpshd_wpshd_vcita_add_calendar_page($by_ajax = false)
{
  // if (!$by_ajax && (true === DOING_CRON || true === DOING_AJAX)) return;
  if (!$by_ajax && ((defined('DOING_CRON') && DOING_CRON) || (defined('DOING_AJAX') && DOING_AJAX))) return;

  return wp_insert_post(array(
    'post_name' => 'appointment-booking',
    'post_title' => __('Book Appointment', 'meeting-scheduler-by-vcita'),
    'post_type' => 'page',
    'post_status' => 'publish',
    'comment_status' => 'closed',
    'post_content' => '[' . WPSHD_VCITA_CALENDAR_WIDGET_SHORTCODE . ']'));
}

function wpshd_vcita_create_embed_code($type, $uid, $width, $height)
{
  if (isset($uid) && !empty($uid)) {
    $code = get_transient('embed_code' . $type . $uid . $width . $height);

    if (!$code) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_URL, "http://" . WPSHD_VCITA_SERVER_URL . "/api/experts/" . urlencode($uid) . "/embed_code?type=" . $type . "&width=" . urlencode($width) . "&height=" . urlencode($height));
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
      curl_setopt($ch, CURLOPT_PROXY, '');
      $data = curl_exec($ch);
      curl_close($ch);

      $data = json_decode($data, true);

      if (isset($data['code'])) {
        $code = html_entity_decode($data['code']);
        // Set the embed code to be cached for an hour
        set_transient('embed_code' . $type . $uid . $width . $height, $code, 3600);
      } else {
        $code = "<iframe frameborder='0' src='//" . WPSHD_VCITA_SERVER_URL . "/" . urlencode($uid) . "/" . $type . "/' width='" . $width . "' height='" . $height . "'></iframe>";
      }
    }
  }

  return $_SERVER['HTTPS'] && $_SERVER['HTTPS'] == 'on' ? str_replace('http://', 'https://', $code) : $code;
}

function wpshd_vcita_make_sure_page_published($wpshd_vcita_widget, $by_ajax = false)
{

  $page_id = wpshd_vcita_default_if_non($wpshd_vcita_widget, 'page_id');
  $page = get_page($page_id);

  if (empty($page)) {
    $page = get_page_by_title(__('Contact Us', 'meeting-scheduler-by-vcita'));
    $page_id = $page->ID;
  }

  if (empty($page)) {
    $page_id = wpshd_vcita_add_contact_page($by_ajax);
  } elseif ($page->{"post_status"} == "trash") {
    wp_untrash_post($page_id);
  } elseif ($page->{"post_status"} != "publish") {
    $page_id = wpshd_vcita_add_contact_page();
  }

  $wpshd_vcita_widget['page_id'] = $page_id;
  $wpshd_vcita_widget['contact_page_active'] = 'true';
  update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
  return $wpshd_vcita_widget;
}

function wpshd_vcita_get_contact_url($whshd_vcita_widget) {
  $page_id = wpshd_vcita_default_if_non($whshd_vcita_widget, 'page_id');
  $page = get_page($page_id);
  $page_url = '/contact-form';

  if (empty($page)) {
    $page = get_page_by_title(__('Contact Us', 'meeting-scheduler-by-vcita'));
    if (!empty($page)) return get_post_permalink($page->ID);
    return $page_url;
  } else {
    return get_post_permalink($page->ID);
  }
}

function wpshd_vcita_get_schedule_url($whshd_vcita_widget) {
  $page_id = wpshd_vcita_default_if_non($whshd_vcita_widget, 'calendar_page_id');
  $page = get_page($page_id);
  $page_url = '/appointment-booking';

  if (empty($page)) {
    $page = get_page_by_title(__('Book Appointment', 'meeting-scheduler-by-vcita'));
    if (!empty($page)) return get_post_permalink($page->ID);
    return $page_url;
  } else {
    return get_post_permalink($page->ID);
  }
}

function wpshd_vcita_make_sure_calendar_page_published($wpshd_vcita_widget, $by_ajax = false)
{

  $page_id = wpshd_vcita_default_if_non($wpshd_vcita_widget, 'calendar_page_id');
  $page = get_page($page_id);

  if (empty($page)) {
    $page = get_page_by_title(__('Book Appointment', 'meeting-scheduler-by-vcita'));
    $page_id = $page->ID;
  }

  if (empty($page)) {
    $page_id = wpshd_wpshd_vcita_add_calendar_page($by_ajax);
  } elseif ($page->{"post_status"} == "trash") {
    wp_untrash_post($page_id);
  } elseif ($page->{"post_status"} != "publish") {
    $page_id = wpshd_wpshd_vcita_add_calendar_page();
  }

  $wpshd_vcita_widget['calendar_page_id'] = $page_id;
  $wpshd_vcita_widget['calendar_page_active'] = 'true';
  update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);

  return $wpshd_vcita_widget;
}

function wpshd_vcita_is_contact_page_available($wpshd_vcita_widget)
{
  if (!isset($wpshd_vcita_widget['page_id']) || empty($wpshd_vcita_widget['page_id'])) {
    $page = get_page_by_title(__('Contact Us', 'meeting-scheduler-by-vcita'));

    if (!empty($page) && $page->{"post_status"} == "publish") {
      return true;
    } else return false;
  } else {
    $page_id = $wpshd_vcita_widget['page_id'];
    $page = get_page($page_id);
    return !empty($page) && $page->{"post_status"} == "publish";
  }
}

function wpshd_vcita_is_calendar_page_available($wpshd_vcita_widget)
{
  if (!isset($wpshd_vcita_widget['calendar_page_id']) || empty($wpshd_vcita_widget['calendar_page_id'])) {
    $page = get_page_by_title(__('Book Appointment', 'meeting-scheduler-by-vcita'));

    if (!empty($page) && $page->{"post_status"} == "publish") {
      return true;
    } else return false;
  } else {
    $page_id = $wpshd_vcita_widget['calendar_page_id'];
    $page = get_page($page_id);
    return !empty($page) && $page->{"post_status"} == "publish";
  }
}

function wpshd_vcita_trash_contact_page($widget_params)
{
  if (isset($widget_params['page_id']) && !empty($widget_params['page_id'])) {
    $page_id = $widget_params['page_id'];
    $page = get_page($page_id);
    if (!empty($page) && $page->{"post_status"} == "publish") wp_trash_post($page_id);
  } else {
    $page = get_page_by_title(__('Contact Us', 'meeting-scheduler-by-vcita'));
    if (!empty($page) && $page->{"post_status"} == "publish") wp_trash_post($page->ID);
  }
}

function wpshd_vcita_trash_current_calendar_page($widget_params)
{
  if (isset($widget_params['calendar_page_id']) && !empty($widget_params['calendar_page_id'])) {
    $page_id = $widget_params['calendar_page_id'];
    $page = get_page($page_id);
    if (!empty($page) && $page->{"post_status"} == "publish") wp_trash_post($page_id);
  } else {
    $page = get_page_by_title(__('Book Appointment', 'meeting-scheduler-by-vcita'));
    if (!empty($page) && $page->{"post_status"} == "publish") wp_trash_post($page->ID);
  }
}

function wpshd_vcita_widget_content($args)
{
  echo wpshd_vcita_add_contact();
}

function wpshd_vcita_widget_admin()
{
  wp_enqueue_style('vcita-widgets-style', plugins_url('assets/style/widgets_page.css', __FILE__));
  $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
  $uid = $wpshd_vcita_widget['uid']; ?>
  <script type="text/javascript">
    jQuery(function ($) {
      $('#vcita_config #start-login')
        .click(function (ev) {
          ev.preventDefault();
          ev.stopPropagation();
          VcitaMixpman.track('wp_sched_login_vcita');
          VcitaUI.openAuthWin(false, false);
        });
      $('#vcita_config #switch-account')
        .click(function (ev) {
          VcitaMixpman.track('wp_sched_logout')
          $.post(`${window.$_ajaxurl}?action=vcita_logout`);
          VcitaUI.openAuthWin(false, true);
        });
      $('#vcita_config .preview')
        .click(function (e) {
          var link = $(e.currentTarget);
          var height = link.data().height ? link.data().height : 600;
          var width = link.data().width ? link.data().width : 600;
          var specs = 'directories=0, height=' + height + ', width=' + width + ', location=0, menubar=0, scrollbars=0, status=0, titlebar=0, toolbar=0';
          window.open(link.attr('href'), '_blank', specs);
          e.preventDefault();
        });
    });
  </script>
  <div id="vcita_config" dir="ltr">
    <?php if (!$uid) { ?>
      <h3>
        <?php echo __('To use vCita\'s sidebar please', 'meeting-scheduler-by-vcita') ?>
        <button class="vcita__btn__blue" id="start-login">
          <?php echo __('Connect to vcita', 'meeting-scheduler-by-vcita') ?>
        </button>
      </h3>
    <?php } else { ?>
      <h3>
        <?php echo __('Contact requests will be sent to this email:', 'meeting-scheduler-by-vcita') ?>
      </h3>
      <div>
        <input class="txt_input" type="text" disabled="disabled" value="<?php echo $wpshd_vcita_widget["email"] ?>"/>
        <a href="javascript:void(0)" id="switch-account">
          <?php echo __('Change Email', 'meeting-scheduler-by-vcita') ?>
        </a>
      </div>
      <div class="no-space">
        <a href="https://app.<?php echo WPSHD_VCITA_SERVER_BASE ?>/app/my-livesite?section=website-widgets" target="_blank">
          <?php echo __('Edit', 'meeting-scheduler-by-vcita') ?>
        </a>
        <a class="preview" href="//<?php echo WPSHD_VCITA_SERVER_URL ?>/contact_widget?v=<?php echo wpshd_vcita_get_uid() ?>&ver=2" data-width="200" data-height="500">
          <?php echo __('Preview', 'meeting-scheduler-by-vcita') ?>
        </a>
      </div>
    <?php } ?>
  </div>
  <?php
} ?>
