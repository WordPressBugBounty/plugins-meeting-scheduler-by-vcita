<?php
  $wpshd_vcita_widget = (array)get_option(WPSHD_VCITA_WIDGET_KEY);
  if (!isset($wpshd_vcita_widget['wp_id'])) $wpshd_vcita_widget['wp_id'] = '';
  $needs_reconnect = wpshd_vcita_check_need_to_reconnect($wpshd_vcita_widget);
?>
<script type="text/javascript">
  window.$_ajaxurl = '<?php echo admin_url('admin-ajax.php') ?>';
  window.$_adminurl = '<?php echo admin_url('admin.php') ?>';
  window.WPSHD_VCITA_LOCALE = '<?php echo get_locale() ?>';
  window.WPSHD_VCITA_WIDGET_ID = '<?php echo WPSHD_VCITA_WIDGET_UNIQUE_ID?>';

  if (window.WPSHD_VCITA_LOCALE === 'en_GB') {
    window.WPSHD_VCITA_LOCALE = 'en-gb';
  } else {
    window.WPSHD_VCITA_LOCALE = window.WPSHD_VCITA_LOCALE.split('_')[0];
  }

  window.WPSHD_VCITA_DEBUG = false;
  window.WPSHD_VCITA_ROOT = '<?php echo WPSHD_VCITA_WIDGET_UNIQUE_ID ?>';
  window.WPSHD_VCITA_DATA = <?php echo json_encode($wpshd_vcita_widget)?>;
  window.WPSHD_VCITA_SERVER_BASE = '<?php echo WPSHD_VCITA_SERVER_BASE ?>';
  window.WPSHD_VCITA_USE_MEET2KNOW = <?php echo WPSHD_VCITA_USE_MEET2KNOW ? 'true' : 'false' ?>;
  window.addEventListener('wpshd_vcita_widget_installed', () => { window.WPSHD_VCITA_WIDGET_INSTALLED = true })

  function wpshd_vcita_redirect(url) {
    const base = '<?php echo WPSHD_VCITA_SERVER_BASE ?>';
    return `https://app.${base}${url}`
  }
