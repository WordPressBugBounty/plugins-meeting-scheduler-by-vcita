const VcitaPreviewObj = function () {
  this.unsaved = false;
  const _this = this;
  this.elem_array = {};
  this.param_array = {
    widget_title: 'changeWidgetTitle',
    widget_text: 'changeText',
    btn_text: 'changeButtonText',
    btn_color: 'changeButtonColor',
    txt_color: 'changeButtonTextColor',
    hover_color: 'changeButtonHoverColor',
    widget_img: 'changeImage'
  };

  this.orig_val_array = {};

  this.changeText = (txt) => {
    if (txt.length === 0) txt = 'Thanks for stopping by! We’re here to help…';
    _this.elem_array.txt_container.textContent = txt;
  };

  this.changeImage = (src) => {
    if (!src) {
      if (_this.elem_array.img_container.firstElementChild != null &&
        _this.elem_array.img_container.firstElementChild.tagName === 'IMG') {
        _this.elem_array.img_container.firstElementChild.remove();
      }
      return;
    }
    if (_this.elem_array.img_container.firstElementChild.tagName !== 'IMG') {
      _this.elem_array.img_container.insertAdjacentHTML('afterbegin', '<img />')
    }
    _this.elem_array.img_container.firstElementChild.src = src;
  };

  this.changeButtonColor = (color) => {
    _this.elem_array.button.style.setProperty('--vcita-schedule-button-button-color', color);
  };

  this.changeWidgetTitle = (txt) => {
    if (txt.length === 0) txt = 'Let\'s talk';
    _this.elem_array.title_container.textContent = txt;
  };

  this.changeButtonText = (txt) => {
    if (txt.length === 0) txt = 'Schedule Now';
    _this.elem_array.button.textContent = txt;
  };

  this.changeButtonTextColor = (color) => {
    _this.elem_array.button.style.setProperty('--vcita-schedule-button-text-color', color);
  };

  this.changeButtonHoverColor = (color) => {
    _this.elem_array.button.style.setProperty('--vcita-schedule-button-hover-color', color);
  };

  this.init = () => {
    _this.elem_array.container = document.querySelector('.vcita__widget__preview__container-inner');
    const c = _this.elem_array.container;
    if (c == null) return;
    _this.elem_array.header = c.querySelector('.vcita__widget__preview__container-inner-header');
    _this.elem_array.img_container = _this.elem_array.header;
    _this.elem_array.title_container = _this.elem_array.header.lastElementChild;
    _this.elem_array.txt_container = c.querySelector('.vcita__widget__preview__container-inner-text');
    _this.elem_array.button = c.querySelector('.vcita_schedule_button');
    for (let i in _this.param_array) {
      _this.orig_val_array[i] = document.querySelector('[name="' + i + '"]').value;
    }
  };
};

function vcita_widget_popup_toggle(show) {
  if (show) {
    jQuery('.vcita_section_toggle-view, ' +
      '.vcita__widget__preview__container-inner-header, ' +
      '.vcita__widget__preview__container-inner-text,' +
      '.vcita__widget__preview__container-inner-powered')
      .removeClass('hidden_from_view');
    jQuery('.vcita_schedule_button').removeClass('big');
    jQuery('.vcita__widget__preview__container-inner').removeClass('no-background');
  } else {
    jQuery('.vcita_section_toggle-view, ' +
      '.vcita__widget__preview__container-inner-header, ' +
      '.vcita__widget__preview__container-inner-text,' +
      '.vcita__widget__preview__container-inner-powered')
      .addClass('hidden_from_view');
    jQuery('.vcita_schedule_button').addClass('big');
    jQuery('.vcita__widget__preview__container-inner').addClass('no-background');
  }
  VcitaMixpman.track('wp_sched_popup_widget', {action: (show ? 'on' : 'off')});
}

function vcita_design_change(e) {
  if (e instanceof HTMLElement) {
    const name = e.getAttribute('name');
    if (name == null) return;
    if (typeof VcitaPreview[VcitaPreview.param_array[name]] != 'function') return;
    VcitaPreview[VcitaPreview.param_array[name]](e.value);
    if (e.value !== VcitaPreview.orig_val_array[name]) VcitaPreview.unsaved = true;
  } else if (typeof e == 'object') {
    const name = e.name;
    if (name == null) return;
    if (typeof VcitaPreview[VcitaPreview.param_array[name]] != 'function') return;
    VcitaPreview[VcitaPreview.param_array[name]](e.value);
    if (e.value !== VcitaPreview.orig_val_array[name]) VcitaPreview.unsaved = true;
  }
}

