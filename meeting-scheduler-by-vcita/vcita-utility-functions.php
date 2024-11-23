<?php

function wpshd_vcita_init() {
	
	if ( ! function_exists( 'register_sidebar_widget' ) || ! function_exists( 'register_widget_control' ) ) {
		return;
	}
	
	wpshd_vcita_initialize_data();
	
	wp_register_sidebar_widget( 'vcita_widget_id', 'vcita Sidebar Widget', 'wpshd_vcita_widget_content' );
}

function wpshd_vcita_initialize_data() {
	$wpshd_vcita_widget = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
	
	if ( empty( $wpshd_vcita_widget ) || ! isset( $wpshd_vcita_widget[ 'vcita_init' ] ) || ! isset( $wpshd_vcita_widget[ 'version' ] ) || $wpshd_vcita_widget[ 'version' ] !== WPSHD_VCITA_WIDGET_VERSION ) {
		$version                         = isset( $wpshd_vcita_widget[ 'version' ] )
			? sanitize_text_field( $wpshd_vcita_widget[ 'version' ] ) : WPSHD_VCITA_WIDGET_VERSION;
		$wpshd_vcita_widget[ 'version' ] = WPSHD_VCITA_WIDGET_VERSION;
		
		if ( $version !== WPSHD_VCITA_WIDGET_VERSION ) {
			$wpshd_vcita_widget[ 'migrated' ] = true;
		}
		else {
			$wpshd_vcita_widget[ 'new_install' ] = true;
		}
		
		$wpshd_vcita_widget[ 'calendar_page_active' ] = wpshd_vcita_is_calendar_page_available( $wpshd_vcita_widget )
			? 1 : 0;
		$wpshd_vcita_widget[ 'contact_page_active' ]  = wpshd_vcita_is_contact_page_available( $wpshd_vcita_widget ) ? 1
			: 0;
		
		$wpshd_vcita_widget                 = wpshd_vcita_create_initial_parameters( false, $wpshd_vcita_widget );
		$wpshd_vcita_widget[ 'vcita_init' ] = true;
	}
    elseif ( $wpshd_vcita_widget[ 'new_install' ] !== true ) {
		$wpshd_vcita_widget[ 'new_install' ] = false;
	}
	
	if ( ! empty( $wpshd_vcita_widget[ 'calendar_page_active' ] ) ) {
		wpshd_vcita_make_sure_calendar_page_published( $wpshd_vcita_widget );
	}
	else {
		wpshd_vcita_trash_current_calendar_page( $wpshd_vcita_widget );
	}
	
	if ( ! empty( $wpshd_vcita_widget[ 'contact_page_active' ] ) ) {
		wpshd_vcita_make_sure_page_published( $wpshd_vcita_widget );
	}
	else {
		wpshd_vcita_trash_contact_page( $wpshd_vcita_widget );
	}
	
	if ( defined( 'WPSHD_VCITA_ANOTHER_PLUGIN' ) && WPSHD_VCITA_ANOTHER_PLUGIN ) {
		$wpshd_vcita_widget[ 'migrated_popup_showed' ] = true;
	}
	
	$wpshd_vcita_widget[ 'version' ] = WPSHD_VCITA_WIDGET_VERSION;
	update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
}

function wpshd_vcita_get_email( $widget_params ) {
	return empty( $widget_params[ 'email' ] ) ? get_option( 'admin_email' ) : $widget_params[ 'email' ];
}

function wpshd_vcita_get_uid() {
	$wpshd_vcita_widget = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
	
	if ( isset( $wpshd_vcita_widget[ 'uid' ] ) && ! empty( $wpshd_vcita_widget[ 'uid' ] ) ) {
		if ( is_string( $wpshd_vcita_widget[ 'uid' ] ) || is_int( $wpshd_vcita_widget[ 'uid' ] ) ) {
			return $wpshd_vcita_widget[ 'uid' ];
		}
	}
	
	return WPSHD_VCITA_WIDGET_DEMO_UID;
}


