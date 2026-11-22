/*
 * The "Enjoying vcita?" notice. Every answer hides it at once. Only "close" is not saved.
 * Settings come from wp_localize_script() as window.wpshdVcitaRating.
 */
(function () {
  'use strict';

  var config = window.wpshdVcitaRating;
  if (!config) return;

  document.querySelectorAll('[data-wpshd-vcita-rating]').forEach(function (notice) {
    notice.addEventListener('click', function (event) {
      var control = event.target.closest('[data-wpshd-vcita-rating-op]');
      if (!control) return;
      var op = control.getAttribute('data-wpshd-vcita-rating-op');
      // "Rate 5 Stars" is a link: let it open the review page in a new tab.
      if (op !== 'rate') event.preventDefault();
      // On the plugin's pages the card sits in its own wrapper. Remove both, so no gap stays.
      (notice.closest('.wpshd-vcita-rating-wrap') || notice).remove();
      if (op === 'close') return;
      fetch(config.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: new URLSearchParams({ action: 'wpshd_vcita_rating', nonce: config.nonce, op: op })
      }).catch(function () {});
    });
  });
})();
