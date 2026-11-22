

// The admin pages' access to vcita. Every request goes to this site's admin-ajax, which calls
// scheduler-proxy from the server (see vcita-proxy.php). The browser never calls the proxy.
const ApiController = function (wpid) {
  const DEBUG = window.WPSHD_VCITA_DEBUG;
  this.AJAX_URL = window.$_ajaxurl;
  this.wpid = wpid;
  const _this = this;
  this.business_data = null;

  this.proxy = function (resource) {
    return _this.sendPost(_this.AJAX_URL, {}, {
      action: 'wpshd_vcita_proxy',
      resource: resource,
      nonce: vcitaSchedulerData.nonce
    });
  };

  this.getAvailability = function () { return _this.proxy('availability') };
  this.getBusiness = function () { return _this.proxy('business') };
  this.getServices = function () { return _this.proxy('services') };
  this.getStaff = function () { return _this.proxy('staff') };

  // The server registers the site with the proxy when it has no wp_id yet.
  this.isInstalled = function () { return typeof _this.wpid == 'string' && _this.wpid.length > 0 };
  this.install = function () {
    return _this.proxy('install').then((data) => {
      if (data && data.success && data.wp_id) _this.wpid = data.wp_id;
      return data;
    });
  };

  // Resolves to the vcita sign-in URL. The server also records that this administrator
  // started a Connect, which the sign-in result is checked against.
  this.getAuthURL = function (reg = false, logout = false) {
    const email = jQuery('#vcita__input-email').val();
    return _this.sendPost(_this.AJAX_URL, {}, {
      action: 'wpshd_vcita_connect_url',
      signup: reg ? '1' : '0',
      logout: logout ? '1' : '0',
      email: typeof email == 'string' ? email : '',
      nonce: vcitaSchedulerData.nonce
    }).then((data) => {
      if (!data || typeof data.url !== 'string') throw new Error((data && data.message) || 'no sign-in URL');
      return data.url;
    });
  };

  // vcitaSchedulerData
  this.saveData = function (key, data) {
    return _this.sendPost(this.AJAX_URL, {}, {
      action: 'vcita_save_data',
      data_name: key,
      data_val: data,
      nonce: vcitaSchedulerData.nonce
    });
  };

  this.sendGet = function (url, opts = {}) {
    return new Promise(function (resolve, reject) {
      const request = _this.createRequest('GET', url, opts);
      _this.bindRequestEvents(request, resolve, reject);
      request.send();
    });
  };

  this.sendPost = function (url, opts = {}, data) {
    return new Promise(function (resolve, reject) {
      const request = _this.createRequest('POST', url, opts);
      const fData = new FormData();
      if (typeof data == 'object' && data != null) {
        for (let i in data) {
          const val = typeof data[i] == 'object' ? JSON.stringify(data[i]) : data[i];
          fData.set(i, val);
        }
      }
      _this.bindRequestEvents(request, resolve, reject);
      request.send(fData);
    });
  };

  this.createRequest = function (method, url, opts) {
    let xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    if (opts.withCredentials) xhr.withCredentials = true;
    return xhr;
  };

  this.bindRequestEvents = function (xhr, resolve, reject) {
    xhr.onerror = function (e) { reject(e.target) };
    xhr.onload = function (e) {
      if (DEBUG) {
        console.log('----------------------- RESPONSE -----------------------');
        console.log(`Status: ${e.target.status}`);
        console.log(e.target.responseText);
        console.log('--------------------------------------------------------');
      }
      try {
        resolve(JSON.parse(e.target.responseText));
      } catch (err) { reject(e.target.responseText) }
    };
  };
}