function vcita_reset_design(el) {
  const d = {
    widget_img: '',
    widget_title: '',
    show_on_site: 1,
    widget_show: 0,
    btn_text: '',
    btn_color: '#01dcf7',
    txt_color: '#ffffff',
    hover_color: '#01dcf7',
    widget_text: '',
    widget_img_clear: 1
  };
  for (let i in d) {
    if (typeof VcitaPreview[VcitaPreview.param_array[i]] == 'function') VcitaPreview[VcitaPreview.param_array[i]](d[i]);
    const el = document.querySelector('[name="' + i + '"]');
    if (el != null) {
      if (el.value !== VcitaPreview.orig_val_array[i]) VcitaPreview.unsaved = true;
      el.value = d[i];
      if (i.indexOf('_color') >= 0) {
        el.parentNode.style.setProperty('--selected-color', d[i]);
        el.parentNode.setAttribute('selected-color', d[i]);
      } else if (i === 'widget_img') {
        const ic = document.querySelector('.vcita_selected_image_wrapper');
        if (ic != null) ic.innerHTML = '';
        VcitaPreview.changeImage(false);
      } else if (el.getAttribute('type') === 'checkbox') {
        el.checked = d[i] == 1;
        if (i === 'widget_show') vcita_widget_popup_toggle(d[i] === 1);
      }
    }
  }
  const c = document.querySelector('.vcita_image_attach_container');
  const inp = c.querySelector('[type=file]');
  inp.value = "";
  inp.classList.remove('to_be_cleared');
  vcita_save_design(el, null, d);
  VcitaMixpman.track('wp_sched_reset')
}

function vcita_save_design(target, form, data) {
  VcitaPreview.unsaved = false;
  if (target != null && target.getAttribute('type') !== 'checkbox' && target.tagName !== 'SELECT') {
    target.setAttribute('disabled', 'true');
    if (target.tagName === 'BUTTON') target.textContent = 'Saving...';
    const tbr = document.querySelector('.to_be_cleared');
    if (tbr != null) tbr.classList.remove('to_be_cleared')
  }
  const fData = new FormData(form != null ? form : undefined);
  fData.append('action', 'vcita_save_settings');
  if (form != null && form instanceof HTMLElement) {
    const chbxs = form.querySelectorAll('[type=checkbox]');
    for (let i = 0; i < chbxs.length; i++) fData.set(chbxs[i].getAttribute('name'), chbxs[i].checked ? 1 : 0);
    const fi = form.querySelector('input[type="file"]');
    if (fi != null) fi.value = '';
  }
  const request = new XMLHttpRequest();
  request.open('POST', `${window.$_ajaxurl}`, true);
  if (typeof data == 'object' && data != null) for (let i in data) fData.append(i, data[i]);
  const onResponse = function (target, value) {
    if (target != null && target.tagName === 'BUTTON') target.textContent = value;
    window.setTimeout(function () {
      if (target != null && target.tagName === 'BUTTON') target.textContent = 'Save';
      target.removeAttribute('disabled');
    }, 4000);
  };
  request.onerror = function (e) { onResponse(target, 'Error occured') };
  request.onload = function (e) {
    const response = e.target;
    if (response.status === 200) {
      try {
        const data = JSON.parse(response.responseText);
        if (data['success']) {
          onResponse(target, 'Saved!');
          if (data['widget_img']) {
            let img = document.querySelector('.vcita_selected_image_wrapper img');
            if (img != null) {
              img.parentNode.classList.remove('hide_onchange');
              img.outerHTML = data['widget_img'];
              img = document.querySelector('.vcita_selected_image_wrapper img');
              if (img.nextElementSibling == null || img.nextElementSibling.tagName !== 'SPAN') {
                img.insertAdjacentHTML('afterend', `<span class="delete_button vcita__btn__close" onclick="vcita_del_attachment(this)">
                  <input type="checkbox" class="onchange_hidden" id="widget_img_clear" name="widget_img_clear" onchange="this.value=this.checked?1:0">
                  <label for="widget_img_clear" class="onchange_hidden"></label></span>`);
              }
            }
          }
        } else onResponse(target, 'Error occured');
      } catch (err) {
        console.log(err);
        onResponse(target, 'Error occured');
      }
    } else onResponse(target, 'Error occured');
  };
  request.send(fData);
}

