const ApiController = function (wpid, callbackURL) {
  const DEBUG = window.WPSHD_VCITA_DEBUG;
  this.WPSHD_VCITA_URL = `https://app.${window.WPSHD_VCITA_SERVER_BASE}/app`;
  this.AJAX_URL = window.$_ajaxurl;
  this.API_URL = window.WPSHD_VCITA_USE_MEET2KNOW ? 'https://us-central1-scheduler-272415.cloudfunctions.net/scheduler-proxy-meet2know' : 'https://us-central1-scheduler-272415.cloudfunctions.net/scheduler-proxy';
  this.clientId = window.WPSHD_VCITA_USE_MEET2KNOW ? 'a27f3c7639bf18840038cce2acb231111e69b9b4167c694c5e62224294abb1ff' : 'cc46751cc204610d8e62157d61c1a50fe59299a2384bde258cf85e60b1f55ea8';
  this.callbackURL = callbackURL;
  this.wpid = wpid;
  const _this = this;
  this.business_data = null;

  if (typeof this.callbackURL !== 'string' || this.callbackURL.length === 0) {
    throw new Error('parameter callbackURL is invalid')
  }

  this.checkIfInstalled = function () {
    return new Promise((resolve, reject) => {
      if (!_this.isInstalled()) {
        _this.install()
          .then((data) => {
            if (data.success && data.wp_id) {
              _this.wpid = data.wp_id;
              _this.saveData('wp_id', data.wp_id).then((res) => {});
              resolve()
            } else reject(new Error('app not installed'))
          })
      } else resolve();
    })
  };

  this.getAvailability = function () {
    return _this.checkIfInstalled()
      .then(() => { return _this.sendGet(`${this.API_URL}/availability/${wpid}`) })
      .catch((err) => { throw err })
  };

  this.getBusiness = function () {
    return _this.checkIfInstalled()
      .then(() => { return _this.sendGet(`${this.API_URL}/business/${wpid}`) })
      .catch((err) => { throw err })
  };

  this.getAuthURL = function (reg = false, logout = false) {
    return _this.checkIfInstalled()
      .then(() => {
        const email = jQuery('#vcita__input-email').val();
        let url = `/oauth/authorize?response_type=code&client_id=${this.clientId}&lang=${window.WPSHD_VCITA_LOCALE}`;
        if (reg) url += '&registration=true&intent=["add_online_scheduling"]';
        if (logout) url += '&logout=true';
        url += `&redirect_uri=${encodeURIComponent(`${_this.API_URL}/callback`)}&state=${_this.wpid}&invite=WP-V-SCHD`;
        return `${this.WPSHD_VCITA_URL}${url}${(typeof email == 'string' && email.length > 0 ? `&email=${email}` : '')}`
      })
      .catch((err) => { throw err })
  };

  this.getServices = function () {
    return _this.checkIfInstalled()
      .then(() => { return _this.sendGet(`${this.API_URL}/services/${wpid}`) })
      .catch((err) => { throw err })
  };

  this.getStaff = function () {
    return _this.checkIfInstalled()
      .then(() => { return _this.sendGet(`${this.API_URL}/staff/${wpid}`) })
      .catch((err) => { throw err })
  };

  this.isInstalled = function () { return typeof _this.wpid == 'string' && _this.wpid.length > 0 };
  this.install = function () { return _this.sendGet(`${this.API_URL}/install?callback=${this.callbackURL}`) };

  this.saveData = function (key, data) {
    return _this.sendPost(this.AJAX_URL, {}, {
      action: 'vcita_save_data',
      data_name: key,
      data_val: data
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
