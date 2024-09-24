<?php

function wpshd_vcita_add_active_engage()
{
    wp_enqueue_style('vcita-widget-style', WPSHD_VCITA_CSS_PATH.'/widget_v.css');

    $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
    $wpshd_vcita_widget_config = create_default_settings_data($wpshd_vcita_widget);
    foreach ($wpshd_vcita_widget_config as $akey => $val) $wpshd_vcita_widget[$akey] = $val;
    if (WPSHD_VCITA_ANOTHER_PLUGIN) $wpshd_vcita_widget['show_on_site'] = 0;
    $wpshd_disable = false;

    if (isset($_GET['WPSCHD_DISABLE_BUTTON']) && $_GET['WPSCHD_DISABLE_BUTTON'] == '1') {
      $wpshd_disable = true;
    }

    if ($wpshd_vcita_widget['show_on_site'] && !$wpshd_disable) { ?>
      <?php if (!$wpshd_vcita_widget['vcita_design']) { ?>
        <style>#livesite_active_engage .ls-more-actions-C {display: none}</style>
      <?php } ?>
      <script type="text/javascript">
        var vcUrl = '<?php echo WPSHD_VCITA_SERVER_URL ?>/widgets/active_engage/<?php echo wpshd_vcita_get_uid() ?>/loader.js?format=js';
        var script = document.createElement('script');
        script.src = '//' + vcUrl;
        script.type = 'text/javascript';

        document.addEventListener('DOMContentLoaded', () => {
          const scripts = document.querySelectorAll('script[src]');
          let sfound = false;

          for (let i = 0; i < scripts.length; i++) {
            if ((scripts[i].getAttribute('src').indexOf('vcita.com') >= 0 &&
              scripts[i].getAttribute('src').indexOf('livesite.js') >= 0) ||
              (scripts[i].getAttribute('src').indexOf('vcita.com') >= 0 &&
                scripts[i].getAttribute('src').indexOf('loader.js') >= 0)
            ) {
              sfound = true;
              break
            }
          }

          if (sfound) return;

          <?php if (!$wpshd_vcita_widget['vcita_design']) { ?>
          document.cookie = "livesite_<?php echo $wpshd_vcita_widget['uid'] ?>_engage=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
          <?php } ?>

          
            <?php if (!$wpshd_vcita_widget['vcita_design']) { ?>
			          window.Vcita = {};
			Vcita.legacyOptions = function () {
            return {
              desktopEnabled: 1,
              mobileEnabled: 1,
              engageButton: true,
              activeEngage: true,
              engageState: 'closed',
              actionButtons: false,
              // inlineActions: false,
              activeEngageAction: "schedule",
              //mobileQuickAction: "schedule",
              activeEngageActionText: '<?php echo $wpshd_vcita_widget['btn_text'] ? $wpshd_vcita_widget['btn_text'] : 'Schedule now'?>',
              engageButtonText: '<?php echo $wpshd_vcita_widget['btn_text'] ? stripslashes($wpshd_vcita_widget['btn_text']) : 'Schedule now'?>',
              activeEngageTitle: "<?php echo $wpshd_vcita_widget['widget_title'] ? stripslashes($wpshd_vcita_widget['widget_title']) : 'Let\'s talk!' ?>",
              activeEngageText: "<?php echo $wpshd_vcita_widget['widget_text'] ? stripslashes($wpshd_vcita_widget['widget_text']) : 'Thanks for stopping by! We\'re here to help…' ?>",
            <?php if ($wpshd_vcita_widget['widget_img']) { ?>
              imageUrl: "<?php echo wp_get_attachment_image_url($wpshd_vcita_widget['widget_img']) ?>",
            <?php } else { ?>
              imageUrl: "",
            <?php } ?>
              textPoweredBy: 'Powered by vcita',
              themeActionColor: '<?php echo $wpshd_vcita_widget['btn_color'] ?>',
              themeActionHover: '<?php echo $wpshd_vcita_widget['hover_color'] ?>',
              themeActionText: '<?php echo $wpshd_vcita_widget['txt_color'] ?>',
              themeMainActionColor: '<?php echo $wpshd_vcita_widget['btn_color'] ?>',
              themeMainActionHover: '<?php echo $wpshd_vcita_widget['hover_color'] ?>',
              themeMainActionText: '<?php echo $wpshd_vcita_widget['txt_color'] ?>'
			  }
          };
            <?php } ?>
            

        <?php if ($wpshd_vcita_widget['widget_show'] && !$wpshd_vcita_widget['vcita_design']) { ?>
          const checkLivesite = () => {
            if (!window.LiveSite) return false;
            setTimeout(window.LiveSite.ui.showActiveEngage, 5000);
            return true;
          };

          const checkEngageButton = () => {
            const leb = document.querySelector('#livesite_engage_button a.ls-engage-button');

            if (leb != null) {
              return true;
            } else return false;
          };

          let tryit = 0, aeint;

          let lint = setInterval(() => {
            if (checkLivesite() || tryit == 100000) {
              clearInterval(lint);
              tryit = 0;

              aeint = setInterval(() => {
                if (checkEngageButton() || tryit == 100000) {
                  clearInterval(aeint);
                } else tryit++;
              }, 10);
            } else tryit++;
          }, 100);
        <?php } else { ?>
          const checkLivesite = () => {
            if (!window.LiveSite) return false;
              // console.log(window.Vcita.legacyOptions());
              // console.log(window.LiveSite);
            return true;
          };
          <?php if (!$wpshd_vcita_widget['vcita_design']) { ?>
          const checkEngageButton = () => {
            const leb = document.querySelector('#livesite_engage_button a.ls-engage-button');

            if (leb != null) {
              <?php if ($wpshd_vcita_widget['migrated']) { ?>
              const lma = document.querySelector('#livesite_active_engage .ls-more-actions-C');
              if (lma != null) lma.style.display = 'block !important';
              <?php } ?>
              leb.classList.remove('livesite-engage');
              leb.classList.add('livesite-schedule');
              // leb.onclick = LiveSite.schedule;
              return true;
            } else return false;
          };

          let tryit = 0, aeint;

          let lint = setInterval(() => {
            if (checkLivesite() || tryit == 100000) {
              clearInterval(lint);
              tryit = 0;

              aeint = setInterval(() => {
                if (checkEngageButton() || tryit == 100000) {
                  clearInterval(aeint);
                } else tryit++;
              }, 10);
            } else tryit++;
          }, 100);
          <?php } else if ($wpshd_vcita_widget['migrated']) { ?>
          const checkEngageButton = () => {
            const leb = document.querySelector('#livesite_engage_button a.ls-engage-button');

            if (leb != null) {
              const lma = document.querySelector('#livesite_active_engage .ls-more-actions-C');
              if (lma != null) lma.style.display = 'block';
              return true;
            } else return false;
          };

          let tryit = 0, aeint;

          let lint = setInterval(() => {
            if (checkLivesite() || tryit == 100000) {
              clearInterval(lint);
              tryit = 0;

              aeint = setInterval(() => {
                if (checkEngageButton() || tryit == 100000) {
                  clearInterval(aeint);
                } else tryit++;
              }, 10);
            } else tryit++;
          }, 100);
          <?php } ?>
        <?php } ?>

          document.body.appendChild(script)
        });
      </script>
    <?php }
} ?>
