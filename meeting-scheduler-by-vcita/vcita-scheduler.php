<?php
/*
Plugin Name: Appointment Booking and Online Scheduling
Plugin URI: https://www.vcita.com
Description: This plugin shows your free time slot on your blog and allows you to book appointments with your clients 24x7x365. Very easy Ajax interface. Easy to setup and can be controlled completely from powerful admin area.
Version: 4.4.6
Author: vCita.com
Author URI: https://www.vcita.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: meeting-scheduler-by-vcita
Domain Path: /languages
*/

$wpshd_plug_name = __( 'Appointment Booking and Online Scheduling', 'meeting-scheduler-by-vcita' );
$wpshd_plug_desc = __( 'This plugin shows your free time slot on your blog and allows you to book appointments with your clients 24x7x365. Very easy Ajax interface. Easy to setup and can be controlled completely from powerful admin area.', 'meeting-scheduler-by-vcita' );

function vcita_enqueue_admin_scripts() {
	
	wp_enqueue_script( 'vcita-pc', plugins_url( 'assets/js/pc_v.js', __FILE__ ) );
	wp_enqueue_script( 'vcita-ui', plugins_url( 'assets/js/utils_v.js', __FILE__ ) );
	wp_enqueue_script( 'vcita-mixpman', plugins_url( 'assets/js/mixpanel_v.js', __FILE__ ) );
	
	wp_register_style( 'vcita-style', plugins_url( 'assets/style/style_v.css', __FILE__ ) );
	wp_enqueue_style( 'vcita-style' );
	wp_enqueue_script( 'jquery' );
    
    wp_localize_script(
	    'vcita-pc',
	    'vcitaSchedulerData',
	    [
		    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		    'nonce'   => wp_create_nonce( 'wpshd_vcita_nonce_action' ),
	    ]
    );
}

function wpshd_vcita_scheduler_other_plugin_installed_warning() {
	echo "<div id='vcita-warning' class='error'><p><b>" . __( "vCita Plugin is already installed", 'meeting-scheduler-by-vcita' ) . "</b>" . __( ', please remove "<b>Appointment Booking and Online Scheduling</b>" and use the available one', 'meeting-scheduler-by-vcita' ) . "</p></div>";
}

/**
 * Check if the requested plugin is already available
 */
function wpshd_vcita_scheduler_check_plugin_available() {
	// Check if vCita plugin already installed.
	$active_plugins = get_option( 'active_plugins' );
	$found          = array();
	
	foreach ( $active_plugins as $filename ) {
		if ( strpos( $filename, 'by-vcita' ) !== false && ( strpos( $filename, 'meeting-scheduler-by-vcita' ) === false || strpos( $filename, 'meeting-scheduler-by-vcita' ) > 0 ) ) {
			$found[] = $filename;
		}
	}
	
	return $found;
}

