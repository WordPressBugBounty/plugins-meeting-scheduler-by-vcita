<?php if (isset($wpshd_vcita_widget["uid"]) && $wpshd_vcita_widget['uid']) { ?>
  <div class="vcita_footer">
    <?php echo __('You are connected to vcita as', 'meeting-scheduler-by-vcita')?>
    <?php echo isset($wpshd_vcita_widget['email']) && $wpshd_vcita_widget['email'] ? $wpshd_vcita_widget['email'] : $wpshd_vcita_widget['uid']; ?>
    |
    <a href="javascript:void(0)" id="switch-account"><?php echo __('Sign out', 'meeting-scheduler-by-vcita')?></a>
  </div>
<?php } ?>
<div class="wpshd_fab_widget">
  <div class="wpshd_fab_button">
    <img width="64" height="64" src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/fab-button.svg"/>
    <div class="glow__orb">
      <div class="glow__orb__shine"></div>
      <div class="glow__orb__light"></div>
    </div>
  </div>
  <div class="wpshd_fab_widget_button-container">
    <div class="wpshd_fab_widget_button" onclick="vcita_show_av_plans(event,'wp_sched_bot_upgrade')">
      <div class="wpshd_fab_widget_button-text"><?php echo __('Upgrade', 'meeting-scheduler-by-vcita')?></div>
      <div class="wpshd_fab_widget_button-img">
        <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/rocket-launch-light.svg"/>
      </div>
    </div>
    <div class="wpshd_fab_widget_button" onclick="wpshd_fab_schedule_demo(event)">
      <div class="wpshd_fab_widget_button-text"><?php echo __('Schedule a demo', 'meeting-scheduler-by-vcita')?></div>
      <div class="wpshd_fab_widget_button-img">
        <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/desktop-light-1.svg"/>
      </div>
    </div>
    <div class="wpshd_fab_widget_button" onclick="wpshd_fab_support(event)">
      <div class="wpshd_fab_widget_button-text"><?php echo __('Support & Docs', 'meeting-scheduler-by-vcita')?></div>
      <div class="wpshd_fab_widget_button-img">
        <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/user-headset-light.svg"/>
      </div>
    </div>
    <div class="wpshd_fab_widget_button" onclick="wpshd_fab_suggest(event)">
      <div class="wpshd_fab_widget_button-text"><?php echo __('Suggest a feature', 'meeting-scheduler-by-vcita')?></div>
      <div class="wpshd_fab_widget_button-img">
        <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/lightbulb-on-light.svg"/>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  document.querySelector('.wpshd_fab_button').onclick = function () {
    this.classList.toggle('active');
  };

  function wpshd_fab_schedule_demo() {
    window.open("https://live.<?php echo WPSHD_VCITA_SERVER_BASE ?>/site/vcita.sales/online-scheduling?service=289tixik1ymrp6hw&vtm_cp=wordpress_plugin_scheduling", "_blank");
    VcitaMixpman.track('wp_sched_bot_demo');
  }

  function wpshd_fab_support() {
    window.open("https://support.<?php echo WPSHD_VCITA_SERVER_BASE ?>/hc/en-us/sections/206185508-Wordpress", "_blank");
    VcitaMixpman.track('wp_sched_bot_support');
  }

  function wpshd_fab_suggest() {
    window.open("https://www.<?php echo WPSHD_VCITA_SERVER_BASE ?>/contact", "_blank");
    VcitaMixpman.track('wp_sched_bot_suggest');
  }
</script>

