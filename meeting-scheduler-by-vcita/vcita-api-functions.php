<?php

function vcita_callback(WP_REST_Request $request)
{
    $action = sanitize_text_field($request['action']);
    $nonce = $request->get_header('X-WP-Nonce');

    // Allow unauthenticated access for initial actions
    $public_actions = array('connect', 'let\'s get started', 'auth');

    // If the action is not public, enforce authorization
    if (!in_array($action, $public_actions) && (!is_user_logged_in() || !current_user_can('edit_posts'))) {
        return new WP_REST_Response('Unauthorized', 401);
    }

    // Verify nonce for all actions
    if (!in_array($action, $public_actions) && !wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_REST_Response('Invalid nonce', 403);
    }

    if ($request->get_method() === 'POST') {
        $data = json_decode($request->get_body(), true);

        if (WPSHD_VCITA_DEBUG) {
            echo('Printing request body:') . PHP_EOL;
            echo(print_r($data, true)) . PHP_EOL;
        }

        if (is_array($data)) {
            if (isset($data['success']) && filter_var($data['success'], FILTER_VALIDATE_BOOLEAN) && $data['success']) {
                processAction($action, $data);
            }
        }
    } else if ($request->get_method() === 'GET') {
        $query_params = $request->get_query_params();
        $query = array();
        foreach ($query_params as $key => $val) {
            $query[sanitize_text_field($key)] = sanitize_text_field($val);
        }

        if (isset($query['method']) && $query['method'] == 'enc' && isset($query['d'])) {
            $data = utf8_decode(base64_decode($query['d']));
            if ($data) $data = json_decode($data, true);
            if ($data && is_array($data)) processAction($action, $data);
        }
    }

    exit;
}

function processAction($action, $data = array())
{
    // Allow unauthenticated access for initial actions
    $public_actions = array('connect', 'let\'s get started', 'auth');

    // If the action is not public, enforce authorization
    if (!in_array($action, $public_actions) && (!is_user_logged_in() || !current_user_can('edit_posts'))) {
        return new WP_REST_Response('Unauthorized', 401);
    }

    if (WPSHD_VCITA_DEBUG) error_log('Processing action ' . $action);

    if ($action == 'install') {
        if (isset($data['wp_id'])) {
            $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
            $wpshd_vcita_widget['wp_id'] = sanitize_text_field($data['wp_id']);
            $status = update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
        }
    } else if (in_array($action, $public_actions)) {
        header('Content-Type: text/html');

        $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
        $fetchUrl = 'https://us-central1-scheduler-272415.cloudfunctions.net/scheduler-proxy/business/' . $wpshd_vcita_widget['wp_id'];
        $rawBusiness = file_get_contents($fetchUrl);
        $business = json_decode($rawBusiness, true);
        print_r($business);
        $businessId = $business['business_data']['id'];
        if ($businessId !== $data['user_data']['business_id']) {
            return;
        }

        if ($data['success']) {
            $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);

            $wpshd_vcita_widget['success'] = filter_var($data['success'], FILTER_VALIDATE_BOOLEAN);
            $wpshd_vcita_widget['uid'] = sanitize_text_field($data['user_data']['business_id']);
            $wpshd_vcita_widget['business_id'] = sanitize_text_field($data['user_data']['business_id']);
            $wpshd_vcita_widget['name'] = sanitize_text_field($data['user_data']['business_name']);
            $wpshd_vcita_widget['email'] = filter_var($data['user_data']['email'], FILTER_VALIDATE_EMAIL);
            update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);

            if (WPSHD_VCITA_DEBUG) {
                error_log('Printing data:');
                error_log(print_r($data, true));
                error_log('Printing widget data:');
                error_log(print_r($wpshd_vcita_widget, true));
            }

            echo '<h1>Authentication OK</h1>
                  <script type="text/javascript">
                    window.close();
                  </script>';
        } else if (isset($data['error'])) {
            echo esc_html($data['message']) ? esc_html($data['message']) : 'some error occurred';
        }
    }
}
