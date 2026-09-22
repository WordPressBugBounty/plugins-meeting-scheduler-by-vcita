function vcita_get_sample_html(_this) {
  const el = _this.parentNode.nextElementSibling;
  if (!el.showed) {
    jQuery(el).slideDown();
    el.showed = true;
  } else {
    jQuery(el).slideUp();
    el.showed = false;
  }
}
document.addEventListener('DOMContentLoaded', () => {
  jQuery('.language-markup').mouseenter(() => { })
})