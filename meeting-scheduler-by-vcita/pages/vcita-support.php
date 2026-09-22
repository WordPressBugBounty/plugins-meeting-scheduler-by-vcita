<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
$wpshd_vcita_uid = $wpshd_vcita_widget["uid"];
?>
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', () => {
  })
</script>
<div class="vcita-wrap" dir="ltr">
  <?php require_once WP_PLUGIN_DIR . '/' . WPSHD_VCITA_WIDGET_UNIQUE_ID . '/php_assets/admin_header.php'; ?>
  <!--
  <?php if ($wpshd_vcita_widget['uid']) { ?>
    <div class="vcita-wrap-left-banner">
      <img onclick="vcita_show_av_plans(event,'wp_sched_upgrade_banner_clicked')" src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ) ?>/images/upgrade_banner.png"/>
    </div>
  <?php } ?>
  -->
  <div class="vcita-wrap-inner">
    <div class="vcita__page__inner__section">
      <h3>
        <?php echo esc_html__('Hi', 'meeting-scheduler-by-vcita') ?> <?php if (!empty($wpshd_vcita_uid)) echo esc_html( !empty($wpshd_vcita_widget['name']) ? $wpshd_vcita_widget['name'] : $wpshd_vcita_widget['email'] ); ?>
        <small>
          <?php echo esc_html__('No worries! We are here to assist', 'meeting-scheduler-by-vcita') ?>
        </small>
      </h3>
    </div>
    <div class="vcita__page__inner__section-divided flex hspaced">
      <section>
        <div class="vcita__support__inner__section-banner-img flex vcentered hcentered">
          <img style="margin-bottom:13px" src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ) ?>/images/assets/contact-us.svg" alt="contact us"/>
        </div>
        <h3 class="hcentered">
          <?php echo esc_html__('Contact Us', 'meeting-scheduler-by-vcita') ?>
          <small>
            <?php echo esc_html__('Have questions? email our support team and get answers to any question you may have', 'meeting-scheduler-by-vcita') ?>
          </small>
        </h3>
        <div class="hcentered">
          <a href="https://support.<?php echo esc_attr( WPSHD_VCITA_SERVER_BASE ) ?>/hc/en-us" target="_blank">
            <?php echo esc_html__('Contact Us', 'meeting-scheduler-by-vcita') ?>
          </a>
        </div>
      </section>
      <section>
        <div class="vcita__support__inner__section-banner-img flex vcentered hcentered">
          <img src="<?php echo esc_url( WPSHD_VCITA_ASSETS_PATH ) ?>/images/assets/rate-us.svg" alt="rate us"/>
        </div>
        <h3 class="hcentered">
          <?php echo esc_html__('Rate Us', 'meeting-scheduler-by-vcita') ?>
          <small>
            <?php echo esc_html__('Enjoying vcita Scheduling? Please rate us 5 stars and help us spread the word', 'meeting-scheduler-by-vcita') ?>
          </small>
        </h3>
        <div class="hcentered">
          <a href="https://wordpress.org/support/plugin/meeting-scheduler-by-vcita/reviews/" target="_blank">
            <?php echo esc_html__('Rate Now', 'meeting-scheduler-by-vcita') ?>
          </a>
        </div>
      </section>
    </div>
    <div class="vcita__page__inner__section">
      <h3>
        <?php echo esc_html__('Frequetly Asked Questions', 'meeting-scheduler-by-vcita') ?>
      </h3>
      <div>
        <ul class="vcita__plus__list">
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo esc_html__('What is a service (also known as a meeting type / event type)?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo esc_html__('When a client books a meeting he has to choose a meeting type (aka service) from your service list. Each new vCita account is configured with several default services that you can customize according to your needs. You can also create as many additional services as you require. To learn more click', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://support.<?php echo esc_attr( WPSHD_VCITA_SERVER_BASE ) ?>/hc/en-us/articles/227896348-Configuring-your-services-menu" target="_blank">
                <?php echo esc_html__('here', 'meeting-scheduler-by-vcita') ?>
              </a>.
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo esc_html__('Can clients book an appointment with me outside my working hours?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo esc_html__('When using online scheduling your clients will be able to book an appointment at any time. However, you can customize the hours of availability to your liking. By default the set working days & hours are Mon-Fri 9am-5pm.', 'meeting-scheduler-by-vcita') ?>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo esc_html__('Can I set different working hours for certain dates?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo esc_html__('Yes. You can change and alter your availability status according to your needs. Check it out', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://app.<?php echo esc_attr( WPSHD_VCITA_SERVER_BASE ) ?>/app/settings/settings__my_availability" target="_blank">
                <?php echo esc_html__('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo esc_html__('Can I set specific working days & hours for a specific service?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo esc_html__('Yes. you can customize and align your working hours per service to your availability. For example: if you provide manicure services only on Mondays and Thursdays you can define specific availability only for that service.', 'meeting-scheduler-by-vcita') ?>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo esc_html__('Do I need to confirm the meeting after a client schedules online?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo esc_html__('By default the meeting requests are automatically accepted (i.e. your clients will get a booking confirmation right after they schedule). If you wish, you can turn off the auto accept feature and manually confirm / decline every meeting request. Turn off auto accept', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://app.<?php echo esc_attr( WPSHD_VCITA_SERVER_BASE ) ?>/app/settings/services?tab=settings" target="_blank">
                <?php echo esc_html__('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo esc_html__('Can I send reminders to clients?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo esc_html__('Yes, By default your clients will get a reminder email 30 minutes before the meeting. You can change how many minutes / hours / days in advance, they will get the alert. You can also create another reminder if needed. Reminders can also be sent via sms. Customize your reminders', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://app.<?php echo esc_attr( WPSHD_VCITA_SERVER_BASE ) ?>/app/settings/messages" target="_blank">
                <?php echo esc_html__('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo esc_html__('Can I sync my calendar with vCita?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo esc_html__('Yes, vCita’s calendar enables you to sync with all other calendars such as Gmail, iCal, Outlook etc. With a click of a button your vCita calendar will be integrated to your prepared calendar. You will be able to see all the meetings that are scheduled on vcita, and most importantly avoid double bookings. You can sync your calendar', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://app.<?php echo esc_attr( WPSHD_VCITA_SERVER_BASE ) ?>/app/settings/calendar_settings" target="_blank">
                <?php echo esc_html__('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo esc_html__('I want to use vcita’s scheduling button just on specific pages on my site. Is it possible?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo esc_html__('Yes. Instructions are available', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://support.<?php echo esc_attr( WPSHD_VCITA_SERVER_BASE ) ?>/hc/en-us/articles/227892408-Add-the-Client-Portal-Widget-on-Selected-Pages-Wordpress" target="_blank">
                <?php echo esc_html__('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header>
              <?php echo esc_html__('What information can I ask my clients to provide during scheduling?', 'meeting-scheduler-by-vcita') ?>
            </header>
            <section>
              <?php echo esc_html__('vCita enables you to fully personalize your meeting form. You can ask your clients to provide any type of information while they schedule a meeting. For example: a phone number, address, date of birth etc. You can customize your meeting form', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://app.<?php echo esc_attr( WPSHD_VCITA_SERVER_BASE ) ?>/app/settings/client_card" target="_blank">
                <?php echo esc_html__('here', 'meeting-scheduler-by-vcita') ?>
              </a>
            </section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header><?php echo esc_html__('Is vcita HIPAA compliant?', 'meeting-scheduler-by-vcita') ?></header>
            <section><?php echo esc_html__('Yes, vcita is HIPAA compliant.', 'meeting-scheduler-by-vcita') ?></section>
          </li>
          <li>
            <input type="checkbox" class="vcita__plus__toggle"><label></label>
            <header><?php echo esc_html__('More unanswered questions?', 'meeting-scheduler-by-vcita') ?></header>
            <section>
              <?php echo esc_html__('Head to', 'meeting-scheduler-by-vcita') ?>&nbsp;
              <a href="https://www.<?php echo esc_attr( WPSHD_VCITA_SERVER_BASE ) ?>/FAQ" target="_blank">vcita.com/FAQ
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
  })
</script>