function wpshd_vcita_check_need_to_reconnect( $wpshd_vcita_widget ) {
	$needs_reconnect = false;
	
	if ( is_array( $wpshd_vcita_widget ) && isset( $wpshd_vcita_widget[ 'migrated' ] ) ) {
		if ( $wpshd_vcita_widget[ 'migrated' ] && isset( $wpshd_vcita_widget[ 'uid' ] ) && ! empty( $wpshd_vcita_widget[ 'uid' ] ) && ( ! isset( $wpshd_vcita_widget[ 'business_id' ] ) || empty( $wpshd_vcita_widget[ 'business_id' ] ) || ( isset( $wpshd_vcita_widget[ 'business_id' ] ) && $wpshd_vcita_widget[ 'business_id' ] != $wpshd_vcita_widget[ 'uid' ] ) ) ) {
			$needs_reconnect = true;
		}
	}
	
	return $needs_reconnect;
}


function wpshd_vcita_is_demo_user() {
	return ( wpshd_vcita_get_uid() == WPSHD_VCITA_WIDGET_DEMO_UID );
}

function wpshd_vcita_get_page_edit_link( $page_id ) {
	$page = get_page( $page_id );
	
	return get_edit_post_link( $page_id );
}

function wpshd_vcita_clean_expert_data() {
	return wpshd_vcita_create_initial_parameters( true, (array) get_option( WPSHD_VCITA_WIDGET_KEY ) );
}

function wpshd_vcita_create_initial_parameters( $clean = false, $old_params ) {
	$arr = create_default_widget_data( $clean, $old_params );
	foreach ( $arr as $key => $val ) {
		$old_params[ $key ] = $val;
	}
	$arr = create_default_settings_data( $old_params );
	foreach ( $arr as $key => $val ) {
		$old_params[ $key ] = $val;
	}
	
	return $old_params;
}

function create_default_widget_data( $clean = false, array $old_params ) {
    
	if ( $clean ) {
		return array(
			'uid'                   => '',
			'name'                  => '',
			'email'                 => '',
			'migrated'              => isset( $old_params[ 'migrated' ] ) ? (bool) $old_params[ 'migrated' ] : false,
			'new_install'           => false,
			'version'               => WPSHD_VCITA_WIDGET_VERSION,
			'success'               => false,
			'business_id'           => '',
			'wp_id'                 => isset( $old_params[ 'wp_id' ] ) ? sanitize_text_field( $old_params[ 'wp_id' ] )
				: '',
			'calendar_page_active'  => false,
			'calendar_page_id'      => isset( $old_params[ 'calendar_page_id' ] )
				? sanitize_text_field( $old_params[ 'calendar_page_id' ] ) : '',
			'contact_page_active'   => false,
			'page_id'               => isset( $old_params[ 'page_id' ] )
				? sanitize_text_field( $old_params[ 'page_id' ] ) : '',
			'start_wizard_clicked'  => isset( $old_params[ 'start_wizard_clicked' ] )
				? intval( $old_params[ 'start_wizard_clicked' ] ) : 0,
			'migrated_popup_showed' => isset( $old_params[ 'migrated_popup_showed' ] )
				? (bool) $old_params[ 'migrated_popup_showed' ] : false
		);
	}
	else {
		$migrated = isset( $old_params[ 'migrated' ] ) && $old_params[ 'migrated' ];
		
		return array(
			'uid'                   => $migrated && isset( $old_params[ 'uid' ] )
				? sanitize_text_field( $old_params[ 'uid' ] ) : '',
			'name'                  => $migrated && isset( $old_params[ 'first_name' ] )
				? sanitize_text_field( $old_params[ 'first_name' ] ) : '',
			'email'                 => $migrated && isset( $old_params[ 'email' ] )
				? sanitize_email( $old_params[ 'email' ] ) : '',
			'migrated'              => $migrated,
			'new_install'           => isset( $old_params[ 'new_install' ] ) ? (bool) $old_params[ 'new_install' ]
				: false,
			'version'               => WPSHD_VCITA_WIDGET_VERSION,
			'success'               => false,
			'business_id'           => '',
			'wp_id'                 => isset( $old_params[ 'wp_id' ] ) ? sanitize_text_field( $old_params[ 'wp_id' ] )
				: '',
			'calendar_page_active'  => isset( $old_params[ 'calendar_page_active' ] )
				? (bool) $old_params[ 'calendar_page_active' ] : false,
			'calendar_page_id'      => isset( $old_params[ 'calendar_page_id' ] )
				? sanitize_text_field( $old_params[ 'calendar_page_id' ] ) : '',
			'contact_page_active'   => isset( $old_params[ 'contact_page_active' ] )
				? (bool) $old_params[ 'contact_page_active' ] : false,
			'page_id'               => isset( $old_params[ 'page_id' ] )
				? sanitize_text_field( $old_params[ 'page_id' ] ) : '',
			'start_wizard_clicked'  => isset( $old_params[ 'start_wizard_clicked' ] )
				? intval( $old_params[ 'start_wizard_clicked' ] ) : 0,
			'migrated_popup_showed' => isset( $old_params[ 'migrated_popup_showed' ] )
				? (bool) $old_params[ 'migrated_popup_showed' ] : false
		);
	}
}


