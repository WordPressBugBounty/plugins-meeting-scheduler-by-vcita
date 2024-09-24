<?php
wp_enqueue_script('vcita_prism', WPSHD_VCITA_ASSETS_PATH.'/assets/extra/prism/prism.js');
wp_enqueue_script('vcita-custom-script', WPSHD_VCITA_ASSETS_PATH.'/assets/js/custom_v.js');

wp_enqueue_style('vcita-prism', WPSHD_VCITA_ASSETS_PATH.'/assets/extra/prism/prism.css');
wp_enqueue_style('vcita-custom-style', WPSHD_VCITA_CSS_PATH. '/custom_v.css');

$wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
?>
<div class="vcita-wrap" dir="ltr">
  <?php require_once WP_PLUGIN_DIR . '/' . WPSHD_VCITA_WIDGET_UNIQUE_ID . '/php_assets/admin_header.php' ?>
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
        <?php echo __('Turn your own site elements into SMART booking buttons', 'meeting-scheduler-by-vcita') ?>
        <small>
          <?php echo __('Super simple HTML will enable you to use your own buttons and release the full power of vcita Scheduling.', 'meeting-scheduler-by-vcita') ?>
        </small>
      </h3>
    </div>
    <div class="vcita__page__inner__section">
      <div class="vcita__custom__inner__section-banner">
        <div class="vcita__page__inner__section-banner-img">
          <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/dark(1).jpg" alt="phone mockup"/>
        </div>
        <section class="vcita__custom__inner__section-wrapper">
          <h3>
            <?php echo __('Scheduling Button', 'meeting-scheduler-by-vcita') ?>
            <small>
              <?php echo __('Turn any element on your site into a scheduling button', 'meeting-scheduler-by-vcita') ?>
            </small>
          </h3>
        </section>
        <section class="vcita__custom__inner__section-wrapper">
          <h4>
            <?php echo __('You can design your own button and place it anywhere you want on your site. Once a visitor clicks on it, your scheduling page on vcita will open.', 'meeting-scheduler-by-vcita') ?>
          </h4>
        </section class="vcita__custom__inner__section-wrapper">
        <!--
        <section>
            <a href="javascript:void(0)" onclick="vcita_get_sample_html(this)">Show sample HTML</a>
        </section>
        -->
        <section class="vcita__custom__inner__section-wrapper">
          <div class="vcita__page__inner__section-code">
            <pre>
              <code class="language-markup">
                &lt;button class="livesite-schedule"&gt;
                <?php echo __('Schedule now', 'meeting-scheduler-by-vcita') ?>
                &lt;/button&gt;
              </code>
            </pre>
          </div>
        </section>
      </div>
    </div>
    <div class="vcita__page__inner__section">
      <div class="vcita__custom__inner__section-banner">
        <div class="vcita__page__inner__section-banner-img">
          <img src="<?php echo WPSHD_VCITA_ASSETS_PATH ?>/images/dark.jpg" alt="phone mockup"/>
        </div>
        <section class="vcita__custom__inner__section-wrapper">
          <h3>
            <?php echo __('Send Parameters', 'meeting-scheduler-by-vcita') ?>
            <small>
              <?php echo __('Ease the scheduling process by adding parameters', 'meeting-scheduler-by-vcita') ?>
            </small>
          </h3>
        </section>
        <section class="vcita__custom__inner__section-wrapper">
          <h4>
            <?php echo __('Instead of making your clients manually fill the meeting details (i.e. choosing the service, time, price etc), you can simply send those as parameters when the user initiates the scheduling process.', 'meeting-scheduler-by-vcita') ?>
          </h4>
        </section>
        <!--
        <section class="vcita__custom__inner__section-wrapper">
            <a href="javascript:void(0)" onclick="vcita_get_sample_html(this)">Show sample HTML</a>
        </section>
        -->
        <section class="vcita__custom__inner__section-wrapper">
          <div class="vcita__page__inner__section-code">
            <pre>
              <code class="language-markup">
                &lt;!-- <?php echo __('You can pass the parameters as one options string by using the data-options attribute', 'meeting-scheduler-by-vcita') ?> --&gt;<br>
                &lt;button class="livesite-schedule" data-options="date:01012020"&gt;
                <?php echo __('Schedule now', 'meeting-scheduler-by-vcita') ?>
                &lt;/button&gt;
              </code>
            </pre>
          </div>
        </section>
      </div>
    </div>
    <div class="vcita__page__inner__section">
      <h3>
        <?php echo __('Using vcita’s LiveSite SDK', 'meeting-scheduler-by-vcita') ?>
        <small>
          <?php echo __('vCita LiveSite SDK offers a wealth of engagement tools to drive more business and deliver amazing customer service through your website. Please review the', 'meeting-scheduler-by-vcita') ?>
          <a href="https://developers.intandem.tech/docs/website-embedded-actions" target="_blank" onclick="VcitaMixpman.track('wp_sched_read_more_faq')">
            <?php echo __('full documentation', 'meeting-scheduler-by-vcita') ?>
          </a>&nbsp;
          <?php echo __('for more details', 'meeting-scheduler-by-vcita') ?>
        </small>
      </h3>
      <div class="vcita__page__inner__section-code">
        <header>HTML</header>
        <pre>
          <code class="language-markup">
            &lt;!-- <?php echo __('You can pass the parameters as one options string by using the data-options attribute', 'meeting-scheduler-by-vcita') ?> --&gt;
            &lt;button class="livesite-contact" data-options="title:<?php echo __('Registration to your English Class', 'meeting-scheduler-by-vcita') ?>"&gt;<?php echo __('Register to English Class', 'meeting-scheduler-by-vcita') ?>&lt;/button&gt;
          </code>
        </pre>
      </div>
      <div class="vcita__page__inner__section-code">
        <header>JavaScript</header>
        <pre>
          <code class="language-js">LiveSite.schedule({email: email, first_name: first_name, last_name: last_name});</code>
        </pre>
      </div>
      <h4 style="margin-top:30px;margin-bottom:20px;font-weight:600">
        <?php echo __('Parameters list', 'meeting-scheduler-by-vcita') ?>
        <small>
          <?php echo __('Below you will find the parameters that you can pass with every scheduling action. Using those parameters will help you provide a quick and smooth scheduling experience', 'meeting-scheduler-by-vcita') ?>
        </small>
      </h4>
      <div>
        <table class="vcita__page__inner-table">
          <thead>
          <tr>
            <td><?php echo __('Option', 'meeting-scheduler-by-vcita') ?></td>
            <td><?php echo __('Description', 'meeting-scheduler-by-vcita') ?></td>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>date</td>
            <td>
              <?php echo __('Preselect date to show the scheduling on (MMDDYY or MMDDYYYY)', 'meeting-scheduler-by-vcita') ?>
            </td>
          </tr>
          <tr>
            <td>service</td>
            <td><?php echo __('Preselect a service by it\'s uid', 'meeting-scheduler-by-vcita') ?></td>
          </tr>
          <tr>
            <td>service_name</td>
            <td>
              <?php echo __('Show only services matching the name (only if service not provided)', 'meeting-scheduler-by-vcita') ?>
            </td>
          </tr>
          <tr>
            <td>staff</td>
            <td><?php echo __('Preselect staff by it\'s uid', 'meeting-scheduler-by-vcita') ?>'</td>
          </tr>
          <tr>
            <td>order</td>
            <td>
              <?php echo __('services_first (default) to show the service selection step before a staff is selected or staff_first to show the staff selection step first and then show the services selection according to the selected staff member', 'meeting-scheduler-by-vcita') ?>
            </td>
          </tr>
          <tr>
            <td>email</td>
            <td>
              <?php echo __('Pre-fill email for the appointment', 'meeting-scheduler-by-vcita') ?>
            </td>
          </tr>
          <tr>
            <td>first_name</td>
            <td>
              <?php echo __('Pre-fill first_name for the appointment', 'meeting-scheduler-by-vcita') ?>
            </td>
          </tr>
          <tr>
            <td>last_name</td>
            <td>
              <?php echo __('Pre-fill last_name for the appointment', 'meeting-scheduler-by-vcita') ?>
            </td>
          </tr>
          <tr>
            <td>phone</td>
            <td><?php echo __('Pre-fill phone for the appointment', 'meeting-scheduler-by-vcita') ?></td>
          </tr>
          <tr>
            <td>agenda</td>
            <td><?php echo __('Pre-fill agenda for the appointment', 'meeting-scheduler-by-vcita') ?></td>
          </tr>
          <tr>
            <td>source</td>
            <td>
              <?php echo __('Action source, will be displayed in the engagement', 'meeting-scheduler-by-vcita') ?>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <?php require_once WP_PLUGIN_DIR.'/'.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/php_assets/admin_footer.php'; ?>
  </div>
</div>


