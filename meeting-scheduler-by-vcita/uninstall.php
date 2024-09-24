<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

function wpshd_vcita_trash_contact_page($widget_params)
{
    if (isset($widget_params['page_id']) && !empty($widget_params['page_id'])) {
        $page_id = $widget_params['page_id'];
        $page = get_page($page_id);

        if (!empty($page) && $page->{"post_status"} == "publish") {
            wp_trash_post($page_id);
        }
    } else {
        $page = get_page_by_title('Contact Us');

        if (!empty($page) && $page->{"post_status"} == "publish"){
            wp_trash_post($page->ID);
        }
    }
}

function wpshd_vcita_trash_current_calendar_page($widget_params)
{
    if (isset($widget_params['calendar_page_id']) && !empty($widget_params['calendar_page_id'])) {
        $page_id = $widget_params['calendar_page_id'];
        $page = get_page($page_id);

        if (!empty($page) && $page->{"post_status"} == "publish") {
            wp_trash_post($page_id);
        }
    } else {
        $page = get_page_by_title('Book Appointment');

        if (!empty($page) && $page->{"post_status"} == "publish"){
            wp_trash_post($page->ID);
        }
    }
}

function vcita_send_get($url)
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

$wpshd_vcita_widget = (array) get_option('vcita_scheduler');
wpshd_vcita_trash_contact_page($wpshd_vcita_widget);
wpshd_vcita_trash_current_calendar_page($wpshd_vcita_widget);

vcita_send_get('https://us-central1-scheduler-272415.cloudfunctions.net/scheduler-proxy/logout/' . $wpshd_vcita_widget['wp_id']);

if (isset($wpshd_vcita_widget['widget_img']) && $wpshd_vcita_widget['widget_img']) {
    wp_delete_attachment($wpshd_vcita_widget['widget_img'], true);
}

delete_option('vcita_scheduler');