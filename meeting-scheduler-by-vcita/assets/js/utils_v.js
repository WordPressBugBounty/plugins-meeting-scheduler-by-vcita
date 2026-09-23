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
    // The auth URL resolves asynchronously - on a fresh install getAuthURL()
    // first registers the site with the scheduler proxy over the network. Opening
    // the window after that await puts window.open() outside the user-gesture
    // window, so browsers block it and the click silently does nothing. Open the
    // window synchronously here, while the gesture is still live, and point it at
    // the URL once we have it.
    const win = window.open('', '_blank');

    VcitaApi.getAuthURL(reg, logout)
      .then((new_location) => {
        const onClose = () => {
          jQuery.ajax({
            'url': `${window.$_ajaxurl}?action=vcita_check_auth`,
            'method': 'POST',
            'success': (resp) => {
              try {
                // wp_send_json() replies with Content-Type: application/json, so
                // jQuery has already parsed this into an object. Calling
                // JSON.parse() on it threw, the catch below swallowed it, and the
                // reload never ran - so after connecting you had to refresh by
                // hand to see that it had worked. Accept either shape.
                const ud = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                if (typeof ud.uid == 'string' && ud.uid.length > 0) {
                  if (!reg) {
                    localStorage.removeItem('wpshd_is_trial');
                    document.cookie = "wpshd_is_trial= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
                    window.location.reload();
                    return false
                  } else {
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
        
        if (!win || win.closed) {
          // The popup was blocked outright. Navigate this tab instead so the
          // user can still connect, rather than the button doing nothing.
          window.location.href = new_location;
          return;
        }

        win.location = new_location;

        const isCloseInterval = setInterval(() => {
          // win was never null-checked here: when the popup was blocked this
          // threw "Cannot read properties of null" every 500ms, forever.
          if (!win || win.closed) {
            clearInterval(isCloseInterval);
            onClose();
          }
        },500)
      })
      .catch((err) => {
        // There was no catch here: if registering the site failed, the promise
        // rejected silently and the button looked dead.
        if (win && !win.closed) { win.close(); }
        console.error('vcita: could not start authentication', err);
        window.alert('vcita could not be reached. Please check your connection and try again.');
      })
  };

  this.openEditAvailabilityWin = (event) => {
    event.preventDefault();
    event.stopPropagation();
    window.open(wpshd_vcita_redirect('/app/settings/calendar_settings'), '_blank')
  };

  this.openEditServicesWin = (event) => {
    event.preventDefault();
    event.stopPropagation();
    window.open(wpshd_vcita_redirect('/app/settings/services'), '_blank')
  };

  this.openEditSettingsWin = (event) => {
    event.preventDefault();
    event.stopPropagation();
    window.open(wpshd_vcita_redirect('/app/dashboard?wizard=os'), '_blank')
  };

  this.openUpgradeWin = (event) => {
    event.preventDefault();
    event.stopPropagation();
    window.open(wpshd_vcita_redirect('/app/settings/upgrade_page'), '_blank')
  };

  this.openSyncCalendarWin = () => {
    window.open(wpshd_vcita_redirect('/app/settings/calendar_settings'), '_blank')
  };

  this.openSyncFacebookWin = () => {
    window.open(wpshd_vcita_redirect('/app/facebook-pages'), '_blank')
  };

  this.openSyncGoogleWin = () => {
    window.open(wpshd_vcita_redirect('/app/reserve-with-google'), '_blank');
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