function create_default_settings_data( array $old_params ) {
	return array(
		'vcita_design' => isset( $old_params[ 'vcita_design' ] )
			? intval( $old_params[ 'vcita_design' ] )
			: ( isset( $old_params[ 'migrated' ] ) && isset( $old_params[ 'uid' ] ) && $old_params[ 'migrated' ] && $old_params[ 'uid' ]
				? 1 : 0 ),
		'widget_img'   => isset( $old_params[ 'widget_img' ] ) ? esc_url( $old_params[ 'widget_img' ] ) : '',
		'widget_title' => isset( $old_params[ 'widget_title' ] ) ? sanitize_text_field( $old_params[ 'widget_title' ] )
			: '',
		'show_on_site' => isset( $old_params[ 'show_on_site' ] ) ? intval( $old_params[ 'show_on_site' ] ) : 1,
		'widget_show'  => isset( $old_params[ 'widget_show' ] ) ? intval( $old_params[ 'widget_show' ] ) : 0,
		'btn_text'     => isset( $old_params[ 'btn_text' ] ) ? sanitize_text_field( $old_params[ 'btn_text' ] ) : '',
		'btn_color'    => isset( $old_params[ 'btn_color' ] ) ? sanitize_hex_color( $old_params[ 'btn_color' ] )
			: '#01dcf7',
		'txt_color'    => isset( $old_params[ 'txt_color' ] ) ? sanitize_hex_color( $old_params[ 'txt_color' ] )
			: '#ffffff',
		'hover_color'  => isset( $old_params[ 'hover_color' ] ) ? sanitize_hex_color( $old_params[ 'hover_color' ] )
			: '#01dcf7',
		'widget_text'  => isset( $old_params[ 'widget_text' ] )
			? sanitize_textarea_field( $old_params[ 'widget_text' ] ) : '',
	);
	
}

function wpshd_vcita_default_if_non( $arr_obj, $index, $default_value = '' ) {
	return isset( $arr_obj ) && isset( $arr_obj[ $index ] ) ? $arr_obj[ $index ] : $default_value;
}

function wpshd_vcita_add_contact($atts) {
	$wpshd_vcita_widget = (array) get_option(WPSHD_VCITA_WIDGET_KEY);
	$id = WPSHD_VCITA_WIDGET_DEMO_UID;
	
	if (isset($wpshd_vcita_widget['uid']) && !empty($wpshd_vcita_widget['uid'])) {
		$id = sanitize_text_field($wpshd_vcita_widget['uid']);
	}
	
	$atts = shortcode_atts(array(
		'type'   => 'contact',
		'width'  => '100%',
		'height' => '450px',
	), $atts);
	
	$width  = sanitize_text_field($atts['width']);
	$height = sanitize_text_field($atts['height']);
	
	return wpshd_vcita_create_embed_code($atts['type'], $id, $width, $height);
}



