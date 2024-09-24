<?php

add_action('wp_ajax_vcita_dismiss', 'vcita_dismiss');
add_action('wp_ajax_vcita_logout', 'vcita_logout_callback');
add_action('wp_ajax_vcita_check_auth', 'vcita_check_auth');
add_action('wp_ajax_vcita_save_settings', 'vcita_save_settings_callback');
add_action('wp_ajax_vcita_save_data', 'vcita_save_user_data_callback');
add_action('wp_ajax_vcita_deactivate_others', 'vcita_vcita_deactivate_others_callback');

function vcita_dismiss()
{
  if (isset($_GET['dismiss'])) {
    $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
    $wpshd_vcita_widget['dismiss'] = true;
    $wpshd_vcita_widget['dismiss_time'] = microtime(true);
    update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
    echo 'dismissed';
    wp_die();
  } else if (isset($_GET['dismiss_switch'])) {
    $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
    $wpshd_vcita_widget['dismiss_switch'] = true;
    $wpshd_vcita_widget['dismiss_switch_time'] = microtime(true);
    update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
    echo 'dismissed';
    wp_die();
  } else if (isset($_GET['switch_on'])) {
    $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
    $wpshd_vcita_widget['dismiss_switch'] = false;
    unset($wpshd_vcita_widget['dismiss_switch_time']);
    $wpshd_vcita_widget['show_on_site'] = 1;
    update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
    echo 'dismissed';
    wp_die();
  }
}

function vcita_check_auth()
{
  $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
  echo json_encode($wpshd_vcita_widget);
  wp_die();
}

function vcita_vcita_deactivate_others_callback()
{
  $av_plugin_list = wp_cache_get('WPSHD_VCITA_ANOTHER_PLUGIN_LIST');
  $found = array();
  foreach ($av_plugin_list as $av_plugin) $found[] = $av_plugin['file'];
  deactivate_plugins($found);

  echo 'success';
  wp_die();
}

function vcita_logout_callback()
{
  if ( current_user_can('delete_plugins') ) {
    $wpshd_vcita_widget = wpshd_vcita_clean_expert_data();
    $wpshd_vcita_widget['dismiss'] = false;
    unset($wpshd_vcita_widget['dismiss_time']);

    if (isset($wpshd_vcita_widget['wp_id']) && $wpshd_vcita_widget['wp_id']) {
      vcita_send_get('https://us-central1-scheduler-272415.cloudfunctions.net/scheduler-proxy/logout/' . $wpshd_vcita_widget['wp_id']);
    }

    update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
    echo 'logged out';
    wp_die();
  }
}

function vcita_save_user_data_callback()
{
  header('Content-Type: application/json');
  $response = array();

  if (isset($_REQUEST['data_name']) && isset($_REQUEST['data_val'])) {
    $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
    $wpshd_vcita_widget[$_REQUEST['data_name']] = $_REQUEST['data_val'];
    update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
    $response['success'] = true;
  } else {
    $response['error'] = 'Request invalid';
  }

  echo json_encode($response);
  wp_die();
}

