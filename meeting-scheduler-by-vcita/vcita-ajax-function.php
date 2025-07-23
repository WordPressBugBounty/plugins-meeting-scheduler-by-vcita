<?php

add_action( 'wp_ajax_vcita_dismiss', 'vcita_dismiss' );
add_action( 'wp_ajax_vcita_logout', 'vcita_logout_callback' );
add_action( 'wp_ajax_vcita_check_auth', 'vcita_check_auth' );
add_action( 'wp_ajax_vcita_save_settings', 'vcita_save_settings_callback' );
add_action( 'wp_ajax_vcita_save_data', 'vcita_save_user_data_callback' );
add_action( 'wp_ajax_vcita_deactivate_others', 'vcita_vcita_deactivate_others_callback' );

function vcita_dismiss() {
	
	if ( isset( $_GET[ 'dismiss' ] ) ) {
		$wpshd_vcita_widget                   = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
		$wpshd_vcita_widget[ 'dismiss' ]      = true;
		$wpshd_vcita_widget[ 'dismiss_time' ] = microtime( true );
		update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
		echo 'dismissed';
		wp_die();
	}
	else if ( isset( $_GET[ 'dismiss_switch' ] ) ) {
		$wpshd_vcita_widget                          = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
		$wpshd_vcita_widget[ 'dismiss_switch' ]      = true;
		$wpshd_vcita_widget[ 'dismiss_switch_time' ] = microtime( true );
		update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
		echo 'dismissed';
		wp_die();
	}
	else if ( isset( $_GET[ 'switch_on' ] ) ) {
		$wpshd_vcita_widget                     = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
		$wpshd_vcita_widget[ 'dismiss_switch' ] = false;
		unset( $wpshd_vcita_widget[ 'dismiss_switch_time' ] );
		$wpshd_vcita_widget[ 'show_on_site' ] = 1;
		update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
		echo 'dismissed';
		wp_die();
	}
	
	wp_send_json_error( 'Invalid request', 400 );
}

function vcita_check_auth() {
	// Authorization check - only administrators can access auth data
	if (!current_user_can('manage_options')) {
		wp_send_json_error('Insufficient permissions', 403);
		wp_die();
	}
	
	$wpshd_vcita_widget = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
	
	// Filter response to only include safe, necessary data
	$safe_data = array(
		'uid' => isset($wpshd_vcita_widget['uid']) ? sanitize_text_field($wpshd_vcita_widget['uid']) : '',
		'success' => isset($wpshd_vcita_widget['success']) ? (bool) $wpshd_vcita_widget['success'] : false,
		'migrated' => isset($wpshd_vcita_widget['migrated']) ? (bool) $wpshd_vcita_widget['migrated'] : false,
		'show_on_site' => isset($wpshd_vcita_widget['show_on_site']) ? (int) $wpshd_vcita_widget['show_on_site'] : 0,
		'contact_page_active' => isset($wpshd_vcita_widget['contact_page_active']) ? (int) $wpshd_vcita_widget['contact_page_active'] : 0,
		'calendar_page_active' => isset($wpshd_vcita_widget['calendar_page_active']) ? (int) $wpshd_vcita_widget['calendar_page_active'] : 0,
	);
	
	wp_send_json( $safe_data );
}

function vcita_vcita_deactivate_others_callback() {
	$av_plugin_list = wp_cache_get( 'WPSHD_VCITA_ANOTHER_PLUGIN_LIST' );
	
	if ( ! $av_plugin_list ) {
		wp_send_json_error( 'No plugins found', 404 );
	}
	
	$found = array();
	foreach ( $av_plugin_list as $av_plugin ) {
		if ( isset( $av_plugin[ 'file' ] ) ) {
			$found[] = $av_plugin[ 'file' ];
		}
	}
	
	if ( ! empty( $found ) ) {
		deactivate_plugins( $found );
		echo 'success';
	}
	else {
		wp_send_json_error( 'No plugins to deactivate', 400 );
	}
	
	wp_die();
}


function vcita_logout_callback() {
	if ( current_user_can( 'delete_plugins' ) ) {
		$wpshd_vcita_widget              = wpshd_vcita_clean_expert_data();
		$wpshd_vcita_widget[ 'dismiss' ] = false;
		unset( $wpshd_vcita_widget[ 'dismiss_time' ] );
		
		if ( isset( $wpshd_vcita_widget[ 'wp_id' ] ) && ! empty( $wpshd_vcita_widget[ 'wp_id' ] ) ) {
			$response = vcita_send_get( 'https://us-central1-scheduler-272415.cloudfunctions.net/scheduler-proxy/logout/' . sanitize_text_field( $wpshd_vcita_widget[ 'wp_id' ] ) );
			
			if ( is_wp_error( $response ) ) {
				wp_send_json_error( 'Logout failed: ' . $response->get_error_message(), 500 );
			}
		}
		
		update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
		wp_send_json_success( 'Logged out successfully' );
	}
	else {
		wp_send_json_error( 'Unauthorized', 403 );
	}
	
	wp_die();
}


