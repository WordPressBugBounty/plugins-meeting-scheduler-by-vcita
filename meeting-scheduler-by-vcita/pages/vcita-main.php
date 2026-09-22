<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
$wpshd_vcita_uid = $wpshd_vcita_widget['uid'];
$wpshd_vcita_needs_reconnect = wpshd_vcita_check_need_to_reconnect($wpshd_vcita_widget);
$wpshd_vcita_av_plugin_list = wp_cache_get('WPSHD_VCITA_ANOTHER_PLUGIN_LIST');

if (empty($wpshd_vcita_uid)) {
  wp_enqueue_style('vcita-main-style', WPSHD_VCITA_CSS_PATH.'/main_v.css', array(), WPSHD_VCITA_WIDGET_VERSION);
}
?>

<div class="vcita-wrap" dir="ltr">
  <?php
      $wpshd_vcita_file_path = WP_PLUGIN_DIR.'/'.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/php_assets/admin_header.php';
      if (file_exists($wpshd_vcita_file_path)) {
          require_once $wpshd_vcita_file_path;
      }
  ?>
  <div class="vcita-wrap-inner">
    <?php if (WPSHD_VCITA_ANOTHER_PLUGIN) { ?>
      <?php if ($wpshd_vcita_av_plugin_list) {
        $wpshd_vcita_plugin_str = '';

        foreach ($wpshd_vcita_av_plugin_list as $vcita_plugin_data) {
          $wpshd_vcita_plugin_str .= ' or ' . $vcita_plugin_data["Name"];
        }

        $wpshd_vcita_plugin_str = substr($wpshd_vcita_plugin_str, 4);
      ?>
        <div class="vcita__page__error-container">
          <?php echo esc_html__('As you already use', 'meeting-scheduler-by-vcita') ?>&nbsp;
          <b><?php echo esc_html( $wpshd_vcita_plugin_str ) ?></b>&nbsp;
          <?php echo esc_html__('by vcita, you can edit the scheduling widget there. Alternatively you can deactivate all other vcita plugins installed on your site and configure your scheduling widget using this plugin. Set up widget using', 'meeting-scheduler-by-vcita') ?>
          <a href="<?php echo esc_url( get_admin_url('', '', 'admin').'admin.php?page='.urlencode($vcita_plugin_data['url']) ) ?>">
            <?php echo esc_html( $wpshd_vcita_plugin_str ) ?>
          </a>
          <?php echo esc_html__('or', 'meeting-scheduler-by-vcita') ?>
          <a href="javascript:void(0)" onclick="jQuery.ajax({'url':`${window.$_ajaxurl}?action=vcita_deactivate_others&rescan=1`,'method':'GET','success':(resp)=>{try{if(resp=='success'){window.location.reload()}else{VcitaUI.hidePopup()}}catch(err){console.log(err)}}})">
            <?php echo esc_html__('deactivate all other vcita plugins', 'meeting-scheduler-by-vcita') ?>
          </a>
        </div>
      <?php }
    } else {
      if (!empty($wpshd_vcita_uid) || $wpshd_vcita_needs_reconnect) { ?>
        <div class="vcita__page__inner__section">
          <h3>
            <?php echo esc_html__('Hi', 'meeting-scheduler-by-vcita') ?>
            <?php if ($wpshd_vcita_needs_reconnect) { ?>
              <small>
                <?php echo esc_html__('Re-connect below to continue to settings and edit your plugin.', 'meeting-scheduler-by-vcita') ?>
              </small>
            <?php } else { ?>
              <small>
                <?php echo esc_html__('Quickly configure your online booking settings with our online booking wizard', 'meeting-scheduler-by-vcita') ?>
              </small>
            <?php } ?>
          </h3>
          <?php if ($wpshd_vcita_needs_reconnect) { ?>
            <div>
              <button class="vcita__btn__blue start-login reconnect clickme">
                <?php echo esc_html__('Connect to vcita', 'meeting-scheduler-by-vcita') ?>
              </button>
            </div>
          <?php } else { ?>
            <div>
              <a
                class="vcita__btn__blue <?php echo esc_attr( !isset($wpshd_vcita_widget['start_wizard_clicked']) || !$wpshd_vcita_widget['start_wizard_clicked'] ? 'clickme' : '' ) ?>"
                href="https://app.vcita.com/app/settings/calendar_settings"
                target="_blank">
                <?php echo esc_html__('Settings', 'meeting-scheduler-by-vcita') ?>
              </a>
            </div>
          <?php } ?>
        </div>
      <?php } else { ?>
        <div class="vcita__page__inner__section">
          <h3 class="hcentered">
            <?php echo esc_html__('Welcome to vcita!', 'meeting-scheduler-by-vcita') ?>
            <small>
              <?php echo wp_kses_post( __('vcita is a self-service scheduling for you and your clients.<br>Save time on coordination and increase client satisfaction.', 'meeting-scheduler-by-vcita') ) ?>
            </small>
          </h3>
          <div class="txt_input_wrapper hcentered">
            <button style="min-width:220px" class="vcita__btn__blue start-signup clickme" id="start-signup">
              <?php echo esc_html__('Let’s get started', 'meeting-scheduler-by-vcita') ?>
            </button>
          </div>
          <div class="vcita__page__inner__section-footer">
            <?php echo esc_html__('Already have an account?', 'meeting-scheduler-by-vcita') ?>&nbsp;
            <a href="javascript:void(0)" class="start-login">
              <?php echo esc_html__('Connect', 'meeting-scheduler-by-vcita') ?>
            </a>
          </div>
        </div>
      <?php }
      if (empty($wpshd_vcita_uid)) { ?>
        <div class="vcita__page__inner__section">
          <h3 class="hcentered">
            <?php echo esc_html__('See it in action', 'meeting-scheduler-by-vcita') ?>
            <small><?php echo esc_html__('From visitor to client with few easy steps', 'meeting-scheduler-by-vcita') ?></small>
          </h3>
          <div class="hcentered">
            <img style="max-width:100%" src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ); ?>/images/5f21c155293f7985099214.gif" />
          </div>
        </div>
        <div class="vcita__page__inner__section" style="padding-bottom:0">
          <h3 class="hcentered">
            <?php echo esc_html__('Some of vcita’s cool features', 'meeting-scheduler-by-vcita') ?>
          </h3>
          <div>
            <div class="vcita__main__page-banner1-container flex hspaced">
              <div class="vcita__main__page-banner1-wrapper">
                <div class="vcita__main__page-banner-img">
                  <img src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ); ?>/images/assets/hand-with-mobile.svg"/>
                </div>
                <div class="vcita__main__page-banner-title">
                  <?php echo esc_html__('Schedule online – anytime, anywhere', 'meeting-scheduler-by-vcita') ?>
                </div>
                <div class="vcita__main__page-banner-text">
                  <?php echo esc_html__('Our online scheduler can be accessed from anywhere, anytime!', 'meeting-scheduler-by-vcita') ?>
                </div>
              </div>
              <div class="vcita__main__page-banner1-wrapper">
                <div class="vcita__main__page-banner-img">
                  <img src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ); ?>/images/assets/zoom.svg"/>
                </div>
                <div class="vcita__main__page-banner-title">
                  <?php echo esc_html__('The best experience for your clients', 'meeting-scheduler-by-vcita') ?>
                </div>
                <div class="vcita__main__page-banner-text">
                  <?php echo esc_html__('Let clients schedule online services and conduct video calls via Zoom.', 'meeting-scheduler-by-vcita') ?>
                </div>
              </div>
              <div class="vcita__main__page-banner1-wrapper">
                <div class="vcita__main__page-banner-img">
                  <img src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ); ?>/images/assets/record-payment.svg"/>
                </div>
                <div class="vcita__main__page-banner-title">
                  <?php echo esc_html__('Billing & Invoicing', 'meeting-scheduler-by-vcita') ?>
                </div>
                <div class="vcita__main__page-banner-text">
                  <?php echo esc_html__('Handle your billing & Invoicing like a pro, and accept payments online', 'meeting-scheduler-by-vcita') ?>
                </div>
              </div>
            </div>
            <div class="vcita__main__page-banner1-container flex hspaced">
              <div class="vcita__main__page-banner1-wrapper">
                <div class="vcita__main__page-banner-img">
                  <img src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ); ?>/images/assets/team.svg"/>
                </div>
                <div class="vcita__main__page-banner-title">
                  <?php echo esc_html__('Solo? Team? vcita fits all', 'meeting-scheduler-by-vcita') ?>
                </div>
                <div class="vcita__main__page-banner-text">
                  <?php echo esc_html__('Whether you’re a one-person show or manage a team of employees, we’ve got you covered!', 'meeting-scheduler-by-vcita') ?>
                </div>
              </div>
              <div class="vcita__main__page-banner1-wrapper">
                <div class="vcita__main__page-banner-img">
                  <img src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ); ?>/images/assets/no-coding-needed.svg"/>
                </div>
                <div class="vcita__main__page-banner-title">
                  <?php echo esc_html__('No coding needed', 'meeting-scheduler-by-vcita') ?></div>
                <div class="vcita__main__page-banner-text">
                  <?php echo esc_html__('Integrating vcita’s scheduling software with your online assets is super easy and requires no technical knowledge or coding.', 'meeting-scheduler-by-vcita') ?>
                </div>
              </div>
              <div class="vcita__main__page-banner1-wrapper">
                <div class="vcita__main__page-banner-img">
                  <img src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ); ?>/images/assets/save-the-back-and-forth.svg"/>
                </div>
                <div class="vcita__main__page-banner-title">
                  <?php echo esc_html__('Save the back-and-forth', 'meeting-scheduler-by-vcita') ?>
                </div>
                <div class="vcita__main__page-banner-text">
                  <?php echo esc_html__('Let your calendar work for you! Automatically confirm client scheduling requests based on your availability to save time on back & forth coordination.', 'meeting-scheduler-by-vcita') ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="vcita__page__inner__section">
          <h3 class="hcentered">
            <?php echo esc_html__('See what users say about our online scheduler', 'meeting-scheduler-by-vcita') ?>
            <small>
              <?php echo esc_html__('Rated 4.7/5 on Google My Business (308 votes)', 'meeting-scheduler-by-vcita') ?>
            </small>
          </h3>
          <div class="vcita__main__page-cards-container flex hspaced">
            <div class="vcita__main__page-cards-wrapper">
              <div class="vcita__main__page-card-img">
                <img src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ); ?>/images/assets/face_1.png"/>
              </div>
              <div class="vcita__main__page-card-title orange">
                <?php echo esc_html__('Sales & marketing', 'meeting-scheduler-by-vcita') ?>
              </div>
              <div class="vcita__main__page-card-text">
                <?php echo esc_html__('“Before it took 3 days to schedule an appointment with a client. Now it takes 30 seconds.”', 'meeting-scheduler-by-vcita') ?>
              </div>
              <div class="vcita__main__page-card-footer">
                <b><?php echo esc_html__('Brandon Klayman', 'meeting-scheduler-by-vcita') ?></b><br>
                <?php echo esc_html__('Marketing & Sales Expert', 'meeting-scheduler-by-vcita') ?>
              </div>
            </div>
            <div class="vcita__main__page-cards-wrapper">
              <div class="vcita__main__page-card-img">
                <img src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ); ?>/images/assets/face_2.png"/>
              </div>
              <div class="vcita__main__page-card-title blue">
                <?php echo esc_html__('Entertainment & arts', 'meeting-scheduler-by-vcita') ?>
              </div>
              <div class="vcita__main__page-card-text">
                <?php echo esc_html__('“I was able to triple my income because I had given clients the ability to book their own appointments”', 'meeting-scheduler-by-vcita') ?>
              </div>
              <div class="vcita__main__page-card-footer">
                <b><?php echo esc_html__('Patrick Osei', 'meeting-scheduler-by-vcita') ?></b><br>
                <?php echo esc_html__('Recording Studio Owner', 'meeting-scheduler-by-vcita') ?>
              </div>
            </div>
            <div class="vcita__main__page-cards-wrapper">
              <div class="vcita__main__page-card-img">
                <img src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ); ?>/images/assets/face_3.png"/>
              </div>
              <div class="vcita__main__page-card-title pink">
                <?php echo esc_html__('Beauty & fashion', 'meeting-scheduler-by-vcita') ?>
              </div>
              <div class="vcita__main__page-card-text">
                <?php echo esc_html__('“The online scheduling ability enables me to have a life as an entrepreneur and have a good flow to my day.”', 'meeting-scheduler-by-vcita') ?>
              </div>
              <div class="vcita__main__page-card-footer">
                <b><?php echo esc_html__('Janay Mallela', 'meeting-scheduler-by-vcita') ?></b><br>
                <?php echo esc_html__('Wedding Gown Designer', 'meeting-scheduler-by-vcita') ?>
              </div>
            </div>
          </div>
        </div>
        <div class="vcita__page__inner__section">
          <h3 class="hcentered">
            <?php echo esc_html__('It’s time to get booked online', 'meeting-scheduler-by-vcita') ?>
            <small>
              <?php echo esc_html__('Join the thousands of users who use vcita to manage their meetings.', 'meeting-scheduler-by-vcita') ?>
            </small>
          </h3>
          <div class="txt_input_wrapper hcentered">
            <button class="vcita__btn__blue start-signup clickme" id="start-signup">
              <?php echo esc_html__('Let’s get started', 'meeting-scheduler-by-vcita') ?>
            </button>
          </div>
          <div class="vcita__page__inner__section-footer">
            <?php echo esc_html__('Already have an account?', 'meeting-scheduler-by-vcita') ?>&nbsp;
            <a href="javascript:void(0)" class="start-login">
              <?php echo esc_html__('Connect', 'meeting-scheduler-by-vcita') ?>
            </a>
          </div>
        </div>
      <?php } else if (!$wpshd_vcita_needs_reconnect) { ?>
        <div class="vcita__page__inner__section">
          <h3>
            <?php echo esc_html__('Your availability hours', 'meeting-scheduler-by-vcita') ?>
            <small>
              <?php echo esc_html__('Set the time during which clients can book meetings with you.', 'meeting-scheduler-by-vcita') ?>
              <a href="javascript:void(0)" onclick="VcitaUI.openEditAvailabilityWin(event)">
                <?php echo esc_html__('Edit availability', 'meeting-scheduler-by-vcita') ?>
              </a>
            </small>
          </h3>
          <div id="vcita__time__slots">
            <mark class="vcita__preloader__container">
              <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
            </mark>
          </div>
        </div>
        <div class="vcita__page__inner__section">
          <h3>
            <?php echo esc_html__('Your services', 'meeting-scheduler-by-vcita') ?>
            <small>
              <?php echo esc_html__('Here are the services your clients can book with you. You can', 'meeting-scheduler-by-vcita') ?>
              <a href="javascript:void(0)" onclick="VcitaUI.openEditServicesWin(event)">
                <?php echo esc_html__('add / edit', 'meeting-scheduler-by-vcita') ?>
              </a>
              &nbsp;<?php echo esc_html__('more services', 'meeting-scheduler-by-vcita') ?>
            </small>
          </h3>
          <div class="vcita__services__list">
            <mark class="vcita__preloader__container">
              <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
            </mark>
          </div>
        </div>
        <div class="vcita__page__inner__section">
          <h3>
            <?php echo esc_html__('Sync your Calendars', 'meeting-scheduler-by-vcita') ?>
            <small>
              <?php echo esc_html__('Sync your Outlook, iCal or Google Calendar with vcita to efficiently manage your calendar & avoid double bookings', 'meeting-scheduler-by-vcita') ?>
            </small>
          </h3>
          <div>
            <button class="vcita__btn__blue" onclick="VcitaUI.openSyncCalendarWin(event)">
              <?php echo esc_html__('Sync calendar', 'meeting-scheduler-by-vcita') ?>
            </button>
          </div>
        </div>
      <?php }
    }?>
    <?php require_once WP_PLUGIN_DIR.'/'.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/php_assets/admin_footer.php'; ?>
  </div>