function vcita_activate_func() {
	$wpshd_vcita_widget = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
	if ( ! isset( $wpshd_vcita_widget[ 'wp_id' ] ) ) {
		$wpshd_vcita_widget[ 'wp_id' ] = '';
	}
	if ( ! isset( $wpshd_vcita_widget[ 'email' ] ) ) {
		$wpshd_vcita_widget[ 'email' ] = '';
	}
	
	if ( ! isset( $_GET[ 'page' ] ) || ! preg_match( '/' . WPSHD_VCITA_WIDGET_UNIQUE_ID . '\//', $_GET[ 'page' ] ) ) {
		
		wp_enqueue_script( 'vcita-mixpman', plugins_url( 'assets/js/mixpanel_v.js', __FILE__ ) );
		
		echo '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
          if (!window.VcitaMixpman) {
            window.VcitaMixpman = new MixpMan("78aa39b3aa49594f172cfccda537ef1a", "' . $wpshd_vcita_widget[ 'wp_id' ] . '", "' . $wpshd_vcita_widget[ 'email' ] . '");
          }
        });
        
        function wpshd_ntf_dismiss() {
          window.VcitaMixpman.track("wp_sched_close_widget_notification", { created_at: new Date().toISOString() });
          jQuery.get(`${window.$_ajaxurl}?action=vcita_dismiss&dismiss=true`);
        }
        
        function wpshd_ntf_dismiss_switch() {
          window.VcitaMixpman.track("wp_sched_close_connect_notification", { created_at: new Date().toISOString() });
          jQuery.get(`${window.$_ajaxurl}?action=vcita_dismiss&dismiss_switch=true`);
        }
        
        function wpshd_ntf_connect_click() {
          window.VcitaMixpman.track("wp_sched_clicked_connect_notification", { created_at: new Date().toISOString() });
        }
        
        function wpshd_ntf_turn_on_click() {
          jQuery.get(`${window.$_ajaxurl}?action=vcita_dismiss&switch_on=true`);
          window.VcitaMixpman.track("wp_sched_clicked_widget_notification", { created_at: new Date().toISOString() });
        }
      </script>';
		
		if ( isset( $wpshd_vcita_widget[ 'dismiss' ] ) && $wpshd_vcita_widget[ 'dismiss' ] && ! $wpshd_vcita_widget[ 'uid' ] ) {
			if ( isset( $wpshd_vcita_widget[ 'dismiss_time' ] ) ) {
				if ( (int) ( microtime( true ) - $wpshd_vcita_widget[ 'dismiss_time' ] ) / 60 / 60 >= 48 ) {
					$wpshd_vcita_widget[ 'dismiss' ] = false;
				}
			}
		}
		
		if ( isset( $wpshd_vcita_widget[ 'dismiss_switch' ] ) && $wpshd_vcita_widget[ 'dismiss_switch' ] && ! $wpshd_vcita_widget[ 'show_on_site' ] ) {
			if ( isset( $wpshd_vcita_widget[ 'dismiss_switch_time' ] ) ) {
				if ( (int) ( microtime( true ) - $wpshd_vcita_widget[ 'dismiss_switch_time' ] ) / 60 / 60 >= 48 ) {
					$wpshd_vcita_widget[ 'dismiss_switch' ] = false;
				}
			}
		}
		
		if ( ! isset( $wpshd_vcita_widget[ 'dismiss_time' ] ) ) {
			$wpshd_vcita_widget[ 'dismiss_time' ] = microtime( true );
			update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
		}
		
		if ( ! isset( $wpshd_vcita_widget[ 'dismiss_switch_time' ] ) ) {
			$wpshd_vcita_widget[ 'dismiss_switch_time' ] = microtime( true );
			update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
		}
		
		if ( ( ! isset( $wpshd_vcita_widget[ 'dismiss' ] ) || ! $wpshd_vcita_widget[ 'dismiss' ] ) && ( ! isset( $wpshd_vcita_widget[ 'uid' ] ) || ! $wpshd_vcita_widget[ 'uid' ] ) ) {
			echo '<style>
        .wpschd_admin_notice{position:relative;display:flex;flex-wrap:wrap;background:#fff;margin-top:10px;width:calc(100% - 40px);border-radius:2px;padding:10px;box-shadow:0 0 4px 0 #ccc;align-items:flex-start}.wpschd_admin_notice-image{max-width:150px;padding-right:20px}.wpschd_admin_notice-text{padding:0 20px 0 0;flex-grow:1}.wpschd_admin_notice-text header{font-size:16px;font-weight:bold;margin-bottom:20px}.wpschd_admin_notice-text article{font-size:14px;line-height:120%;margin-bottom:20px}.vcita__btn__blue{min-width:125px;font-size:16px;font-weight:600;color:#fff;padding:9px 16px;background-color:#00dcf7;border:none!important;border-radius:0!important;outline:none!important;cursor:pointer;transition:.3s;-webkit-backface-visibility:hidden;backface-visibility:hidden;text-decoration:none}.vcita__btn__blue:hover{background-color:#3973ab}.wpschd_admin_notice_close{width:16px;height:16px;border:2px solid rgba(0,0,0,0.5);border-radius:50%;display:inline-block;position:absolute;top:5px;right:5px;cursor:pointer}.wpschd_admin_notice_close:before,.wpschd_admin_notice_close:after{content:\'\';position:absolute;top:calc(50% - 1px);left:20%;width:60%;height:2px;background-color:rgba(0,0,0,0.5);transition:transform 0.5s}.wpschd_admin_notice_close:before{transform:rotate(45deg)}.wpschd_admin_notice_close:after{transform:rotate(-45deg)}.wpschd_admin_notice_close:hover:before{transform:rotate(135deg)}.wpschd_admin_notice_close:hover:after{transform:rotate(-135deg)}
      </style>';
			
			echo '<div class="wpschd_admin_notice">
        <div class="wpschd_admin_notice_close" onclick="wpshd_ntf_dismiss();this.parentNode.remove()"></div>
        <div class="wpschd_admin_notice-image"><img src="' . WPSHD_VCITA_ASSETS_PATH . '/images/icon-256x256.png"/></div>
        <div class="wpschd_admin_notice-text">
          <header>' . __( 'Your online booking plugin is almost ready…!', 'meeting-scheduler-by-vcita' ) . '</header>
          <article>' . __( 'It’s great to see you added vcita’s online scheduling plugin to your site.<br>To get your plugin up and running, just connect vcita to wordpress with a few easy steps.', 'meeting-scheduler-by-vcita' ) . '</article>
          <footer>
            <a class="vcita__btn__blue" onclick="wpshd_ntf_connect_click()" href="' . get_admin_url( '', '', 'admin' ) . 'admin.php?page=' . WPSHD_VCITA_WIDGET_UNIQUE_ID . '/vcita-settings-functions.php&show_login=true">' . __( 'Connect to vcita', 'meeting-scheduler-by-vcita' ) . '</a>
          </footer>
        </div>
      </div>';
		}
		else if ( ( ! isset( $wpshd_vcita_widget[ 'dismiss_switch' ] ) || ! $wpshd_vcita_widget[ 'dismiss_switch' ] ) && ! $wpshd_vcita_widget[ 'show_on_site' ] ) {
			echo '<style>
        .wpschd_admin_notice{position:relative;display:flex;flex-wrap:wrap;background:#fff;margin-top:10px;width:calc(100% - 40px);border-radius:2px;padding:10px;box-shadow:0 0 4px 0 #ccc;align-items:flex-start}.wpschd_admin_notice-image{max-width:150px;padding-right:20px}.wpschd_admin_notice-text{padding:0 20px 0 0;flex-grow:1}.wpschd_admin_notice-text header{font-size:16px;font-weight:bold;margin-bottom:20px}.wpschd_admin_notice-text article{font-size:14px;line-height:120%;margin-bottom:20px}.vcita__btn__blue{min-width:125px;font-size:16px;font-weight:600;color:#fff;padding:9px 16px;background-color:#00dcf7;border:none!important;border-radius:0!important;outline:none!important;cursor:pointer;transition:.3s;-webkit-backface-visibility:hidden;backface-visibility:hidden;text-decoration:none}.vcita__btn__blue:hover{background-color:#3973ab}.wpschd_admin_notice_close{width:16px;height:16px;border:2px solid rgba(0,0,0,0.5);border-radius:50%;display:inline-block;position:absolute;top:5px;right:5px;cursor:pointer}.wpschd_admin_notice_close:before,.wpschd_admin_notice_close:after{content:\'\';position:absolute;top:calc(50% - 1px);left:20%;width:60%;height:2px;background-color:rgba(0,0,0,0.5);transition:transform 0.5s}.wpschd_admin_notice_close:before{transform:rotate(45deg)}.wpschd_admin_notice_close:after{transform:rotate(-45deg)}.wpschd_admin_notice_close:hover:before{transform:rotate(135deg)}.wpschd_admin_notice_close:hover:after{transform:rotate(-135deg)}
      </style>';
			
			echo '<div class="wpschd_admin_notice">
        <div class="wpschd_admin_notice_close" onclick="wpshd_ntf_dismiss_switch();this.parentNode.remove()"></div>
        <div class="wpschd_admin_notice-image"><img src="' . WPSHD_VCITA_ASSETS_PATH . '/images/icon-256x256.png"/></div>
        <div class="wpschd_admin_notice-text">
          <header>' . __( 'Turn on the booking widget', 'meeting-scheduler-by-vcita' ) . '</header>
          <article>' . __( 'Your clients can not book meeting online with you at the moment. Please turn the booking<br>widget on to enable your clients to book online with you', 'meeting-scheduler-by-vcita' ) . '</article>
          <footer>
            <a class="vcita__btn__blue" href="javascript:void(0)" onclick="wpshd_ntf_turn_on_click();this.closest(\'.wpschd_admin_notice\').remove()">' . __( 'Turn on Widget', 'meeting-scheduler-by-vcita' ) . '</a>
          </footer>
        </div>
      </div>';
		}
	}
	
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	
	$all_plugins = get_plugins();
	$class_str   = '';
	
	foreach ( $all_plugins as $wp_file => $wp_plugin ) {
		if ( strpos( $wp_file, 'by-vcita' ) !== false && ( strpos( $wp_file, 'meeting-scheduler-by-vcita' ) === false || strpos( $wp_file, 'meeting-scheduler-by-vcita' ) > 0 ) ) {
			$class_str .= ',.edit[aria-label*="' . $wp_plugin[ 'Name' ] . '"]';
		}
	}
	
	$prefix    = '<b>' . WPSHD_VCITA_WIDGET_PLUGIN_NAME . '</b>';
	$class     = "error notice is-dismissible";
	$class_str = substr( $class_str, 1 );
	
	if ( $class_str ) { ?>
        <script type="text/javascript">
          document.addEventListener('DOMContentLoaded', function () {
            const plugs = document.querySelectorAll('<?php echo $class_str ?>')
            const pl = plugs.length
            
            for (let i = 0; i < pl; i++) {
              plugs[i].onclick = function (ev) {
                ev.preventDefault()
                ev.stopPropagation()
                
                document.querySelector('.wp-header-end')
                  .insertAdjacentHTML('afterend', '<div class="<?php echo $class ?>">' +
                    '<p><?php echo __( 'You cannot activate this plugin, because you are using', 'meeting-scheduler-by-vcita' ) ?> <?php echo $prefix ?></p>' +
                    '<button type="button" onclick="this.parentNode.remove()" class="notice-dismiss"><span class="screen-reader-text">' +
                    '<?php echo __( 'Dismiss this notice.', 'meeting-scheduler-by-vcita' ) ?>' +
                    '</span></button></div>')
                
                window.scrollTo(0, 0)
              }
            }
          })
        </script>
	<?php }
}

