<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
function vcita_callback(WP_REST_Request $request) {
	$action = sanitize_text_field($request['action']);
	$nonce = $request->get_header('X-WP-Nonce');
	
	$public_actions = array('connect', 'let\'s get started', 'auth');
	
	if (!in_array($action, $public_actions) && (!is_user_logged_in() || !current_user_can('edit_posts'))) {
		return new WP_REST_Response('Unauthorized', 401);
	}
	
	if (!in_array($action, $public_actions) && !wp_verify_nonce($nonce, 'wp_rest')) {
		return new WP_REST_Response('Invalid nonce', 403);
	}
	
	if ($request->get_method() === 'POST') {
		$data = json_decode($request->get_body(), true);

		if (is_array($data) && isset($data['success']) && filter_var($data['success'], FILTER_VALIDATE_BOOLEAN)) {
			wpshd_vcita_process_action($action, $data);
		}
	}
	else if ($request->get_method() === 'GET') {
		$query_params = $request->get_query_params();
		$query = array();
		
		foreach ($query_params as $key => $val) {
			$query[sanitize_text_field($key)] = sanitize_text_field($val);
		}
		
		if (isset($query['method']) && $query['method'] === 'enc' && isset($query['d'])) {
			// The payload is base64-encoded UTF-8 JSON. utf8_decode() converted it to
			// ISO-8859-1, which corrupted every non-ASCII character and made
			// json_decode() return null - so connecting silently failed for any
			// business whose name is not plain ASCII. It is also deprecated in PHP 8.2.
			$data = base64_decode($query['d']);
			if ($data) {
				$data = json_decode($data, true);
				if (is_array($data)) {
					wpshd_vcita_process_action($action, $data);
				}
			}
		}
	}
	
	exit;
}


function wpshd_vcita_process_action($action, $data = array()) {
	$public_actions = array('connect', 'let\'s get started', 'auth');
	
	if (!in_array($action, $public_actions) && (!is_user_logged_in() || !current_user_can('edit_posts'))) {
		return new WP_REST_Response('Unauthorized', 401);
	}
	
	if ($action == 'install' && isset($data['wp_id'])) {
		$wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
		$wpshd_vcita_widget['wp_id'] = sanitize_text_field($data['wp_id']);
		update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
	}
	else if (in_array($action, $public_actions)) {
		header('Content-Type: text/html');

		$wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
		$fetchUrl = 'https://us-central1-scheduler-272415.cloudfunctions.net/scheduler-proxy/business/' . rawurlencode($wpshd_vcita_widget['wp_id']);
		$proxyResponse = wp_remote_get($fetchUrl);
		$business = is_wp_error($proxyResponse) ? null : json_decode(wp_remote_retrieve_body($proxyResponse), true);

		$submitted_business_id = isset($data['user_data']['business_id']) ? sanitize_text_field($data['user_data']['business_id']) : '';

		// Fail CLOSED: only proceed when the proxy returned a well-formed,
		// matching business record. Previously this used isset() on the
		// success path, which meant a malformed/empty proxy response (a
		// missing wp_id, an unknown wp_id, or the proxy being unreachable)
		// silently skipped the ownership check instead of rejecting the
		// request (CVE-2026-14433). wp_remote_get() also means a network
		// failure surfaces as a WP_Error rather than a PHP warning, and
		// still denies by default via the null above.
		if (
			!is_array($business) ||
			empty($business['business_data']['id']) ||
			$submitted_business_id === '' ||
			$business['business_data']['id'] !== $submitted_business_id
		) {
			return new WP_REST_Response('Unauthorized', 403);
		}

		// Belt-and-braces: a vcita business_id is always a 16-char lowercase
		// alphanumeric uid. Rejecting anything else here neutralises stored
		// XSS at the source, regardless of how it's later rendered.
		if (!preg_match('/^[a-z0-9]{16}$/', $submitted_business_id)) {
			return new WP_REST_Response('Invalid business_id', 400);
		}

		if (!empty($data['success'])) {
			$wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);

			$wpshd_vcita_widget['success'] = filter_var($data['success'], FILTER_VALIDATE_BOOLEAN);
			$wpshd_vcita_widget['uid'] = $submitted_business_id;
			$wpshd_vcita_widget['business_id'] = $submitted_business_id;
			$wpshd_vcita_widget['name'] = sanitize_text_field($data['user_data']['business_name']);
			$wpshd_vcita_widget['email'] = filter_var($data['user_data']['email'], FILTER_VALIDATE_EMAIL);
			update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);

			// This page is normally rendered inside the pop-up opened by
			// openAuthWin(), which closes itself and lets the opener refresh.
			// When the browser blocks that pop-up the flow completes in the main
			// tab instead, where window.close() is a no-op - which left the user
			// stranded on a bare "Authentication OK" page even though the
			// connection had succeeded. Send them back to the plugin in that case.
			$wpshd_vcita_return_url = admin_url( 'admin.php?page=' . WPSHD_VCITA_WIDGET_UNIQUE_ID . '/vcita-settings-functions.php' );

			echo '<h1>Authentication OK</h1>
                  <script type="text/javascript">
                    try {
                      if (window.opener && !window.opener.closed) { window.close(); }
                    } catch (e) {}
                    setTimeout(function () {
                      window.location.replace(' . wp_json_encode( $wpshd_vcita_return_url ) . ');
                    }, 400);
                  </script>';
		} else if (isset($data['error'])) {
			echo esc_html($data['message'] ?? 'Some error occurred');
		}
	}
}