function vcita_save_user_data_callback() {
	header('Content-Type: application/json');
	$response = array();
	
	// CSRF
	if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'wpshd_vcita_nonce_action')) {
		wp_send_json_error('Invalid nonce');
		wp_die();
	}
	
	if (!current_user_can('manage_options')) {
		wp_send_json_error('Insufficient permissions');
		wp_die();
	}
	
	if (isset($_REQUEST['data_name']) && isset($_REQUEST['data_val'])) {
		$data_name = sanitize_key($_REQUEST['data_name']);
		$data_val  = sanitize_text_field($_REQUEST['data_val']);
		
		$wpshd_vcita_widget = (array) get_option(WPSHD_VCITA_WIDGET_KEY);
		$wpshd_vcita_widget[$data_name] = $data_val;
		
		update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
		
		$response['success'] = true;
	} else {
		$response['error'] = 'Request invalid';
	}
	
	// Возвращаем ответ
	echo json_encode($response);
	wp_die();
}


function vcita_save_settings_callback() {
	header( 'Content-Type: application/json' );
	$response = array();
	
	// CSRF
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wpshd_vcita_nonce_action')) {
		wp_send_json_error('Invalid nonce');
		wp_die();
	}
	
	// Authorization check - only administrators can manage widget settings
	if (!current_user_can('manage_options')) {
		wp_send_json_error('Insufficient permissions');
		wp_die();
	}
	
	if ( isset( $_POST[ 'btn_text' ] ) || isset( $_POST[ 'btn_color' ] ) || isset( $_POST[ 'txt_color' ] ) || isset( $_POST[ 'show_on_site' ] ) || isset( $_POST[ 'widget_title' ] ) || isset( $_POST[ 'widget_show' ] ) || isset( $_POST[ 'widget_text' ] ) || isset( $_FILES[ 'widget_img' ] ) || isset( $_POST[ 'calendar_page_active' ] ) || isset( $_POST[ 'contact_page_active' ] ) || isset( $_POST[ 'hover_color' ] ) || isset( $_POST[ 'vcita_design' ] ) ) {
		
		
		$wpshd_vcita_widget = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
		
		
		if ( isset( $_POST[ 'show_on_site' ] ) ) {
			$wpshd_vcita_widget[ 'show_on_site' ] = filter_var( $_POST[ 'show_on_site' ], FILTER_VALIDATE_INT );
			if ( $_POST[ 'show_on_site' ] ) {
				$wpshd_vcita_widget[ 'dismiss_switch' ] = false;
				unset( $wpshd_vcita_widget[ 'dismiss_switch_time' ] );
			}
		}
		
		if ( isset( $_POST[ 'vcita_design' ] ) ) {
			$wpshd_vcita_widget[ 'vcita_design' ] = filter_var( $_POST[ 'vcita_design' ], FILTER_VALIDATE_INT );
		}
		
		
		foreach (
			[
				'btn_text',
				'btn_color',
				'txt_color',
				'hover_color',
				'widget_title',
				'widget_show',
				'widget_text'
			] as $field
		) {
			if ( isset( $_POST[ $field ] ) ) {
				$wpshd_vcita_widget[ $field ] = sanitize_text_field( $_POST[ $field ] );
			}
		}
		
		
		if ( isset( $_POST[ 'widget_img_clear' ] ) && $_POST[ 'widget_img_clear' ] ) {
			if ( ! empty( $wpshd_vcita_widget[ 'widget_img' ] ) ) {
				wp_delete_attachment( $wpshd_vcita_widget[ 'widget_img' ], true );
				$wpshd_vcita_widget[ 'widget_img' ] = '';
			}
		}
		
		
		foreach (
			[
				'calendar_page_active' => 'wpshd_vcita_make_sure_calendar_page_published',
				'contact_page_active'  => 'wpshd_vcita_make_sure_page_published'
			] as $page => $callback
		) {
			if ( isset( $_POST[ $page ] ) ) {
				if ( $_POST[ $page ] && $wpshd_vcita_widget[ 'uid' ] ) {
					call_user_func( $callback, $wpshd_vcita_widget, true );
					$wpshd_vcita_widget[ $page ] = 1;
				}
				else {
					call_user_func( 'wpshd_vcita_trash_' . $page, $wpshd_vcita_widget );
					$wpshd_vcita_widget[ $page ] = 0;
				}
			}
		}
		
		
		// Secure file upload handling
		if ( isset( $_FILES[ 'widget_img' ] ) && $_FILES[ 'widget_img' ][ 'error' ] == UPLOAD_ERR_OK ) {
			
			// Check upload permissions
			if ( ! current_user_can( 'upload_files' ) ) {
				$response[ 'error' ] = 'You do not have permission to upload files';
				echo json_encode( $response );
				wp_die();
			}
			
			// Use WordPress built-in secure file upload handling
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			
			// Define allowed file types and extensions
			$allowed_types = array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'gif'          => 'image/gif',
				'png'          => 'image/png',
			);
			
			// Validate file extension
			$file_ext = pathinfo( $_FILES[ 'widget_img' ][ 'name' ], PATHINFO_EXTENSION );
			$file_ext = strtolower( $file_ext );
			
			$allowed_extensions = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
			if ( ! in_array( $file_ext, $allowed_extensions ) ) {
				$response[ 'error' ] = 'Invalid file extension. Only JPG, PNG, and GIF files are allowed.';
				echo json_encode( $response );
				wp_die();
			}
			
			// Use WordPress file upload validation
			$upload_overrides = array(
				'test_form' => false,
				'mimes'     => $allowed_types
			);
			
			$movefile = wp_handle_upload( $_FILES[ 'widget_img' ], $upload_overrides );
			
			if ( $movefile && ! isset( $movefile[ 'error' ] ) ) {
				// Verify the uploaded file is actually an image
				$image_info = getimagesize( $movefile[ 'file' ] );
				if ( $image_info === false ) {
					// Not a valid image file, delete it
					wp_delete_file( $movefile[ 'file' ] );
					$response[ 'error' ] = 'Uploaded file is not a valid image.';
					echo json_encode( $response );
					wp_die();
				}
				
				// Create attachment record in database
				$attachment = array(
					'guid'           => $movefile[ 'url' ],
					'post_mime_type' => $movefile[ 'type' ],
					'post_title'     => sanitize_file_name( pathinfo( $_FILES[ 'widget_img' ][ 'name' ], PATHINFO_FILENAME ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);
				
				$upload_id = wp_insert_attachment( $attachment, $movefile[ 'file' ] );
				
				if ( ! is_wp_error( $upload_id ) ) {
					// Generate attachment metadata
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					$attachment_data = wp_generate_attachment_metadata( $upload_id, $movefile[ 'file' ] );
					wp_update_attachment_metadata( $upload_id, $attachment_data );
					
					// Store the attachment ID
					$wpshd_vcita_widget[ 'widget_img' ] = $upload_id;
					$response[ 'widget_img' ] = wp_get_attachment_image( $upload_id );
				} else {
					// Error creating attachment, delete uploaded file
					wp_delete_file( $movefile[ 'file' ] );
					$response[ 'error' ] = 'Failed to create attachment.';
					echo json_encode( $response );
					wp_die();
				}
			} else {
				$response[ 'error' ] = isset( $movefile[ 'error' ] ) ? $movefile[ 'error' ] : 'File upload failed.';
				echo json_encode( $response );
				wp_die();
			}
		}
		
		
		if ( ! isset( $response[ 'error' ] ) ) {
			$response[ 'success' ] = true;
		}
		
		
		update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
	}
	else {
		$response[ 'error' ] = 'Nothing to change';
	}
	
	echo json_encode( $response );
	wp_die();
}

function vcita_send_get($url, $options = array()) {
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
	} elseif (empty($error) && $httpcode !== 200) {
		return array(
			'error'       => $output,
			'description' => 'Request was not successful',
			'http_code'   => $httpcode,
		);
	} else {
		return array(
			'error'       => $error,
			'description' => 'Request was not successful',
		);
	}
}


function vcita_send_post($url, $options = array()) {
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	
	if (!empty($options['post_data'])) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $options['post_data']);
	}
	
	
	$output = curl_exec($ch);
	$error = curl_error($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	
	if (empty($error) && $httpcode === 200) {
		return json_decode($output, true);
	} elseif (empty($error) && $httpcode !== 200) {
		return array(
			'error'       => $output,
			'description' => 'Request was not successful',
			'status'      => $httpcode,
		);
	} else {
		return array(
			'error'       => $error,
			'description' => 'Request was not successful',
			'status'      => $httpcode,
		);
	}
}


?>