function wpshd_vcita_add_calendar($atts) {
	$wpshd_vcita_widget = (array) get_option(WPSHD_VCITA_WIDGET_KEY);
	$id = WPSHD_VCITA_WIDGET_DEMO_UID;
	
	if (isset($wpshd_vcita_widget['uid']) && !empty($wpshd_vcita_widget['uid'])) {
		$id = $wpshd_vcita_widget['uid'];
	}
	
	$atts = shortcode_atts(array(
		'type'   => 'scheduler',
		'width'  => '100%',
		'height' => '500px',
	), $atts);
	
	return wpshd_vcita_create_embed_code($atts['type'], $id, $atts['width'], $atts['height']);
}

function wpshd_vcita_add_contact_page($by_ajax = false) {
	if (!$by_ajax && ((defined('DOING_CRON') && DOING_CRON) || (defined('DOING_AJAX') && DOING_AJAX))) {
		return;
	}
	
	$existing_page = get_page_by_path('contact-form');
	if ($existing_page) {
		return $existing_page->ID;
	}
	
	return wp_insert_post(array(
		'post_name'      => 'contact-form',
		'post_title'     => __('Contact Us', 'meeting-scheduler-by-vcita'),
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'comment_status' => 'closed',
		'post_content'   => '[' . WPSHD_VCITA_WIDGET_SHORTCODE . ']'
	));
}


function wpshd_wpshd_vcita_add_calendar_page($by_ajax = false) {
	// if (!$by_ajax && (true === DOING_CRON || true === DOING_AJAX)) return;
	if (!$by_ajax && ((defined('DOING_CRON') && DOING_CRON) || (defined('DOING_AJAX') && DOING_AJAX))) {
		return;
	}
	
	$existing_page = get_page_by_path('appointment-booking');
	if ($existing_page) {
		return $existing_page->ID;
	}
	
	return wp_insert_post(array(
		'post_name'      => 'appointment-booking',
		'post_title'     => __('Book Appointment', 'meeting-scheduler-by-vcita'),
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'comment_status' => 'closed',
		'post_content'   => '[' . WPSHD_VCITA_CALENDAR_WIDGET_SHORTCODE . ']'
	));
}


function wpshd_vcita_create_embed_code($type, $uid, $width, $height) {
	if (!in_array($type, ['contact', 'calendar'])) {
		return '';
	}
	
	if (isset($uid) && !empty($uid)) {
		$code = get_transient('embed_code' . $type . $uid . $width . $height);
		
		if (!$code) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, "http://" . WPSHD_VCITA_SERVER_URL . "/api/experts/" . urlencode($uid) . "/embed_code?type=" . urlencode($type) . "&width=" . urlencode($width) . "&height=" . urlencode($height));
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_PROXY, '');
			$data = curl_exec($ch);
			curl_close($ch);
			
			$data = json_decode($data, true);
			
			if (isset($data['code'])) {
				$code = html_entity_decode($data['code']);
				// Set the embed code to be cached for an hour
				set_transient('embed_code' . $type . $uid . $width . $height, $code, 3600);
			} else {
				// Проверка на валидность ширины и высоты
				$width = is_numeric($width) ? intval($width) : 100;
				$height = is_numeric($height) ? intval($height) : 450;
				
				$code = "<iframe frameborder='0' src='//" . WPSHD_VCITA_SERVER_URL . "/" . urlencode($uid) . "/" . $type . "/' width='" . $width . "' height='" . $height . "'></iframe>";
			}
		}
	} else {
		return ''; // Если uid не задан, вернуть пустую строку
	}
	
	return $_SERVER['HTTPS'] && $_SERVER['HTTPS'] == 'on' ? str_replace('http://', 'https://', $code) : $code;
}


function wpshd_vcita_make_sure_page_published($wpshd_vcita_widget, $by_ajax = false) {
	$page_id = wpshd_vcita_default_if_non($wpshd_vcita_widget, 'page_id');
	
	if (!is_numeric($page_id) || $page_id < 0) {
		$page_id = wpshd_vcita_add_contact_page($by_ajax);
	} else {
		$page = get_page($page_id);
		
		if (empty($page)) {
			$page = get_page_by_title(__('Contact Us', 'meeting-scheduler-by-vcita'));
			if ($page) {
				$page_id = $page->ID;
			} else {
				$page_id = wpshd_vcita_add_contact_page($by_ajax);
			}
		} elseif ($page->{"post_status"} == "trash") {
			wp_untrash_post($page_id);
		} elseif ($page->{"post_status"} != "publish") {
			$page_id = wpshd_vcita_add_contact_page($by_ajax);
		}
	}
	
	$wpshd_vcita_widget['page_id'] = $page_id;
	$wpshd_vcita_widget['contact_page_active'] = 'true';
	update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
	
	return $wpshd_vcita_widget;
}