function vcita_save_settings_callback()
{
  header('Content-Type: application/json');
  $response = array();

  if (isset($_POST['btn_text']) || isset($_POST['btn_color']) || isset($_POST['txt_color']) ||
    isset($_POST['show_on_site']) || isset($_POST['widget_title']) || isset($_POST['widget_title']) ||
    isset($_POST['txt_color']) || isset($_POST['widget_show']) || isset($_POST['widget_text']) ||
    isset($_FILES['widget_img']) || isset($_POST['calendar_page_active']) || isset($_POST['contact_page_active']) ||
    isset($_POST['hover_color']) || isset($_POST['vcita_design'])
  ) {
    $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);

    if (isset($_POST['show_on_site'])) {
      $wpshd_vcita_widget['show_on_site'] = filter_var($_POST['show_on_site'], FILTER_VALIDATE_INT);

      if ($_POST['show_on_site']) {
        $wpshd_vcita_widget['dismiss_switch'] = false;
        unset($wpshd_vcita_widget['dismiss_switch_time']);
      }
    }

    if (isset($_POST['vcita_design'])) {
      $wpshd_vcita_widget['vcita_design'] = filter_var($_POST['vcita_design'], FILTER_VALIDATE_INT);
    }

    if (isset($_POST['btn_text'])) {
      $wpshd_vcita_widget['btn_text'] = htmlentities($_POST['btn_text']);
    }

    if (isset($_POST['btn_color'])) {
      $wpshd_vcita_widget['btn_color'] = filter_var($_POST['btn_color'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH);
    }

    if (isset($_POST['txt_color'])) {
      $wpshd_vcita_widget['txt_color'] = filter_var($_POST['txt_color'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH);
    }

    if (isset($_POST['hover_color'])) {
      $wpshd_vcita_widget['hover_color'] = filter_var($_POST['hover_color'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH);
    }

    if (isset($_POST['widget_title'])) {
      $wpshd_vcita_widget['widget_title'] = htmlentities($_POST['widget_title']);
    }

    if (isset($_POST['txt_color'])) {
      $wpshd_vcita_widget['txt_color'] = filter_var($_POST['txt_color'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH);
    }

    if (isset($_POST['widget_show'])) {
      $wpshd_vcita_widget['widget_show'] = filter_var($_POST['widget_show'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_ENCODE_HIGH);
    }

    if (isset($_POST['widget_text'])) {
      $wpshd_vcita_widget['widget_text'] = htmlentities($_POST['widget_text']);
    }

    if (isset($_POST['widget_img_clear']) && $_POST['widget_img_clear']) {
      if (!empty($wpshd_vcita_widget['widget_img'])) {
        wp_delete_attachment($wpshd_vcita_widget['widget_img'], true);
        $wpshd_vcita_widget['widget_img'] = '';
      }
    }

    if (isset($_POST['calendar_page_active'])) {
      if ($_POST['calendar_page_active'] && $wpshd_vcita_widget['uid']) {
        wpshd_vcita_make_sure_calendar_page_published($wpshd_vcita_widget, true);
        $wpshd_vcita_widget['calendar_page_active'] = 1;
      } else {
        wpshd_vcita_trash_current_calendar_page($wpshd_vcita_widget);
        $wpshd_vcita_widget['calendar_page_active'] = 0;
      }
    }

    if (isset($_POST['contact_page_active'])) {
      if ($_POST['contact_page_active'] && $wpshd_vcita_widget['uid']) {
        wpshd_vcita_make_sure_page_published($wpshd_vcita_widget, true);
        $wpshd_vcita_widget['contact_page_active'] = 1;
      } else {
        wpshd_vcita_trash_contact_page($wpshd_vcita_widget);
        $wpshd_vcita_widget['contact_page_active'] = 0;
      }
    }

    function isFileImage($mimeType)
    {
      return in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif']);
    }

    if (isset($_FILES['widget_img']) && $_FILES['widget_img']['error'] == UPLOAD_ERR_OK) {

      if (!isFileImage($_FILES['widget_img']['type'])) {
        $response['error'] = 'Invalid file type must be jpeg, png or gif';

        echo json_encode($response);
        wp_die();
      }

      require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

      $wordpress_upload_dir = wp_upload_dir();
      $i = 1;

      $widget_img = $_FILES['widget_img'];
      $new_file_path = $wordpress_upload_dir['path'] . '/' . $widget_img['name'];
      $new_file_mime = mime_content_type($widget_img['tmp_name']);
      $_error = false;

      if (empty($widget_img)) $_error = true;
      if ($widget_img['error']) $_error = true;
      if ($widget_img['size'] > wp_max_upload_size()) $_error = true;
      if (!in_array($new_file_mime, get_allowed_mime_types())) $_error = true;

      while (file_exists($new_file_path)) {
        $i++;
        $new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $widget_img['name'];
      }

      if ($_error) {
        file_put_contents(dirname(__FILE__) . '/debug.log', print_r($_FILES['widget_img'], TRUE), FILE_APPEND);
        file_put_contents(dirname(__FILE__) . '/debug.log', 'error occured', FILE_APPEND);
      }

      if (move_uploaded_file($widget_img['tmp_name'], $new_file_path) && $_error === false) {
        $upload_id = wp_insert_attachment(array(
          'guid' => $new_file_path,
          'post_mime_type' => $new_file_mime,
          'post_title' => preg_replace('/\.[^.]+$/', '', $widget_img['name']),
          'post_content' => '',
          'post_status' => 'inherit'
        ), $new_file_path);

        require_once(ABSPATH . 'wp-admin/includes/image.php');

        wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $new_file_path));
        $wpshd_vcita_widget['widget_img'] = $upload_id;
        $response['widget_img'] = wp_get_attachment_image($upload_id);
      }
    }

    if (!isset($response['error']) || !$response['error']) {
      $response['success'] = true;
    }

    update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
  } else {
    $response['error'] = 'Nothing to change';
  }

  echo json_encode($response);
  wp_die();
}

function vcita_send_get($url, $options = array())
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  $output = curl_exec($ch);
  $error = curl_error($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if (empty($error) && $httpcode === 200) {
    return json_decode($output, true);
  } else if (empty($error)) {
    return array(
      'error' => $output,
      'description' => 'Request was not successful',
      'http_code' => $httpcode
    );
  } else {
    return array(
      'error' => $error,
      'description' => 'request was not successful'
    );
  }
}

function vcita_send_post($url, $options = array())
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  if ($options['post_data']) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $options['post_data']);
  }

  $output = curl_exec($ch);
  $error = curl_error($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if (empty($error) && $httpcode === 200) {
    return json_decode($output, true);
  } else if (empty($error)) {
    return array(
      'error' => $output,
      'description' => 'Request was not successful',
      'status' => $httpcode
    );
  } else {
    return array(
      'error' => $error,
      'description' => 'request was not successful',
      'status' => $httpcode
    );
  }
}

?>