<?php if (WPSHD_VCITA_DEBUG) { ?>
  window.WPSHD_VCITA_DEBUG = true;
  console.log(window.WPSHD_VCITA_DATA);
<?php } ?>
  function vcita_show_av_plans(ev, ev_name) {
    ev.preventDefault();
    ev.stopPropagation();
    <?php if (isset($wpshd_vcita_widget['uid']) && $wpshd_vcita_widget['uid']) { ?>
    const url = wpshd_vcita_redirect('/app/settings/upgrade_page');
    <?php } else { ?>
    const url = 'https://www.<?php echo WPSHD_VCITA_SERVER_BASE; ?>/pricing';
    <?php } ?>

    // removed because vcita can not be open in iframe
    // VcitaUI.constructPopup(
    //   { useWrapper: true, width: '100%', height: '100%' },
    //   `<iframe src="${url}"></iframe>`
    // )

    // added instead of the popup/dialog to be opened in a new tab
    window.open(url, '_blank');

    if (ev_name) {
      VcitaMixpman.track(ev_name);
    }
  }
  document.addEventListener('DOMContentLoaded', () => {
    const vc = document.querySelector('.vcita-wrap');
    if (vc != null) vc.style.opacity = '1';
  });

  window.WPSHD_VCITA_VERSION = '<?php echo WPSHD_VCITA_WIDGET_VERSION; ?>';
  window.VcitaUI = new UIController('<?php echo plugins_url(WPSHD_VCITA_WIDGET_UNIQUE_ID) ?>',<?php echo json_encode($wpshd_vcita_widget) ?>);
  window.VcitaApi = new ApiController('<?php echo $wpshd_vcita_widget['wp_id'] ?>', '<?php echo WPSHD_VCITA_WIDGET_CALLBACK_URL ?>');
  window.VcitaMixpman = new MixpMan('78aa39b3aa49594f172cfccda537ef1a', '<?php echo $wpshd_vcita_widget['wp_id']?>', '<?php echo $wpshd_vcita_widget['email']?>');

  window.dispatchEvent(new CustomEvent('vcita_api_initialized', {
    bubbles: true,
    detail: { VcitaApi: VcitaApi }
  }));

  if (URLSearchParams) {
    const search_params = new URLSearchParams(window.location.search);

    if (search_params.has('show_login')) {
      search_params.delete('show_login');

      if (window.history.replaceState) {
        window.history.replaceState({}, null, window.location.origin + window.location.pathname + '?' + search_params.toString());
      }
    }
  }
  <?php if (isset($_GET['show_login']) && (!isset($wpshd_vcita_widget['uid']) || !$wpshd_vcita_widget['uid'])) echo 'window.VcitaUI.openAuthWin(true);'; ?>
  <?php if (isset($wpshd_vcita_widget['uid']) && $wpshd_vcita_widget['uid']) { ?>
  (function(){
    try {
      let rdata = localStorage.getItem('wpshd_rate_state');
      if (rdata !== null) {
        rdata = JSON.parse(rdata);
        if (!rdata['<?php echo $wpshd_vcita_widget['uid'] ?>']) {
          rdata['<?php echo $wpshd_vcita_widget['uid'] ?>'] = {
            dismiss: false,
            wait: false,
            date: new Date().getTime(),
            version: '<?php echo WPSHD_VCITA_WIDGET_VERSION; ?>'
          }
        }
      } else {
        rdata = {
          '<?php echo $wpshd_vcita_widget['uid'] ?>': {
            dismiss: false,
            wait: false,
            date: new Date().getTime(),
            version: '<?php echo WPSHD_VCITA_WIDGET_VERSION; ?>'
          }
        };
      }

      const rd = rdata['<?php echo $wpshd_vcita_widget['uid'] ?>'];
      if (rd.dismiss && rd.version === '<?php echo WPSHD_VCITA_WIDGET_VERSION; ?>') {
        return false;
      }
      if (rd.wait) {
        const day = 24 * 60 * 60 * 1000;
        const diffDays = Math.round(Math.abs((rd.date - new Date().getTime()) / day));
        if (diffDays < 2) {
          return false;
        }
      }
      document.addEventListener('DOMContentLoaded', () => {
        window.VcitaUI.constructPopup({}, wpshd_rate_popup());
      })
    } catch (e) {
      if (window.WPSHD_VCITA_DEBUG) console.log(e)
    }
  })();
  <?php } ?>
<?php if (
  $wpshd_vcita_widget['new_install'] &&
  (!isset($wpshd_vcita_widget['track_new']) || !$wpshd_vcita_widget['track_new'])
) { ?>
  VcitaMixpman.track('wp_sched_added_to_new_site');

  <?php
    $wpshd_vcita_widget['track_new'] = true;
    update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
  ?>
<?php } ?>
  if (!VcitaApi.isInstalled()) {
    VcitaApi.install().then((data) => {
      <?php if (WPSHD_VCITA_DEBUG) echo 'console.log(data);' . PHP_EOL ?>

      window.dispatchEvent(new CustomEvent('wpshd_vcita_widget_installed', {
        bubbles: true,
        detail: { data: data }
      }));

      if (data.success && data.wp_id) {
        VcitaApi.wpid = data.wp_id;
        vcita_sync_business();
        VcitaApi.saveData('wp_id', data.wp_id).then((res) => {});
      }
    }).catch(function (err) { console.log(err) });
  } else {
    VcitaApi.install()
      .then(() => vcita_sync_business());
  }

