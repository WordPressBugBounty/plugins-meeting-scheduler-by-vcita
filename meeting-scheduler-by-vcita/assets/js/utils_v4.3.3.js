const UIController = function (homeUrl, widget_data = {}) {
  const DEBUG = window.WPSHD_VCITA_DEBUG;
  const _this = this;
  this.widget_data = widget_data;
  this.HOME_URL = homeUrl;
  this.WPSHD_VCITA_URL = `https://app.${window.WPSHD_VCITA_SERVER_BASE}`;

  this.isSafari = function () {
    let isChrome = navigator.userAgent.indexOf('Chrome') > -1;
    let isSafari = navigator.userAgent.indexOf('Safari') > -1;
    if ((isChrome) && (isSafari)) isSafari = false;
    return isSafari
  };

  this.getRegPopupHTML = function (ud) { return wpshd_reg_popup(this.HOME_URL, ud) };
  this.getReconnectPopupHTML = function () { return wpshd_reconnect_popup() };
  this.getMigratedPopupHTML = function (ud) { return wpshd_migrated_popup(this.HOME_URL, ud) };
  this.getDeactivationPopupHTML = function (ud) { return wpshd_deactivation_popup(this.HOME_URL) };

  this.openAlert = function (txt) {
    _this.constructPopup({userWrapper: true}, `<div class="vcita_alert_wrapper">${txt}</div>`);
  };

  this.openAuthWin = (reg = false, logout = false) => {
    VcitaApi.getAuthURL(reg, logout)
      .then((new_location) => {
        const onClose = () => {
          jQuery.ajax({
            'url': `${window.$_ajaxurl}?action=vcita_check_auth`,
            'method': 'POST',
            'success': (resp) => {
              try {
                const ud = JSON.parse(resp);
                if (typeof ud.uid == 'string' && ud.uid.length > 0) {
                  if (!reg) {
                    localStorage.removeItem('wpshd_is_trial');
                    document.cookie = "wpshd_is_trial= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
                    window.location.reload();
                    return false
                  } else {
                    VcitaMixpman.track('wp_vpay_completed_registration');
                    VcitaMixpman.track('wp_vpay_welcome');
                    window.location = `${window.$_adminurl}?page=${window.WPSHD_VCITA_WIDGET_ID}${encodeURIComponent('/vcita-settings-functions.php')}&registered=true`
                  }
                }
              } catch (err) { console.log(err) }
            },
          })
          if (logout) {
            localStorage.removeItem('wpshd_is_trial');
            document.cookie = "wpshd_is_trial= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
            window.location.reload();
            return false
          }
        };
        if (!_this.isSafari()) {
          _this.constructPopup({
            useWrapper: true,
            title: 'vCita authentication',
            width: '100%',
            height: '100%',
            iframe_onclose: onClose
          }, `<iframe src=${new_location}></iframe>`);
        } else {
          const win = window.open(new_location, '_blank');
          win.onclose = onClose;
        }
      })
  };

  this.openEditAvailabilityWin = (event) => {
    event.preventDefault();
    event.stopPropagation();
    VcitaMixpman.track('wp_vpay_edit_availability');
    _this.constructPopup({
      useWrapper: true,
      title: 'vCita edit calendar and availability',
      width: '100%',
      height: '100%',
      iframe_onclose: () => { if (typeof vcita_fetch_staff == 'function') vcita_fetch_staff(true) }
    }, `<iframe src="${wpshd_vcita_redirect('/app/settings/calendar')}"></iframe>`);
  };

  this.openEditServicesWin = (event) => {
    event.preventDefault();
    event.stopPropagation();
    VcitaMixpman.track('wp_vpay_edit_services');
    _this.constructPopup({
      useWrapper: true,
      title: 'vCita services',
      width: '100%',
      height: '100%',
      iframe_onclose: () => { if (typeof vcita_fetch_staff == 'function') vcita_fetch_staff(true) }
    }, `<iframe src="${wpshd_vcita_redirect('/app/settings/services')}"></iframe>`);
  };

  this.openEditSettingsWin = (event) => {
    event.preventDefault();
    event.stopPropagation();
    VcitaMixpman.track('wp_vpay_setup');
    _this.constructPopup({
      useWrapper: true,
      title: 'vCita settings',
      width: '100%',
      height: '100%',
      iframe_onclose: () => { if (typeof vcita_fetch_staff == 'function') vcita_fetch_staff(true) }
    }, `<iframe src="${wpshd_vcita_redirect('/app/dashboard?wizard=os')}"></iframe>`);
  };

  this.openUpgradeWin = (event) => {
    event.preventDefault();
    event.stopPropagation();
    _this.constructPopup({
      useWrapper: true,
      title: 'vCita settings',
      width: '100%',
      height: '100%',
      iframe_onclose: () => { if (typeof vcita_sync_business == 'function') vcita_sync_business() }
    }, `<iframe src="${wpshd_vcita_redirect('/app/settings/upgrade_page')}"></iframe>`);
  };

  this.openSyncCalendarWin = () => {
    VcitaMixpman.track('wp_vpay_sync');
    _this.constructPopup({
      useWrapper: true,
      title: 'vCita calendar sync',
      width: '100%',
      height: '100%',
      iframe_onclose: () => {}
    }, `<iframe src="${wpshd_vcita_redirect('/app/settings/calendar')}"></iframe>`);
  };

  this.openSyncFacebookWin = () => {
    VcitaMixpman.track('wp_vpay_facebook');
    _this.constructPopup({
      useWrapper: true,
      title: 'vCita calendar sync',
      width: '100%',
      height: '100%',
      iframe_onclose: () => {}
    }, `<iframe src="${wpshd_vcita_redirect('/app/facebook-pages')}"></iframe>`);
  };

  this.openSyncGoogleWin = () => {
    VcitaMixpman.track('wp_vpay_google');
    _this.constructPopup({
      useWrapper: true,
      title: 'vCita calendar sync',
      width: '100%',
      height: '100%',
      iframe_onclose: () => {}
    }, `<iframe src="${wpshd_vcita_redirect('/app/reserve-with-google')}"></iframe>`);
  };

  /*
  * Method used to construct popup using HTML text or DOM elements
  *
  * opts object:
  * - useWrapper [boolean] - HTML is wrapped in a container
  * - additionalClass [string] - CSS classes added to popup container element
  * - width [string] - CSS style width that is set to popup wrapper
  * - height [string] - CSS style height that is set to popup wrapper
  * - css [string] - CSS style text that is set to popup wrapper
  * - title [string] - popup title
  * - onClose [function] - function invoked when popup is closed
  * - iframe_onclose - function invoked when iframe inside popup is closed
  * */

  this.constructPopup = (opts, html) => {
    if (typeof opts != 'object' || opts == null) opts = {};
    _this.hidePopup();
    const el = document.createElement('div');
    el.id = 'vcita_popup';
    el.className = 'flex vcentered hcentered';

    if (typeof opts.additionalClass == 'string' && opts.additionalClass.trim().length > 0) {
      el.classList.add(opts.additionalClass);
    } else if (typeof opts.additionalClass == 'object' && Array.isArray(opts.additionalClass)) {
      const ll = opts.additionalClass.length;

      for (let i = 0; i < l; i++) {
        if (typeof opts.additionalClass[i] !== 'string' || opts.additionalClass[i].trim().length === 0) continue;
        el.classList.add(opts.additionalClass[i]);
      }
    }

    if (typeof opts.title != 'string') opts.title = '';
    if (opts.useWrapper) {
      el.insertAdjacentHTML('beforeend', `<div><header class="flex vcentered">
        ${opts.title}<div class="vcita__btn__close" 
        onclick="event.preventDefault();event.stopPropagation();VcitaUI.hidePopup(true);"></div>
        </header><div class="vc_popup_wrapper"></div></div>`);
    } else el.insertAdjacentHTML('beforeend', '<div class="vc_popup_wrapper"></div>');

    document.body.appendChild(el);

    if (typeof opts.onClose == 'function') {
      el.addEventListener('popupClose', (eve) => { opts.onClose(eve) });
    }
    if (typeof opts.css == 'string') el.firstElementChild.style.cssText = opts.css;
    if (typeof opts.width == 'string') el.firstElementChild.style.width = opts.width;
    if (typeof opts.height == 'string') el.firstElementChild.style.height = opts.height;
    const c = opts.useWrapper ? el.lastElementChild.lastElementChild : el.lastElementChild;

    if (opts.useWrapper) {
      c.style.width = '100%';
      c.style.background = '#fff';
    }

    if (typeof html == 'string') {
      c.insertAdjacentHTML('beforeend', html);
    } else if (html instanceof HTMLElement) c.appendChild(html);

    let iframe = c.querySelector('iframe');

    if (iframe != null) {
      c.insertAdjacentHTML('beforeend', _this.createPreloaderHTML());
      iframe.addEventListener('load', () => {
        if (c.lastElementChild.classList.contains('vcita__preloader__container')) {
          c.removeChild(c.lastElementChild);
        }
        let timeout = setInterval(() => {
          if (c.querySelector('iframe') == null && typeof opts.iframe_onclose == 'function') _this.hidePopup();
        }, 10);
      });
    }

    if (typeof opts.iframe_onclose == 'function') {
      el.addEventListener('popupClose', (event) => { opts.iframe_onclose(event.detail) });
    }
  };

  this.createPreloaderHTML = () => {
    return `<mark class="vcita__preloader__container">
      <div class="lds-ring"><div></div><div></div><div></div><div></div></div></mark>`;
  };

  this.hidePopup = (ignore = false) => {
    const el = document.getElementById('vcita_popup');
    if (el != null) {
      document.body.removeChild(el);
      el.dispatchEvent(new CustomEvent('popupClose', {
        bubbles: true,
        detail: { ignore: ignore }
      }));
    }
  };
};