function vcita_admin_footer_script() {
	global $pagenow;
	$wpshd_vcita_widget = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
	
	if ( 'plugins.php' === $pagenow ) {
		echo '<script type="text/javascript">
        function vcita_send_deactivate_feedback(ev, data) {
          ev.preventDefault();
          ev.stopPropagation();
        
          if (data.close) {
            tb_remove();
            return;
          }
        
          if (data.msg && window.VcitaMixpman) {
            VcitaMixpman.track("wp_sched_deactivation", { reason: data.msg });
          } else if (data.el != undefined && window.VcitaMixpman) {
            if (data.el == null) {
              VcitaMixpman.track("wp_sched_deactivation", { reason: "no reason" });
            } else VcitaMixpman.track("wp_sched_deactivation", { reason: data.el.textContent });
          }
        
          if (window.vcita_deactivate_url != undefined && window.vcita_deactivate_url != null) {
            window.location.href = window.vcita_deactivate_url;
          }
        }
        
        function wpshd_vcita_snooze_rate(e, _this) {
          e.preventDefault();
          wpshd_vcita_put_lsr_data("' . $wpshd_vcita_widget[ 'uid' ] . '", {
            dismiss: false,
            wait: true,
            date: new Date().getTime(),
            version: "' . WPSHD_VCITA_WIDGET_VERSION . '"
          });
        }
        
        function wpshd_vcita_put_lsr_data(key, val) {
          let rdata = {};
          try {
            rdata = localStorage.getItem("wpshd_rate_state");
            if (rdata !== null) {
              rdata = JSON.parse(rdata);
            } else {
              rdata = {};
            }
          } catch (err) {
            rdata = {};
          }
          rdata[key] = val;
          localStorage.setItem("wpshd_rate_state", JSON.stringify(rdata));
        }
        
        function wpshd_vcita_goToRate(e, _this, open = true) {
          e.preventDefault();
          if (open) {
            window.open("https://wordpress.org/support/plugin/meeting-scheduler-by-vcita/reviews/#new-post", "_blank");
          }
          wpshd_vcita_put_lsr_data("' . $wpshd_vcita_widget[ 'uid' ] . '", {
            dismiss: true,
            wait: false,
            date: new Date().getTime(),
            version: "' . WPSHD_VCITA_WIDGET_VERSION . '"
          });
        }
        
        (function () {
          jQuery("[data-slug=\'meeting-scheduler-by-vcita\'] .deactivate a").click(function () {
            window.vcita_deactivate_url = this.getAttribute("href");
            tb_show("' . __( 'Quick feedback', 'meeting-scheduler-by-vcita' ) . '", "/?TB_inline&inlineId=vcita__deactivate__modal-container");
            jQuery("#TB_window").addClass("vcita_tb_window");
            jQuery("#TB_ajaxContent").addClass("vcita_tb_content");
            return false;
          });';
		
		if ( $wpshd_vcita_widget[ 'uid' ] ) {
			echo '
          try {
            let rdata = localStorage.getItem("wpshd_rate_state");
            if (rdata !== null) {
              rdata = JSON.parse(rdata);
              if (!rdata["' . $wpshd_vcita_widget[ 'uid' ] . '"]) {
                rdata["' . $wpshd_vcita_widget[ 'uid' ] . '"] = {
                  dismiss: false,
                  wait: false,
                  date: new Date().getTime(),
                  version: "' . WPSHD_VCITA_WIDGET_VERSION . '"
                }
              }
            } else {
              rdata = {
                "' . $wpshd_vcita_widget[ 'uid' ] . '": {
                  dismiss: false,
                  wait: false,
                  date: new Date().getTime(),
                  version: "' . WPSHD_VCITA_WIDGET_VERSION . '"
                }
              };
            }
            const rd = rdata["' . $wpshd_vcita_widget[ 'uid' ] . '"];
            if (rd.dismiss && rd.version === "' . WPSHD_VCITA_WIDGET_VERSION . '") return false;
            if (rd.wait) {
              const day = 24 * 60 * 60 * 1000;
              const diffDays = Math.round(Math.abs((rd.date - new Date().getTime()) / day));
              if (diffDays < 2) return false;
            }
            document.addEventListener("DOMContentLoaded", () => {
              const txt = `<div class="notice notice-info" style="position:relative">
                <button type="button" class="notice-dismiss" onclick="this.parentNode.remove()"></button>
                <div style="font-size:20px;font-weight:bold;margin-top:10px"><img style="vertical-align:middle" src="' . plugins_url( WPSHD_VCITA_WIDGET_UNIQUE_ID . '/images/settings.png' ) . '"/>&nbsp;&nbsp;' . addslashes( __( 'Enjoying vcita?', 'meeting-scheduler-by-vcita' ) ) . '</div>
                <div style="font-size:16px;margin-top:10px">
                <div>' . addslashes( __( 'Can you please help us with a BIG favor and rate vcita 5 stars.', 'meeting-scheduler-by-vcita' ) ) . '</div>
                <div style="text-align:left;margin:10px 0">
                <button class="button-primary" onclick="wpshd_vcita_goToRate(event,this);this.closest(\'.notice\').remove()">
                ' . addslashes( __( 'Rate 5 Stars', 'meeting-scheduler-by-vcita' ) ) . '</button>
                &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" style="text-decoration:none" onclick="wpshd_vcita_snooze_rate(event,this);this.closest(\'.notice\').remove()">' . addslashes( __( 'maybe later', 'meeting-scheduler-by-vcita' ) ) . '</a>
                &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" style="text-decoration:none" onclick="wpshd_vcita_goToRate(event,this,false);this.closest(\'.notice\').remove()">' . addslashes( __( 'already rated', 'meeting-scheduler-by-vcita' ) ) . '</a>
                </div></div></div>`;
              document.querySelector(".wp-header-end").insertAdjacentHTML("afterend", txt);
            })
          } catch (e) {
            if (window.WPSHD_VCITA_DEBUG) console.log(e)
          }';
		}
		echo '
        }())
      </script>';
		
		wp_register_style( 'vcita-plugins-page-style', plugins_url( 'assets/style/plugins_page_v.css', __FILE__ ) );
		
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'vcita-plugins-page-script' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'vcita-plugins-page-style' );
		
		wp_enqueue_script( 'vcita-mixpman', plugins_url( 'assets/js/mixpanel_v.js', __FILE__ ) );
		
		echo '<script type="text/javascript">
      document.addEventListener("DOMContentLoaded", function () {  
        if (!window.VcitaMixpman) {
          window.VcitaMixpman = new MixpMan("78aa39b3aa49594f172cfccda537ef1a", "' . $wpshd_vcita_widget[ 'wp_id' ] . '", "' . $wpshd_vcita_widget[ 'email' ] . '");
        }});' .
			
			( $wpshd_vcita_widget[ 'just_activated' ]
				? 'window.setTimeout(function(){VcitaMixpman.track("wp_sched_activate")},2000)' : '' ) .
			
			'</script>
      <div id="vcita__deactivate__modal-container" style="display:none;">
        <form id="vcita__deactivate__modal-container-inner">
          <h3>' . __( 'If you have a moment, please let us know why you are deactivating:', 'meeting-scheduler-by-vcita' ) . '</h3>
          <section>
            <div><input name="reason" type="radio"><label>' . __( 'The plugin didn\'t work', 'meeting-scheduler-by-vcita' ) . '</label></div>
            <div><input name="reason" type="radio"><label>' . __( 'I don\'t like to share this information with you', 'meeting-scheduler-by-vcita' ) . '</label></div>
            <div><input name="reason" type="radio"><label>' . __( 'It\'s a temporary deactivation. I\'m just debugging an issue', 'meeting-scheduler-by-vcita' ) . '</label></div>
            <div><input name="reason" type="radio"><label>' . __( 'I found a better plugin', 'meeting-scheduler-by-vcita' ) . '</label></div>
            <div><input name="reason" type="radio"><label>' . __( 'Other', 'meeting-scheduler-by-vcita' ) . '</label></div>
          </section>
          <section>
            <button onclick="vcita_send_deactivate_feedback(event,{el:document.querySelector(\'#vcita__deactivate__modal-container-inner :checked ~ label\')})">' . __( 'Skip & deactivate', 'meeting-scheduler-by-vcita' ) . '</button>
            <button onclick="vcita_send_deactivate_feedback(event,{msg:\'support\'})">' . __( 'We can help: Support Service', 'meeting-scheduler-by-vcita' ) . '</button>
            <button onclick="vcita_send_deactivate_feedback(event,{close:true})"><b>' . __( 'Cancel', 'meeting-scheduler-by-vcita' ) . '</b></button>
          </section>
        </form>
      </div>';
		
		if ( $wpshd_vcita_widget[ 'just_activated' ] ) {
			$wpshd_vcita_widget[ 'just_activated' ] = false;
			update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
		}
	}
}

