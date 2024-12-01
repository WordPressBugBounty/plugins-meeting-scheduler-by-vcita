const MixpMan = function (token, instance_id = '', email = '') {
  const _this = this;
  const DEBUG = window.WPSHD_VCITA_DEBUG;
  this.queue = [];
  this.instance_id = '';
  this.email = '';
  this.mixpanel_loaded = false;

  this.init = function (token, instance_id = '', email = '') {
    _this.instance_id = instance_id;
    _this.email = email;

    window.addEventListener('vcita_mixpanel_initialized', () => {
      if (DEBUG) console.log('mixpanel initialized');

      mixpanel.init(token, {
        loaded: function (mixpanel) {
          if (DEBUG) console.log('mixpanel loaded');
          _this.mixpanel_loaded = true;

          mixpanel.register({
            'plugin': 'Integrations::WordpressScheduling',
            'wp_instance_id': _this.instance_id
          });

          const l = _this.queue.length;

          for (let i = 0; i < l; i++) {
            _this.sendEvent(mixpanel, _this.queue[i].event, _this.queue[i].data)
          }
        }
      });
    });

    (function (c, a) {
      if (!a.__SV) {
        var b = window;

        try {
          var d, m, j, k = b.location, f = k.hash;
          d = function (a, b) { return (m = a.match(RegExp(b + "=([^&]*)"))) ? m[1] : null };
          f && d(f, "state") && (j = JSON.parse(decodeURIComponent(d(f, "state"))), "mpeditor" === j.action && (b.sessionStorage.setItem("_mpcehash", f), history.replaceState(j.desiredHash || "", c.title, k.pathname + k.search)));
        } catch (n) {}

        var l, h;
        window.mixpanel = a;
        a._i = [];

        a.init = function (b, d, g) {
          function c(b, i) {
            var a = i.split('.');
            2 == a.length && (b = b[a[0]], i = a[1]);
            b[i] = function () { b.push([i].concat(Array.prototype.slice.call(arguments, 0))) }
          }

          var e = a;
          'undefined' !== typeof g ? e = a[g] = [] : g = 'mixpanel';
          e.people = e.people || [];

          e.toString = function (b) {
            var a = "mixpanel";
            "mixpanel" !== g && (a += "." + g);
            b || (a += " (stub)");
            return a
          };

          e.people.toString = function () { return e.toString(1) + '.people (stub)' }
          l = 'disable time_event track track_pageview track_links track_forms track_with_groups add_group set_group remove_group register register_once alias unregister identify name_tag set_config reset opt_in_tracking opt_out_tracking has_opted_in_tracking has_opted_out_tracking clear_opt_in_out_tracking people.set people.set_once people.unset people.increment people.append people.union people.track_charge people.clear_charges people.delete_user people.remove'.split(' ');
          for (h = 0; h < l.length; h++) c(e, l[h]);
          var f = "set set_once union unset remove delete".split(" ");

          e.get_group = function () {
            function a(c) {
              b[c] = function () {
                var call2_args = arguments;
                var call2 = [c].concat(Array.prototype.slice.call(call2_args, 0));
                e.push([d, call2])
              }
            }

            for (var b = {}, d = ["get_group"].concat(Array.prototype.slice.call(arguments, 0)), c = 0; c < f.length; c++) a(f[c]);
            return b;
          };

          a._i.push([b, d, g]);
        };

        a.__SV = 1.2;
        b = c.createElement("script");
        b.type = "text/javascript";
        b.async = !0;
        b.src = "undefined" !== typeof MIXPANEL_CUSTOM_LIB_URL ? MIXPANEL_CUSTOM_LIB_URL : "file:" === c.location.protocol && "//cdn4.mxpnl.com/libs/mixpanel-2-latest.min.js".match(/^\/\//) ? "https://cdn4.mxpnl.com/libs/mixpanel-2-latest.min.js" : "//cdn4.mxpnl.com/libs/mixpanel-2-latest.min.js";
        d = c.getElementsByTagName("script")[0];

        b.addEventListener('load', () => {
          window.dispatchEvent(new CustomEvent('vcita_mixpanel_initialized', {
            bubbles: true,
            detail: {}
          }))
        });

        d.parentNode.insertBefore(b, d)
      }
    })(document, window.mixpanel || []);
    /*
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:1805524,hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    */
  };

  this.sendEvent = function (mixpanel, event, data) {
    try {
      if (DEBUG) console.log(`sending track data of event [${event}]`);
      const d = {}
      for (let i in data) d[i] = data[i];
      d.distinct_id = _this.instance_id;
      d.email = _this.email;
      mixpanel.track(event, d, function (e) {});

      if (typeof mixpanel.get_property == 'function') {
        d.plugin = mixpanel.get_property('plugin');
        d['wp_instance_id'] = mixpanel.get_property('wp_instance_id')
      }

      if (DEBUG) console.log(d);
    } catch (err) {}
  };

  this.track = function (event, data) {
    if (typeof event !== 'string') event = 'Event';
    if (typeof data !== 'object' || data == null) data = {};

    if (!_this.mixpanel_loaded) {
      this.queue.push({event: event, data: data})
    } else _this.sendEvent(mixpanel, event, data)
  };

  this.init(token, instance_id, email);
};
