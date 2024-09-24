<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', () => {
    if (URLSearchParams) {
      const tab = new URLSearchParams(window.location.search).get('tab');
      if (tab !== null) {
        for (let cur of document.querySelectorAll('.wp-has-current-submenu')) {
          const cc = cur.querySelector('li.current');
          if (cc !== null) cc.classList.remove('current');

          const a = cur.querySelector(`[href*="tab=${tab}"]`);
          if (a !== null) a.parentNode.classList.add('current');
        }
      }
    }
  });

  function wpshd_reg_popup(home_url, ud) {
    return '<div id="vcita_reg_popup">' +
      '<div class="vcita__btn__close" onclick="VcitaUI.hidePopup(true)"></div>' +
      `<img src="${home_url}/images/hands-welcome.svg"/>` +
      '<div class="vcita_reg_popup-title"><?php echo __('Welcome Onboard!', 'meeting-scheduler-by-vcita') ?></div>' +
      '<div class="vcita_reg_popup-text">' +
      `<div>Hey ${(ud.name ? ud.name : ud.email)}</div>` +
      '<div><?php echo addslashes(__('Your account on vcita was successfully created and you are ready to accept appointments online.', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><?php echo addslashes(__('Your default working days are <b>Monday-Friday</b> from <b>9am</b> to <b>5pm</b>. You can edit those on the settings tab.', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><?php echo addslashes(__('Go ahead to customize your scheduling button look and feel.', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><?php echo addslashes(__('Happy Scheduling,', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><?php echo addslashes(__('The vcita Team', 'meeting-scheduler-by-vcita')) ?></div>' +
      '</div></div>'
  }

  function wpshd_reconnect_popup() {
    return '<div id="vcita_reg_popup">' +
      '<div class="vcita__btn__close" onclick="VcitaUI.hidePopup(true)"></div>' +
      '<div class="vcita_reg_popup-title"><?php echo addslashes(__('Please reconnect vcita', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div class="vcita_reg_popup-text">' +
      '<div><?php echo addslashes(__('Two more steps to go:', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><?php echo addslashes(__('1. Please reconnect your vcita account to this plugin.', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><?php echo addslashes(__('2. RECOMMENDED: After you reconnect please schedule a demo appointment with yourself through your site to make sure that  everything works properly.', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div style="text-align:center">' +
      '<button class="vcita__btn__blue" onclick="event.preventDefault();event.stopPropagation();VcitaMixpman.track(\'wp_sched_login_vcita\');VcitaMixpman.track(\'wp_sched_reconnect_click\');VcitaUI.openAuthWin(false,false)">' +
      '<?php echo addslashes(__('Reconnect vcita', 'meeting-scheduler-by-vcita')) ?></button></div>' +
      '</div></div>'
  }

  function wpshd_rate_popup() {
    return '<div id="vcita_reg_popup">' +
      '<div class="vcita__btn__close" onclick="VcitaUI.hidePopup(true)"></div>' +
      '<div class="vcita_reg_popup-title"><?php echo addslashes(__('Enjoying vcita?', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div class="vcita_reg_popup-text">' +
      '<div><?php echo addslashes(__('Can you please help us with a BIG favor and rate vcita 5 stars.', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div style="text-align:center">' +
      '<button class="vcita__btn__blue" onclick="wpshd_vcita_goToRate(event,this)">' +
      '<?php echo addslashes(__('Rate 5 Stars', 'meeting-scheduler-by-vcita')) ?></button>' +
      '&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick="wpshd_vcita_snooze_rate(event,this)"><?php echo addslashes(__('maybe later', 'meeting-scheduler-by-vcita')) ?></a>'+
      '&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick="wpshd_vcita_goToRate(event,this,false)"><?php echo addslashes(__('already rated', 'meeting-scheduler-by-vcita')) ?></a>'+
      '</div></div></div>'
  }

  function wpshd_migrated_popup(home_url, ud) {
    return '<div id="vcita_reg_popup">' +
      '<div class="vcita__btn__close" onclick="VcitaUI.hidePopup(true)"></div>' +
      `<img src="${home_url}/images/hands-welcome.svg"/>` +
      '<div class="vcita_reg_popup-title"><?php echo addslashes(__('Fresh Scheduling Experience', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div class="vcita_reg_popup-text">' +
      `<div><?php echo addslashes(__('Hey', 'meeting-scheduler-by-vcita')) ?> ${(ud.name ? ud.name : ud.email)}</div>` +
      '<div><?php echo addslashes(__('Thank you for updating the plugin.', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><?php echo addslashes(__('What\'s new in this update? absolutely everything. To provide you a better experience we gave the plugin a fresh new design and coded everything from scratch.', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><?php echo addslashes(__('Although the code is brand new, our team worked very hard to make sure that all vcita features you had in the previous version will remain active. In any event, we advise you to take a quick review of your site just to make sure that everything works as before.', 'meeting-scheduler-by-vcita')) ?></div>' +
      `<div><?php echo addslashes(__('If you have any issues you are welcome to contact', 'meeting-scheduler-by-vcita')) ?> <a href="https://support.${WPSHD_VCITA_SERVER_BASE}/hc/en-us" target="_blank"><?php echo __('our support', 'meeting-scheduler-by-vcita') ?></a>.</div>` +
      '<div><?php echo addslashes(__('Enjoy,', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><?php echo addslashes(__('The vcita Team', 'meeting-scheduler-by-vcita')) ?></div>' +
      '</div></div>'
  }

  function wpshd_deactivation_popup(home_url) {
    return '<div id="vcita_reg_popup">' +
      '<div class="vcita__btn__close" onclick="VcitaUI.hidePopup(true)"></div>' +
      `<img src="${home_url}/images/hands-welcome.svg"/>` +
      '<div class="vcita_reg_popup-title"><?php echo addslashes(__('Thanks for activating vcita scheduler', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div class="vcita_reg_popup-text">' +
      '<div><?php echo addslashes(__('Hi', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><?php echo addslashes(__('We\'ve noticed that you are using other vcita plugins', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><?php echo addslashes(__('In order to customize your scheduling button you need to deactivate them', 'meeting-scheduler-by-vcita')) ?></div>' +
      '<div><button class="vcita__btn__blue" onclick="jQuery.ajax({' +
      '\'url\':\'<?php echo admin_url('admin-ajax.php') ?>?action=vcita_deactivate_others\',\'method\':\'GET\',' +
      '\'success\':(resp)=>{try{if(resp==\'success\'){window.location.reload()}else{VcitaUI.hidePopup()}}catch(err){console.log(err)}}})">Deactivate plugins</button></div>' +
      '<div><?php echo addslashes(__('The vcita Team', 'meeting-scheduler-by-vcita')) ?></div>' +
      '</div></div>'
  }