function vcita_set_rest() {
	
	register_rest_route( 'vcita-wordpress/v1', '/actions/(?P<action>.+)', array(
		'methods'  => array( 'GET', 'POST' ),
		'callback' => 'vcita_callback',
	), true );
}

function vcita_update_message( $data, $r ) {
	if ( isset( $data[ 'upgrade_notice' ] ) ) {
		printf( '<div class="update-message">%s</div>', wpautop( $data[ 'upgrade_notice' ] ) );
	}
}

function wpshd_vcita_check_redirect() {
	if ( get_option( 'wpshd_vcita_redirect_needed', false ) ) {
		delete_option( 'wpshd_vcita_redirect_needed' );
		exit( wp_redirect( get_admin_url( '', '', 'admin' ) . 'admin.php?page=' . WPSHD_VCITA_WIDGET_UNIQUE_ID . '/vcita-settings-functions.php' ) );
	}
}

function wpshd_vcita_activation_func() {
	$wpshd_vcita_widget = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
	
	if ( WPSHD_VCITA_ANOTHER_PLUGIN ) {
		$av_plugin_list                            = wp_cache_get( 'WPSHD_VCITA_ANOTHER_PLUGIN_LIST' );
		$wpshd_vcita_widget[ 'deactivate_showed' ] = true;
		
		$found = array();
		foreach ( $av_plugin_list as $av_plugin ) {
			$found[] = $av_plugin[ 'file' ];
		}
		deactivate_plugins( $found );
		
		add_option( 'wpshd_vcita_redirect_needed', true );
	}
	
	$wpshd_vcita_widget[ 'just_activated' ] = true;
	update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
}

