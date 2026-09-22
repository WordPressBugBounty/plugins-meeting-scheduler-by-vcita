<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}


// get_page_by_title() was deprecated in WordPress 6.2. Local copy of the
// replacement: first page with this exact title in any status, incl. trash.
function wpshd_vcita_get_page_by_title( $page_title ) {
	if ( '' === trim( (string) $page_title ) ) {
		return null;
	}

	$query = new WP_Query( array(
		'post_type'              => 'page',
		'title'                  => $page_title,
		'post_status'            => array( 'publish', 'future', 'draft', 'pending', 'private', 'trash' ),
		'posts_per_page'         => 1,
		'orderby'                => 'ID',
		'order'                  => 'ASC',
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
	) );

	return ! empty( $query->posts ) ? $query->posts[0] : null;
}

function wpshd_vcita_trash_contact_page($widget_params) {
	if (!empty($widget_params['page_id'])) {
		$page_id = $widget_params['page_id'];
		$page = get_post($page_id);
		
		if (!is_null($page) && $page->post_status === 'publish') {
			wp_trash_post($page_id);
		}
	} else {
		
		$page = wpshd_vcita_get_page_by_title('Contact Us');
		
		
		if (!is_null($page) && $page->post_status === 'publish') {
			wp_trash_post($page->ID);
		}
	}
}

function wpshd_vcita_trash_current_calendar_page($widget_params) {
	if (!empty($widget_params['calendar_page_id'])) {
		$page_id = $widget_params['calendar_page_id'];
		$page = get_post($page_id);
		
		
		if (!is_null($page) && $page->post_status === 'publish') {
			wp_trash_post($page_id);
		}
	} else {
		
		$page = wpshd_vcita_get_page_by_title('Book Appointment');
		
		
		if (!is_null($page) && $page->post_status === 'publish') {
			wp_trash_post($page->ID);
		}
	}
}

function vcita_send_get($url)
{
	$response = wp_remote_get( $url, array( 'timeout' => 15 ) );

	if ( is_wp_error( $response ) ) {
		return array(
			'error' => $response->get_error_message(),
			'description' => 'Request was not successful'
		);
	}

	$output   = wp_remote_retrieve_body( $response );
	$httpcode = (int) wp_remote_retrieve_response_code( $response );

	if ( 200 === $httpcode ) {
		return json_decode( $output, true );
	}

	return array(
		'error' => $output,
		'description' => 'Request was not successful',
		'http_code' => $httpcode
	);
}


$wpshd_vcita_widget = (array) get_option('vcita_scheduler');
wpshd_vcita_trash_contact_page($wpshd_vcita_widget);
wpshd_vcita_trash_current_calendar_page($wpshd_vcita_widget);

vcita_send_get('https://us-central1-scheduler-272415.cloudfunctions.net/scheduler-proxy/logout/' . $wpshd_vcita_widget['wp_id']);

if (isset($wpshd_vcita_widget['widget_img']) && $wpshd_vcita_widget['widget_img']) {
    wp_delete_attachment($wpshd_vcita_widget['widget_img'], true);
}

delete_option('vcita_scheduler');