function vcita_del_attachment(_this) {
  _this.style.display = 'none';
  if (_this.previousElementSibling != null) _this.previousElementSibling.remove();
  if (_this.firstElementChild != null && _this.firstElementChild.tagName === 'INPUT') {
    _this.firstElementChild.value = 1;
    _this.firstElementChild.checked = true;
  }
  vcita_design_change({name: 'widget_img', value: ''})
}

function vcita_clear_fileinput(ev, el) {
  ev.preventDefault();
  ev.stopPropagation();
  const inp = el.parentNode.querySelector('[type=file]');
  inp.value = "";
  inp.classList.remove('to_be_cleared');
  const c = document.querySelector('.vcita_selected_image_wrapper');
  c.classList.remove('hide_onchange');
  const img = c.querySelector('.new_attach');
  if (img != null) c.removeChild(img);
  const enf = el.nextElementSibling.firstElementChild;
  if (enf != null && enf.tagName === 'IMG') {
    vcita_design_change({name: 'widget_img', value: enf.src})
  } else vcita_design_change({name: 'widget_img', value: ''})
}

function vcita_onFileInputChange(inp) {
  if (inp.files.length === 0) return;
  inp.classList.add('to_be_cleared');
  const c = document.querySelector('.vcita_selected_image_wrapper');
  c.classList.add('hide_onchange');
  const img = c.querySelector('.new_attach');
  if (img != null) c.removeChild(img);
  const fr = new FileReader();
  fr.onload = function () {
    c.classList.add('hide_onchange');
    c.insertAdjacentHTML('beforeend', `<img src="${this.result}" class="vcita_thumb new_attach alt="vcita_thumb"/>`);
    VcitaPreview.changeImage(this.result);
  };
  fr.readAsDataURL(inp.files[0]);
}

jQuery(function ($) {
  const pickers = document.querySelectorAll('.color_input');
  const lp = pickers.length;
  for (let i = 0; i < lp; i++) {
    const pick = pickers[i];
    $(pick).ColorPicker({
      color: pick.getAttribute('selected-color'),
      onChange: function (hsb, hex, rgb, el) {
        el.setAttribute('selected-color', `#${hex}`);
        el.style.setProperty('--selected-color', `#${hex}`);
        el.firstElementChild.value = `#${hex}`;
        vcita_design_change(el.firstElementChild);
      }
    });
  }
});

function vcita_create_selects(val) {
  const clf = function (elmnt) {
    var x, y, i, arrNo = [];
    x = document.getElementsByClassName('vcita__input__select-items');
    y = document.getElementsByClassName('vcita__input__select-selected');
    for (i = 0; i < y.length; i++) {
      if (elmnt === y[i]) {
        arrNo.push(i);
      } else y[i].classList.remove('select-arrow-active')
    }
    for (i = 0; i < x.length; i++) if (arrNo.indexOf(i)) x[i].classList.add('vcita__input__select-hide');
  };
  var x, i, j, selElmnt, a, b, c;
  x = document.querySelectorAll('.vcita__input__select');
  const xl = x.length;
  for (i = 0; i < xl; i++) {
    selElmnt = x[i].querySelector('select');
    if (selElmnt == null) continue;
    a = selElmnt.nextElementSibling;
    if (a == null) continue;
    b = document.createElement('div');
    b.setAttribute('class', 'vcita__input__select-items vcita__input__select-hide');
    for (j = 0; j < selElmnt.length; j++) {
      if (selElmnt.selectedIndex === j) a.innerHTML = selElmnt.options[j].innerHTML;
      if (typeof val == 'object' && val[selElmnt.id] === selElmnt.options[j].value) {
        a.classList.remove('with-placeholder');
      }
      c = document.createElement('div');
      if (selElmnt.options[j].getAttribute('data-style') != null) {
        c.setAttribute('style', selElmnt.options[j].getAttribute('data-style'));
      }
      c.setAttribute('data-value', selElmnt.options[j].value);
      c.innerHTML = selElmnt.options[j].innerHTML;
      c.addEventListener('click', function (e) {
        var y, i, k, s, h;
        s = this.parentNode.parentNode.firstElementChild;
        h = this.parentNode.previousElementSibling;
        for (i = 0; i < s.length; i++) {
          if (s.options[i].innerHTML === this.innerHTML && s.options[i].innerHTML !== h.innerHTML) {
            const os = s.querySelector('option[selected]');
            if (os != null) os.removeAttribute('selected');
            s.selectedIndex = i;
            y = this.parentNode.getElementsByClassName('same-as-selected');
            for (k = 0; k < y.length; k++) y[k].removeAttribute('class');
            this.className = 'same-as-selected';
            if ('createEvent' in document) {
              var evt = document.createEvent('HTMLEvents');
              evt.initEvent('change', false, true);
              s.dispatchEvent(evt);
            } else s.fireEvent('onchange');
            break
          }
        }
        h.textContent = this.textContent;
        h.click();
      });
      b.appendChild(c);
    }
    x[i].appendChild(b);
    a.addEventListener('click', function (e) {
      e.stopPropagation();
      const all = document.querySelectorAll('.input-error');
      for (let i = 0; i < all.length; i++) all[i].classList.remove('input-error');
      if (this.previousElementSibling.disabled) {
        this.previousElementSibling.classList.add('input-error');
        return;
      }
      clf(this);
      this.nextElementSibling.classList.toggle('vcita__input__select-hide');
      this.classList.toggle('select-arrow-active');
    });
  }
  document.addEventListener('click', clf);
}

