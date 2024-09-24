<?php
  wp_enqueue_style('vcita-premium-style', WPSHD_VCITA_ASSETS_PATH.'assets/style/premium_v.css');
  $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
?>
<div class="vcita-wrap" dir="ltr">
  <?php require_once WP_PLUGIN_DIR."/".WPSHD_VCITA_WIDGET_UNIQUE_ID.'/php_assets/admin_header.php' ?>
  <div class="vcita-wrap-inner">
    <div class="vcita__page__inner__section">
      <div class="vcita__premium__inner__section-banner">
        <section class="vcita__custom__inner__section-wrapper">
          <h3 class="vcita__premium__inner-header">
            <?php echo __('Manage your business<br>from a to z with vcita:', 'meeting-scheduler-by-vcita') ?>
            <!--<small><?php echo __('Enjoy these awesome scheduling features:', 'meeting-scheduler-by-vcita') ?></small>-->
          </h3>
          <ul>
            <li class="thumb-green"><?php echo __('Scheduling', 'meeting-scheduler-by-vcita') ?></li>
            <li class="thumb-pink"><?php echo __('Online payments', 'meeting-scheduler-by-vcita') ?></li>
            <li class="thumb-blue"><?php echo __('Billing & Invoicing', 'meeting-scheduler-by-vcita') ?></li>
            <li class="thumb-yellow"><?php echo __('Client management (CRM)', 'meeting-scheduler-by-vcita') ?></li>
            <li class="thumb-green2"><?php echo __('Marketing & coupons', 'meeting-scheduler-by-vcita') ?></li>
          </ul>
          <button class="vcita__btn__blue clickme" onclick="vcita_show_av_plans(event,'wp_sched_see_plans')">
            <?php echo __('Check available plans', 'meeting-scheduler-by-vcita') ?>
          </button>
          <h4>
            <?php echo __('Not sure what best fits your needs? we’d be happy to advise.', 'meeting-scheduler-by-vcita') ?>
            <a target="_blank" href="https://live.vcita.com/site/vcita.sales/online-scheduling?service=289tixik1ymrp6hw&vtm_cp=wordpress_scheduling"><?php echo __('Click here', 'meeting-scheduler-by-vcita') ?></a>
            &nbsp;<?php echo __('to book a quick consultation with our Customer Success Team.', 'meeting-scheduler-by-vcita') ?>
          </h4>
        </section>
        <div class="vcita__premium__inner__section-banner-img" style="background:url('<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/img.jpg')">
          <!--
          <div class="vcita__premium__stamp"><?php echo __('just $12 a month', 'meeting-scheduler-by-vcita') ?></div>
          -->
        </div>
      </div>
    </div>
    <?php require_once WP_PLUGIN_DIR.'/'.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/php_assets/admin_footer.php'; ?>
  </div>
</div>
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', () => {
    VcitaMixpman.track('wp_sched_change_tab', { tab: 'premium' });
  });
</script>