function wpshd_vcita_get_contact_url( $whshd_vcita_widget ) {
	$page_id = wpshd_vcita_default_if_non($whshd_vcita_widget, 'page_id');
	
	if (!is_numeric($page_id) || $page_id < 0) {
		return '/contact-form';
	}
	
	$page = get_page($page_id);
	$page_url = '/contact-form';
	
	if (empty($page)) {
		$page = get_page_by_title(__('Contact Us', 'meeting-scheduler-by-vcita'));
		if (!empty($page)) {
			return get_post_permalink($page->ID);
		}
		
		return $page_url;
	} else {
		return get_post_permalink($page->ID);
	}
}

function wpshd_vcita_get_schedule_url( $whshd_vcita_widget ) {
	$page_id = wpshd_vcita_default_if_non($whshd_vcita_widget, 'calendar_page_id');
	
	if (!is_numeric($page_id) || $page_id < 0) {
		return '/appointment-booking';
	}
	
	$page = get_page($page_id);
	$page_url = '/appointment-booking';
	
	if (empty($page)) {
		$page = get_page_by_title(__('Book Appointment', 'meeting-scheduler-by-vcita'));
		if (!empty($page)) {
			return get_post_permalink($page->ID);
		}
		
		return $page_url;
	} else {
		return get_post_permalink($page->ID);
	}
}

function wpshd_vcita_make_sure_calendar_page_published( $wpshd_vcita_widget, $by_ajax = false ) {
	
	$page_id = wpshd_vcita_default_if_non( $wpshd_vcita_widget, 'calendar_page_id' );
	$page    = get_page( $page_id );
	
	if ( empty( $page ) ) {
		$page    = get_page_by_title( __( 'Book Appointment', 'meeting-scheduler-by-vcita' ) );
		$page_id = $page->ID;
	}
	
	if ( empty( $page ) ) {
		$page_id = wpshd_wpshd_vcita_add_calendar_page( $by_ajax );
	}
    elseif ( $page->{"post_status"} == "trash" ) {
		wp_untrash_post( $page_id );
	}
    elseif ( $page->{"post_status"} != "publish" ) {
		$page_id = wpshd_wpshd_vcita_add_calendar_page();
	}
	
	$wpshd_vcita_widget[ 'calendar_page_id' ]     = $page_id;
	$wpshd_vcita_widget[ 'calendar_page_active' ] = 'true';
	update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
	
	return $wpshd_vcita_widget;
}

function wpshd_vcita_is_contact_page_available( $wpshd_vcita_widget ) {
	if ( ! isset( $wpshd_vcita_widget[ 'page_id' ] ) || empty( $wpshd_vcita_widget[ 'page_id' ] ) ) {
		$page = get_page_by_title( __( 'Contact Us', 'meeting-scheduler-by-vcita' ) );
		
		if ( ! empty( $page ) && $page->{"post_status"} == "publish" ) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		$page_id = $wpshd_vcita_widget[ 'page_id' ];
		$page    = get_page( $page_id );
		
		return ! empty( $page ) && $page->{"post_status"} == "publish";
	}
}

function wpshd_vcita_is_calendar_page_available( $wpshd_vcita_widget ) {
	if ( ! isset( $wpshd_vcita_widget[ 'calendar_page_id' ] ) || empty( $wpshd_vcita_widget[ 'calendar_page_id' ] ) ) {
		$page = get_page_by_title( __( 'Book Appointment', 'meeting-scheduler-by-vcita' ) );
		
		if ( ! empty( $page ) && $page->{"post_status"} == "publish" ) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		$page_id = $wpshd_vcita_widget[ 'calendar_page_id' ];
		$page    = get_page( $page_id );
		
		return ! empty( $page ) && $page->{"post_status"} == "publish";
	}
}