function vcita_show_edit_livesite(ev) {
  ev.preventDefault();
  ev.stopPropagation();
  VcitaUI.constructPopup(
    { useWrapper: true, title: 'vCita Online presence', width: '100%', height: '100%' },
    `<iframe src="https://app.${window.WPSHD_VCITA_SERVER_BASE}/app/my-livesite?section=website-widgets"></iframe>`)
}

function vcita_switch_contact_page(event, _this) {
  vcita_save_design(_this, null, {'contact_page_active': _this.checked ? 1 : 0});
  const pr = _this.closest('.vcita__toggle__checkbox-container').nextElementSibling;
  if (pr != null) pr.classList.toggle('hidden');
  const small = _this.closest('h3').querySelectorAll('small');
  const l = small.length;
  for (let i = 0; i < l; i++) small[i].classList.toggle('hidden')
}

function vcita_switch_calendar_page(event, _this) {
  vcita_save_design(_this, null, {'calendar_page_active': _this.checked ? 1 : 0});
  const pr = _this.closest('.vcita__toggle__checkbox-container').nextElementSibling;
  if (pr != null) pr.classList.toggle('hidden');
  const small = _this.closest('h3').querySelectorAll('small');
  const l = small.length;
  for (let i = 0; i < l; i++) small[i].classList.toggle('hidden')
}

function vcita_widget_show_toggle(_this, show) {
  const p = _this.closest('.vcita__page__inner__section');
  p.classList.toggle('no-margin');
  let n = p.nextElementSibling;
  while (n != null && n.classList.contains('vcita__page__inner__section')) {
    if (n.classList.contains('no-hide')) {
      n = n.nextElementSibling;
      continue
    }
    n.classList.toggle('hidden');
    n = n.nextElementSibling
  }
  const h3 = _this.closest('h3');
  const sm = h3.querySelectorAll('small');
  for (let i = 0; i < sm.length; i++) sm[i].classList.toggle('hidden');
  if (p.nextElementSibling == null) p.classList.remove('no-margin');
  VcitaMixpman.track('wp_sched_switch', {action: (show ? 'on' : 'off')});
  vcita_save_design(_this, null, {'show_on_site': show ? 1 : 0})
}

function vcita_toggle_design_view(el, v) {
  let n = el.closest('section').nextElementSibling;
  while (n != null && n.tagName === 'SECTION') {
    if (v === 1) {
      n.classList.add('hidden')
    } else n.classList.remove('hidden');
    n = n.nextElementSibling;
  }
  document.querySelector('.vcita__page__inner__section-banner-img').classList.toggle('hidden');
  document.querySelector('.vcita__page__inner__section-banner').classList.toggle('content-stretched');
  vcita_save_design(el, null, {'vcita_design': v})
}

document.addEventListener('DOMContentLoaded', () => {
  VcitaMixpman.track('wp_sched_change_tab', {tab: 'add'});
  VcitaPreview = new VcitaPreviewObj();
  VcitaPreview.init();
  vcita_create_selects();
  jQuery('.vcita_info').tooltip();
});

window.addEventListener('beforeunload', function (ev) {
  if (VcitaPreview.unsaved) {
    ev.preventDefault();
    ev.stopPropagation();
    ev.returnValue = 'Leave site\nChanges you made may not be saved';
  }
});