</script>
<?php
require_once WP_PLUGIN_DIR . '/' . WPSHD_VCITA_WIDGET_UNIQUE_ID . '/php_assets/admin_scripts.php';

$query = isset($_GET['tab']) ? $_GET['tab'] : '';
$url = $_SERVER['REQUEST_URI'];

  $menu_urls = array(
  $wpshd_vcita_widget['uid'] ? __('Settings', 'meeting-scheduler-by-vcita') : __('Main', 'meeting-scheduler-by-vcita') => 'vcita-settings-functions.php',
  __('Add to Site', 'meeting-scheduler-by-vcita') => 'vcita-settings-functions.php&tab=vcita-add-to-site',
  __('Custom Implementation', 'meeting-scheduler-by-vcita') => 'vcita-settings-functions.php&tab=vcita-custom-impl',
  __('Support', 'meeting-scheduler-by-vcita') => 'vcita-settings-functions.php&tab=vcita-support',
  __('Premium', 'meeting-scheduler-by-vcita') => 'vcita-settings-functions.php&tab=vcita-premium'
);

$is_current = false;
$url_found = false;
$current_name = '';
$active_marked = false;

if (wpshd_vcita_check_need_to_reconnect($wpshd_vcita_widget) && !WPSHD_VCITA_ANOTHER_PLUGIN &&
  (!isset($wpshd_vcita_widget['reconnect_showed']) || !$wpshd_vcita_widget['reconnect_showed'])
) {
  $wpshd_vcita_widget['reconnect_showed'] = true;
  update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);

  echo '<script type="text/javascript">
    document.addEventListener(\'DOMContentLoaded\', () => {
      window.VcitaUI.constructPopup({}, window.VcitaUI.getReconnectPopupHTML());
    })
    </script>';
}
?>

