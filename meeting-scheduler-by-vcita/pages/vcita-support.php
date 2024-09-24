<?php
$wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
$uid = $wpshd_vcita_widget["uid"];
?>
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', () => {
    VcitaMixpman.track('wp_sched_change_tab', {tab: 'support'});
  })
</script>
<div class="vcita-wrap" dir="ltr">
  <?php require_once WP_PLUGIN_DIR . '/' . WPSHD_VCITA_WIDGET_UNIQUE_ID . '/php_assets/admin_header.php'; ?>
  <!--
  <?php if ($wpshd_vcita_widget['uid']) { ?>
    <div class="vcita-wrap-left-banner">
      <img onclick="vcita_show_av_plans(event,'wp_sched_upgrade_banner_clicked')" src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/upgrade_banner.png"/>
    </div>
  <?php } ?>
  -->
  <div class="vcita-wrap-inner">
    <div class="vcita__page__inner__section">
      <h3>
        <?php echo __('Hi', 'meeting-scheduler-by-vcita') ?> <?php if (!empty($uid)) echo !empty($wpshd_vcita_widget['name']) ? $wpshd_vcita_widget['name'] : $wpshd_vcita_widget['email']; ?>
        <small>
          <?php echo __('No worries! We are here to assist', 'meeting-scheduler-by-vcita') ?>
        </small>
      </h3>
    </div>
    <div class="vcita__page__inner__section-divided flex hspaced">
      <section>
        <div class="vcita__support__inner__section-banner-img flex vcentered hcentered">
          <img style="margin-bottom:13px" src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/assets/contact-us.svg" alt="contact us"/>
        </div>
        <h3 class="hcentered">
          <?php echo __('Contact Us', 'meeting-scheduler-by-vcita') ?>
          <small>
            <?php echo __('Have questions? email our support team and get answers to any question you may have', 'meeting-scheduler-by-vcita') ?>
          </small>
        </h3>
        <div class="hcentered">
          <a href="https://support.<?php echo WPSHD_VCITA_SERVER_BASE ?>/hc/en-us" target="_blank" onclick="VcitaMixpman.track('wp_sched_contact_support')">
            <?php echo __('Contact Us', 'meeting-scheduler-by-vcita') ?>
          </a>
        </div>
      </section>
      <section>
        <div class="vcita__support__inner__section-banner-img flex vcentered hcentered">
          <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/assets/rate-us.svg" alt="rate us"/>
        </div>
        <h3 class="hcentered">
          <?php echo __('Rate Us', 'meeting-scheduler-by-vcita') ?>
          <small>
            <?php echo __('Enjoying vcita Scheduling? Please rate us 5 stars and help us spread the word', 'meeting-scheduler-by-vcita') ?>
          </small>
        </h3>
        <div class="hcentered">
          <a href="https://wordpress.org/support/plugin/meeting-scheduler-by-vcita/reviews/" target="_blank" onclick="VcitaMixpman.track('wp_sched_rate')">
            <?php echo __('Rate Now', 'meeting-scheduler-by-vcita') ?>
          </a>
        </div>
      </section>
    </div>
    <div class="vcita__page__inner__section">
      <h3>
        <?php echo __('Frequetly Asked Questions', 'meeting-scheduler-by-vcita') ?>
      </h3>
      <div>
        <ul class="vcita__plus__list">
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo __('What is a service (also known as a meeting type / event type)?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo __('When a client books a meeting he has to choose a meeting type (aka service) from your service list. Each new vCita account is configured with several default services that you can customize according to your needs. You can also create as many additional services as you require. To learn more click', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://support.<?php echo WPSHD_VCITA_SERVER_BASE ?>/hc/en-us/articles/227896348-Configuring-your-services-menu" target="_blank">
                <?php echo __('here', 'meeting-scheduler-by-vcita') ?>
              </a>.
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo __('Can clients book an appointment with me outside my working hours?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo __('When using online scheduling your clients will be able to book an appointment at any time. However, you can customize the hours of availability to your liking. By default the set working days & hours are Mon-Fri 9am-5pm.', 'meeting-scheduler-by-vcita') ?>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo __('Can I set different working hours for certain dates?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo __('Yes. You can change and alter your availability status according to your needs. Check it out', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://app.<?php echo WPSHD_VCITA_SERVER_BASE ?>/app/settings/settings__my_availability" target="_blank">
                <?php echo __('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo __('Can I set specific working days & hours for a specific service?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo __('Yes. you can customize and align your working hours per service to your availability. For example: if you provide manicure services only on Mondays and Thursdays you can define specific availability only for that service.', 'meeting-scheduler-by-vcita') ?>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo __('Do I need to confirm the meeting after a client schedules online?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo __('By default the meeting requests are automatically accepted (i.e. your clients will get a booking confirmation right after they schedule). If you wish, you can turn off the auto accept feature and manually confirm / decline every meeting request. Turn off auto accept', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://app.<?php echo WPSHD_VCITA_SERVER_BASE ?>/app/settings/services?tab=settings" target="_blank">
                <?php echo __('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo __('Can I send reminders to clients?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo __('Yes, By default your clients will get a reminder email 30 minutes before the meeting. You can change how many minutes / hours / days in advance, they will get the alert. You can also create another reminder if needed. Reminders can also be sent via sms. Customize your reminders', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://app.<?php echo WPSHD_VCITA_SERVER_BASE ?>/app/settings/messages" target="_blank">
                <?php echo __('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo __('Can I sync my calendar with vCita?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo __('Yes, vCita’s calendar enables you to sync with all other calendars such as Gmail, iCal, Outlook etc. With a click of a button your vCita calendar will be integrated to your prepared calendar. You will be able to see all the meetings that are scheduled on vcita, and most importantly avoid double bookings. You can sync your calendar', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://app.<?php echo WPSHD_VCITA_SERVER_BASE ?>/app/settings/calendar_settings" target="_blank">
                <?php echo __('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo __('I want to use vcita’s scheduling button just on specific pages on my site. Is it possible?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo __('Yes. Instructions are available', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://support.<?php echo WPSHD_VCITA_SERVER_BASE ?>/hc/en-us/articles/227892408-Add-the-Client-Portal-Widget-on-Selected-Pages-Wordpress" target="_blank">
                <?php echo __('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo __('What information can I ask my clients to provide during scheduling?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo __('vCita enables you to fully personalize your meeting form. You can ask your clients to provide any type of information while they schedule a meeting. For example: a phone number, address, date of birth etc. You can customize your meeting form', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://app.<?php echo WPSHD_VCITA_SERVER_BASE ?>/app/settings/client_card" target="_blank">
                <?php echo __('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header><?php echo __('Is vcita HIPAA compliant?', 'meeting-scheduler-by-vcita') ?></header>
            <section><?php echo __('Yes, vcita is HIPAA compliant.', 'meeting-scheduler-by-vcita') ?></section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header><?php echo __('More unanswered questions?', 'meeting-scheduler-by-vcita') ?></header>
            <section>
              <?php echo __('Head to', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://www.<?php echo WPSHD_VCITA_SERVER_BASE ?>/FAQ" target="_blank">vcita.com/FAQ
              </a>
            </section>
          </li>
        </ul>
      </div>
    </div>
    <?php require_once WP_PLUGIN_DIR . '/' . WPSHD_VCITA_WIDGET_UNIQUE_ID . '/php_assets/admin_footer.php'; ?>
  </div>
</div>
<script type="text/javascript">
  jQuery('.vcita__plus__toggle').click(() => {
    VcitaMixpman.track('wp_sched_read_answer')
  })
</script>