function wpshd_vcita_deactivation_func() {
	$wpshd_vcita_widget                        = (array) get_option( WPSHD_VCITA_WIDGET_KEY );
	$wpshd_vcita_widget[ 'deactivate_showed' ] = false;
	$wpshd_vcita_widget[ 'reconnect_showed' ]  = false;
	update_option( WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget );
}

function wpshd_vcita_this_screen() {
	$screen = get_current_screen();
	
	if ( strpos( $screen->id, 'meeting-scheduler-by-vcita' ) !== false ) {
		add_action( 'admin_enqueue_scripts', 'vcita_enqueue_admin_scripts' );
		wpshd_vcita_init();
	}
}

function wpshd_register_wp_rest_settings() {
	define( 'WPSHD_VCITA_WIDGET_CALLBACK_URL', rest_url( null, 'vcita-wordpress/v1' ) );
}

function wpshd_vcita_load_translations() {
	load_plugin_textdomain( 'meeting-scheduler-by-vcita', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

if ( WP_DEBUG && WP_DEBUG_DISPLAY && ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
	@ ini_set( 'display_errors', 1 );
}

$av_plugins = wpshd_vcita_scheduler_check_plugin_available( 'vcita_scheduler' );

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$all_plugins = get_plugins();

if ( $av_plugins ) {
	define( 'WPSHD_VCITA_ANOTHER_PLUGIN', 1 );
	$all_plugin_data = array();
	
	foreach ( $all_plugins as $wp_file => $wp_plugin ) {
		if ( in_array( $wp_file, $av_plugins ) ) {
			$wp_plugin[ 'file' ] = $wp_file;
			
			if ( strpos( $wp_file, 'crm-customer-relationship-management-by-vcita' ) !== false || strpos( $wp_file, 'lead-capturing-call-to-actions-by-vcita' ) !== false ) {
				$wp_plugin[ 'url' ] = plugin_dir_path( $wp_file ) . 'vcita-settings-functions.php';
				array_unshift( $all_plugin_data, $wp_plugin );
			}
			else if ( strpos( $wp_file, 'paypal-payment-button-by-vcita' ) !== false || strpos( $wp_file, 'event-registration-calendar-by-vcita' ) !== false || strpos( $wp_file, 'contact-form-with-a-meeting-scheduler-by-vcita' ) !== false ) {
				$wp_plugin[ 'url' ] = 'live-site';
				$all_plugin_data[]  = $wp_plugin;
			}
			else {
				$all_plugin_data[] = $wp_plugin;
			}
		}
	}
	
	wp_cache_set( 'WPSHD_VCITA_ANOTHER_PLUGIN_LIST', $all_plugin_data );
	foreach ( $all_plugin_data as $av_plugin ) {
		$found[] = $av_plugin[ 'file' ];
	}
	deactivate_plugins( $found );
	delete_option( 'wpshd_vcita_redirect_needed' );
	
	if ( WPSHD_VCITA_ANOTHER_PLUGIN ) {
		header( 'Location: ' . get_admin_url( '', '', 'admin' ) . 'admin.php?page=meeting-scheduler-by-vcita/vcita-settings-functions.php' );
	}
}
else {
	define( 'WPSHD_VCITA_ANOTHER_PLUGIN', 0 );
	wp_cache_set( 'WPSHD_VCITA_ANOTHER_PLUGIN_LIST', array() );
}

define( 'WPSHD_VCITA_USE_MEET2KNOW', false );
define( 'WPSHD_VCITA_DEBUG', ( isset( $_GET[ 'WPSHD_DEBUG' ] ) && $_GET[ 'WPSHD_DEBUG' ] == 'true' ? true : false ) );
define( 'WPSHD_VCITA_SERVER_PREFIX', "www." );
define( 'WPSHD_VCITA_SERVER_BASE', WPSHD_VCITA_USE_MEET2KNOW ? "meet2know.com"
	: "vcita.com" ); /* Don't include the protocol, added dynamically */
define( 'WPSHD_VCITA_SERVER_URL', WPSHD_VCITA_SERVER_PREFIX . WPSHD_VCITA_SERVER_BASE );
define( 'WPSHD_VCITA_WIDGET_VERSION', '4.4.6' );
define( 'WPSHD_VCITA_WIDGET_PLUGIN_NAME', __( 'Appointment Booking and Online Scheduling by vCita', 'meeting-scheduler-by-vcita' ) );
define( 'WPSHD_VCITA_WIDGET_KEY', 'vcita_scheduler' );
define( 'WPSHD_VCITA_WIDGET_API_KEY', 'wp-v-schd' );
define( 'WPSHD_VCITA_WIDGET_INVITE_CODE', 'WP-V-SCHD' );
define( 'WPSHD_VCITA_WIDGET_MENU_NAME', __( 'vCita Online Scheduling', 'meeting-scheduler-by-vcita' ) );
define( 'WPSHD_VCITA_WIDGET_SHORTCODE', 'vCitaMeetingScheduler' );
define( 'WPSHD_VCITA_CALENDAR_WIDGET_SHORTCODE', 'vCitaSchedulingCalendar' );
define( 'WPSHD_VCITA_WIDGET_UNIQUE_ID', basename( __DIR__ ) );
define( 'WPSHD_VCITA_WIDGET_UNIQUE_LOCATION', __FILE__ );
define( 'WPSHD_VCITA_WIDGET_SHOW_EMAIL_PRIVACY', 'true' );
define( 'WPSHD_VCITA_WIDGET_DEMO_UID', 'wordpress.demo' );  /*	vCita.com/meet2know.com demo user uid: wordpress.demo */

$assets_path = plugin_dir_url( __FILE__ );
if ( is_ssl() && strpos( $assets_path, 'http:' ) !== false ) {
	$assets_path = str_replace( 'http:', 'https:', $assets_path );
}

define( 'WPSHD_VCITA_ASSETS_PATH', $assets_path );
define( 'WPSHD_VCITA_JS_PATH', WPSHD_VCITA_ASSETS_PATH . 'assets/js' );
define( 'WPSHD_VCITA_CSS_PATH', WPSHD_VCITA_ASSETS_PATH . 'assets/style' );

require_once( WP_PLUGIN_DIR . "/" . WPSHD_VCITA_WIDGET_UNIQUE_ID . "/vcita-utility-functions.php" );
require_once( WP_PLUGIN_DIR . "/" . WPSHD_VCITA_WIDGET_UNIQUE_ID . "/vcita-widgets-functions.php" );
require_once( WP_PLUGIN_DIR . "/" . WPSHD_VCITA_WIDGET_UNIQUE_ID . "/vcita-settings-functions.php" );
require_once( WP_PLUGIN_DIR . "/" . WPSHD_VCITA_WIDGET_UNIQUE_ID . "/vcita-api-functions.php" );
require_once( WP_PLUGIN_DIR . "/" . WPSHD_VCITA_WIDGET_UNIQUE_ID . "/vcita-ajax-function.php" );
/* --- Static initializer for Wordpress hooks --- */

//add_action('rest_api_init', 'wpshd_register_wp_rest_settings');
add_filter( 'plugin_action_links', 'wpshd_vcita_add_plugins_actions', 10, 4 );
add_action( 'current_screen', 'wpshd_vcita_this_screen' );
add_action( 'admin_init', 'wpshd_vcita_check_redirect' );
add_action( 'admin_menu', 'wpshd_vcita_admin_actions' );
add_action( 'wp_head', 'wpshd_vcita_add_active_engage' );

add_action( 'admin_notices', 'vcita_activate_func' );
add_action( 'admin_footer', 'vcita_admin_footer_script' );
add_action( 'plugins_loaded', 'wpshd_vcita_load_translations', 0 );

add_shortcode( WPSHD_VCITA_WIDGET_SHORTCODE, 'wpshd_vcita_add_contact' );
add_shortcode( WPSHD_VCITA_CALENDAR_WIDGET_SHORTCODE, 'wpshd_vcita_add_calendar' );

register_activation_hook( __FILE__, 'wpshd_vcita_activation_func' );
register_deactivation_hook( __FILE__, 'wpshd_vcita_deactivation_func' );

// REST Endpoint
add_action( 'rest_api_init', 'vcita_set_rest' );

if ( function_exists( 'register_sidebar_widget' ) && function_exists( 'register_widget_control' ) ) {
	wp_register_sidebar_widget( 'vcita_widget_id', 'vcita Sidebar Widget', 'wpshd_vcita_widget_content' );
	wp_register_widget_control( 'vcita_widget_id', 'vcita Sidebar Widget', 'wpshd_vcita_widget_admin' );
}

global $pagenow;

if ( 'plugins.php' === $pagenow ) {
	// $file = basename(__FILE__);
	// $folder = basename(dirname(__FILE__));
	// $hook = "in_plugin_update_message-{$folder}/{$file}";
	// add_action($hook, 'vcita_update_message', 20, 2);
} ?>