<?php if (
  $wpshd_vcita_widget['migrated'] &&
  (!isset($wpshd_vcita_widget['migrated_popup_showed']) || !$wpshd_vcita_widget['migrated_popup_showed']) &&
  !WPSHD_VCITA_ANOTHER_PLUGIN
) { ?>
  // VcitaUI.constructPopup({}, VcitaUI.getMigratedPopupHTML(<?php echo json_encode($wpshd_vcita_widget) ?>));
  VcitaMixpman.track('wp_sched_upgrade');
  <?php
    $wpshd_vcita_widget['migrated_popup_showed'] = true;
    update_option(WPSHD_VCITA_WIDGET_KEY, $wpshd_vcita_widget);
  ?>
<?php } ?>
  function vcita_sync_business() {
  <?php if ($wpshd_vcita_widget['uid'] && !$needs_reconnect) { ?>
    VcitaApi.getBusiness()
      .then((data) => {
        if (WPSHD_VCITA_DEBUG) console.log(data);
        const plan = data.plan;
        if (!plan) return;

        const ac = document.querySelector('.vcita__page__alert-container');
        if (ac === null) return false;
        ac.innerHTML = '';

        if (typeof plan.expires_on === 'string') {
          const d = new Date(plan.expires_on);
          const now = new Date();
          const vcita_premium_url = '<?php echo get_admin_url('','','admin').'admin.php?page='.WPSHD_VCITA_WIDGET_UNIQUE_ID.'/vcita-premium.php' ?>';

          if (d.getTime() < now.getTime()) {
            ac.classList.add('vcita-danger');
            ac.innerHTML = `${data.business_data.name}, your trial ended. Please upgrade to enjoy vcita&nbsp;&nbsp;<a href="${vcita_premium_url}">Upgrade now</a>`;
          } else {
            const day = 24 * 60 * 60 * 1000;
            const diffDays = Math.round(Math.abs((d - now) / day));

            if (diffDays <= 5) {
              ac.classList.add('vcita-warning');
              ac.innerHTML = `${data.business_data.name}, your trial ends in ${diffDays} days&nbsp;&nbsp;<a href="${vcita_premium_url}">Upgrade now</a>`;
            }
          }

          if (URLSearchParams) {
            const search_params = new URLSearchParams(window.location.search);

            if (search_params.has('registered') && search_params.get('registered') == 'true') {
              VcitaUI.constructPopup({
                // onClose: (eve) => { VcitaUI.openEditSettingsWin(eve) }
              }, VcitaUI.getRegPopupHTML(WPSHD_VCITA_DATA));

              search_params.delete('registered');

              if (window.history.replaceState) {
                window.history.replaceState({}, null, window.location.origin + window.location.pathname + '?' + search_params.toString());
              }
            }
          }
        }

        if (plan.plan_name && plan.plan_name.toLowerCase() === 'trial') {
          localStorage.setItem('wpshd_is_trial', 1);
          document.cookie = "wpshd_is_trial=1; expires = Thu, 01 Jan 9999 00:00:00 GMT";
          jQuery('.vcita-btn-upgrade').show();
        } else {
          localStorage.removeItem('wpshd_is_trial');
          document.cookie = "wpshd_is_trial= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
        }
      })
      .catch((err) => { console.log(err) });
    <?php } ?>
  }

  jQuery(function ($) {
    $('.start-login')
      .click(function (ev) {
        ev.preventDefault();
        ev.stopPropagation();
        VcitaMixpman.track('wp_sched_login_vcita');
        VcitaUI.openAuthWin(false, false);
        if (ev.target.classList.contains('reconnect')) VcitaMixpman.track('wp_sched_reconnect_click');
      });
    $('.start-signup')
      .click(function () {
        VcitaMixpman.track('wp_sched_join_vcita', {tab: 'main'});
        VcitaUI.openAuthWin(true, false);
      });
    $('.start-signup-add')
      .click(function () {
        VcitaMixpman.track('wp_sched_join_vcita', {tab: 'add'});
        VcitaUI.openAuthWin(true, false);
      });
    $('#switch-account')
      .click(function () {
        VcitaMixpman.track('wp_sched_logout')
        jQuery.post(`${window.$_ajaxurl}?action=vcita_logout`);
        VcitaUI.openAuthWin(false, true);
      });
    $('#vcita__input-email').keypress(function (e) { if (e.keyCode == 13) $('#start-signup').click() });
  });
</script>
