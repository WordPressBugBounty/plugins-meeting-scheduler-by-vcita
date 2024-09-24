function vcita_fetch_availability(add_preloader = false) {
  const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  let avc = document.getElementById('vcita__time__slots');
  if (avc == null) return;
  if (add_preloader) avc.innerHTML = VcitaUI.createPreloaderHTML();

  VcitaApi.getAvailability()
    .then((data) => {
      if (window.WPSHD_VCITA_DEBUG) console.log(data);
      if (avc != null) {
        const av = data.availabilities;
        if (av) {
          if (Object.keys(av).length === 0) {
            avc.innerHTML = '';
            return
          }
          avc.innerHTML = '<table></table>';
          avc = avc.lastElementChild;
          const formatter = new Intl.DateTimeFormat('en-us', {
            hour: 'numeric',
            minute: 'numeric',
            hour12: true,
          });
          for (let i in av) {
            const l = av[i].length;
            let day = new Date(i);
            day = days[day.getDay()];
            avc.insertAdjacentHTML('beforeend', '<tr><td>' + day + '</td><td></td></tr>');
            const dc = avc.lastElementChild.lastElementChild.lastElementChild;
            if (l > 0) {
              let ss = 0;
              for (let j = 0; j < l; j++) {
                const dur = av[i][j].duration_minutes * 60 * 1000;
                const ds = new Date(av[i][j].start_time);

                if (ss === 0) {
                  dc.insertAdjacentHTML('beforeend', formatter.format(ds));
                  ss = 1;
                }

                ds.setTime(ds.getTime() + dur);

                if (j + 1 < l) {
                  const dn = new Date(av[i][j + 1].start_time);

                  if (dn.getTime() === ds.getTime()) {
                    // continue
                  } else {
                    ss = 0;
                    dc.insertAdjacentHTML('beforeend', ' - ' + formatter.format(ds) + '&nbsp;&nbsp;');
                  }
                } else dc.insertAdjacentHTML('beforeend', ' - ' + formatter.format(ds));
              }
            }
          }
        } else avc.innerHTML = 'Error: missing data';
      }
    })
    .catch((error) => {
      console.log(error);
      avc.innerHTML = 'error';
    });
}

document.addEventListener('DOMContentLoaded', () => {
  vcita_fetch_staff();
  VcitaMixpman.track('wp_sched_change_tab', { tab: 'main' });
});

function vcita_save_start_wizard(_this) {
  _this.classList.remove('clickme');
  const request = new XMLHttpRequest();
  request.open('GET', `${window.$_ajaxurl}?action=vcita_save_data&data_name=start_wizard_clicked&data_val=1`, true);
  request.send();
}