function wpshd_vcita_trash_contact_page( $widget_params ) {
	if ( isset( $widget_params[ 'page_id' ] ) && ! empty( $widget_params[ 'page_id' ] ) ) {
		$page_id = $widget_params[ 'page_id' ];
		$page    = get_page( $page_id );
		if ( ! empty( $page ) && $page->{"post_status"} == "publish" ) {
			wp_trash_post( $page_id );
		}
	}
	else {
		$page = get_page_by_title( __( 'Contact Us', 'meeting-scheduler-by-vcita' ) );
		if ( ! empty( $page ) && $page->{"post_status"} == "publish" ) {
			wp_trash_post( $page->ID );
		}
	}
}

function wpshd_vcita_trash_current_calendar_page( $widget_params ) {
	if ( isset( $widget_params[ 'calendar_page_id' ] ) && ! empty( $widget_params[ 'calendar_page_id' ] ) ) {
		$page_id = $widget_params[ 'calendar_page_id' ];
		$page    = get_page( $page_id );
		if ( ! empty( $page ) && $page->{"post_status"} == "publish" ) {
			wp_trash_post( $page_id );
		}
	}
	else {
		$page = get_page_by_title( __( 'Book Appointment', 'meeting-scheduler-by-vcita' ) );
		if ( ! empty( $page ) && $page->{"post_status"} == "publish" ) {
			wp_trash_post( $page->ID );
		}
	}
}

function wpshd_vcita_widget_content( $args ) {
	echo wpshd_vcita_add_contact();
}

function wpshd_vcita_widget_admin() {
	wp_enqueue_style('vcita-widgets-style', plugins_url('assets/style/widgets_page.css', __FILE__));
	$wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
	$uid = isset($wpshd_vcita_widget['uid']) ? sanitize_text_field($wpshd_vcita_widget['uid']) : ''; // Очистка uid
	$email = isset($wpshd_vcita_widget['email']) ? sanitize_email($wpshd_vcita_widget['email']) : ''; // Очистка email
	?>
    <script type="text/javascript">
      jQuery(function ($) {
        $('#vcita_config #start-login').click(function (ev) {
          ev.preventDefault();
          ev.stopPropagation();
          VcitaMixpman.track('wp_sched_login_vcita');
          VcitaUI.openAuthWin(false, false);
        });
        $('#vcita_config #switch-account').click(function (ev) {
          VcitaMixpman.track('wp_sched_logout');
          $.post(`${window.$_ajaxurl}?action=vcita_logout`);
          VcitaUI.openAuthWin(false, true);
        });
        $('#vcita_config .preview').click(function (e) {
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
				<?php echo __('To use vCita\'s sidebar please', 'meeting-scheduler-by-vcita'); ?>
                <button class="vcita__btn__blue" id="start-login">
					<?php echo __('Connect to vcita', 'meeting-scheduler-by-vcita'); ?>
                </button>
            </h3>
		<?php } else { ?>
            <h3>
				<?php echo __('Contact requests will be sent to this email:', 'meeting-scheduler-by-vcita'); ?>
            </h3>
            <div>
                <input class="txt_input" type="text" disabled="disabled" value="<?php echo esc_attr($email); ?>"/> <!-- Используем esc_attr для вывода email -->
                <a href="javascript:void(0)" id="switch-account">
					<?php echo __('Change Email', 'meeting-scheduler-by-vcita'); ?>
                </a>
            </div>
            <div class="no-space">
                <a href="https://app.<?php echo esc_url(WPSHD_VCITA_SERVER_BASE); ?>/app/my-livesite?section=website-widgets" target="_blank">
					<?php echo __('Edit', 'meeting-scheduler-by-vcita'); ?>
                </a>
                <a class="preview" href="//<?php echo esc_url(WPSHD_VCITA_SERVER_URL); ?>/contact_widget?v=<?php echo esc_attr(wpshd_vcita_get_uid()); ?>&ver=2" data-width="200" data-height="500">
					<?php echo __('Preview', 'meeting-scheduler-by-vcita'); ?>
                </a>
            </div>
		<?php } ?>
    </div>
	<?php
}