</div>
<?php if (!WPSHD_VCITA_ANOTHER_PLUGIN) {
  if ($wpshd_vcita_uid && !$wpshd_vcita_needs_reconnect) { ?>
    <?php
      
      wp_enqueue_script('vcita-main', plugins_url('assets/js/main_v.js', str_ireplace('/pages', '', __FILE__)), array(), WPSHD_VCITA_WIDGET_VERSION, true);
	  
	  wp_localize_script(
		  'vcita-main',
		  'vcitaSchedulerData',
		  [
			  'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			  'nonce'   => wp_create_nonce( 'wpshd_vcita_nonce_action' ),
		  ]
	  );
      
      ?>
  <?php } else { ?>
    <script type="text/javascript">
      document.addEventListener('DOMContentLoaded', () => {
        const vid = document.querySelector('.vcita__page__main-video');

        if (vid != null) {
          vid.addEventListener('click', function () {
            if (this.lastElementChild.tagName == 'IMG') {
              this.innerHTML = '<video controls preload="metadata" src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ); ?>/assets/media/video.mp4"></video>';
              this.firstElementChild.addEventListener('play', () => { })
              this.firstElementChild.play()
            }
          })
        }
        <?php if ($wpshd_vcita_needs_reconnect) { ?>
        <?php } ?>
      })
    </script>
  <?php } ?>
<?php } ?>
<script type="text/javascript">
  function vcita_fetch_staff(add_preloader) {
    const e = document.querySelector('.vcita__services__list');
    if (e == null) return;
    if (add_preloader) e.innerHTML = VcitaUI.createPreloaderHTML();

    VcitaApi.getStaff()
      .then((resp1) => {
        if (window.WPSHD_VCITA_DEBUG) console.log(resp1);

        if (resp1.error) {
          e.innerHTML = `<div>${resp1.message}</div>`;
          const avc = document.getElementById('vcita__time__slots');
          if (avc != null) avc.innerHTML = `<div>${resp1.message}</div>`;
          return;
        }

        const staffLength = resp1.staff ? resp1.staff.length : null;

        if (staffLength) {
          VcitaApi.getServices()
            .then((resp2) => {
              if (window.WPSHD_VCITA_DEBUG) console.log(resp2);
              e.firstElementChild.remove();

              if (resp2.services_data != undefined) {
                const serviceLength = resp2.services_data.length;

                e.insertAdjacentHTML('beforeend', '<table class="vcita__page__inner-table"><thead>' +
                  '<tr><td><?php echo esc_js( __("Service name", "meeting-scheduler-by-vcita") ) ?></td>' +
                  '<td><?php echo esc_js( __("Service duration", "meeting-scheduler-by-vcita") ) ?></td>' +
                  '</tr></thead><tbody></tbody></table>');

                const tab = e.lastElementChild.lastElementChild;

                for (let i = 0; i < serviceLength; i++) {
                  const s = resp2.services_data[i];
                  tab.insertAdjacentHTML('beforeend', `<tr><td style="width:75%">${s.name}</td><td>${s.duration} minutes</td></tr>`);
                }

                if (staffLength === 1 && serviceLength) {
                  vcita_fetch_availability(add_preloader);
                } else {
                  const avc = document.getElementById('vcita__time__slots');
                  if (avc != null) avc.innerHTML = '';
                }
              }
            }).catch((err) => {
                console.log(err);
                e.innerHTML = '<div>Some error occurred</div>';
                const avc = document.getElementById('vcita__time__slots');
                if (avc != null && avc) avc.innerHTML = '';
            });
        } else {
            e.parentNode.remove();
            document.querySelector('.vcita__time__slots').remove()
        }
      })
      .catch((serr) => {
        console.log(serr);
        e.innerHTML = '<div>Some error occurred</div>';
        const avc = document.getElementById('vcita__time__slots');
        if (avc != null && avc.firstElementChild != null) avc.firstElementChild.remove();
      })
  }
</script>