<div id="vcita-head">
  <div>
    <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/new-logo.svg" alt="vCita"/>
  </div>
  <?php echo __('Online Booking by vcita', 'meeting-scheduler-by-vcita') ?>
  <?php if ($wpshd_vcita_widget['uid']) { ?>
  <div class="vcita-btn-upgrade" style="display:none" onclick="vcita_show_av_plans(event,'wp_sched_upgrade_button_clicked')">
    <?php echo __('Upgrade', 'meeting-scheduler-by-vcita') ?>
  </div>
  <?php } ?>
</div>
<div id="vcita_admin_menu">
  <div id="vcita_admin_menu_wrapper">
    <?php if ($query) {
      foreach ($menu_urls as $name => $val) {
        if (!$url_found) {
          $is_current = strpos($val, $query) !== false;
          $url_found = $is_current;
          if ($is_current) $current_name = $name;
        } else {
          break;
        }
      }
    }

    foreach ($menu_urls as $name => $val) {
      if (!$url_found) {
        $is_current = strpos($url, $val) !== false;
        $url_found = $is_current;
        if ($is_current) $current_name = $name;
      }

      if (!$active_marked && $current_name == $name) { ?>
        <div class="active">
          <a href="javascript:void(0)"><?php echo $name ?></a>
        </div>
        <?php $active_marked = true; ?>
      <?php } else { ?>
        <div>
          <?php $href = get_admin_url('', '', 'admin') . 'admin.php?page=' . WPSHD_VCITA_WIDGET_UNIQUE_ID . '/' . $val; ?>
          <a href="<?php echo $href ?>"><?php echo $name ?></a>
        </div>
      <?php } ?>
    <?php } ?>
  </div>
  <div class="vcita__page__alert-container"></div>
</div>
<script type="text/javascript">
  function wpshd_vcita_goToRate(e, _this, open = true) {
    e.preventDefault();
    if (open) {
      window.open('https://wordpress.org/support/plugin/meeting-scheduler-by-vcita/reviews/#new-post', '_blank');
    }
    VcitaUI.hidePopup(true);
    wpshd_vcita_put_lsr_data('<?php echo $wpshd_vcita_widget['uid'] ?>', {
      dismiss: true,
      wait: false,
      date: new Date().getTime(),
      version: '<?php echo WPSHD_VCITA_WIDGET_VERSION; ?>'
    });
  }

  function wpshd_vcita_snooze_rate(e, _this) {
    e.preventDefault();
    VcitaUI.hidePopup(true);
    wpshd_vcita_put_lsr_data('<?php echo $wpshd_vcita_widget['uid'] ?>', {
      dismiss: false,
      wait: true,
      date: new Date().getTime(),
      version: '<?php echo WPSHD_VCITA_WIDGET_VERSION; ?>'
    });
  }

  function wpshd_vcita_put_lsr_data(key, val) {
    let rdata = {};
    try {
      rdata = localStorage.getItem('wpshd_rate_state');
      if (rdata !== null) {
        rdata = JSON.parse(rdata);
      } else {
        rdata = {};
      }
    } catch (err) {
      rdata = {};
    }
    rdata[key] = val;
    localStorage.setItem('wpshd_rate_state', JSON.stringify(rdata));
  }
</script>