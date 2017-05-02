$(function () {
  // For laracasts/flash
  $('#flash-overlay-modal').modal();
  $('div.alert').not('.alert-important').delay(3000).fadeOut(350);

  // Use hidden fields for boolean inputs so FormValidations work
  $('.checkbox label input[type="checkbox"]').change(function(e) {
    let $hiddenInput = $(e.target).siblings('label input[type="hidden"]');

    if ($(e.target).prop('checked')) {
      $hiddenInput.val('1');
    } else {
      $hiddenInput.val('0');
    }
  });

  $.fn.select2.defaults.set('theme', 'bootstrap');
  $('select:not(.no-select2)').select2({ width: '100%' });
});

window.loadTranslations = function (msgIds) {
  return $.get('/translations', { 'msg_id[]': msgIds }, null, "json")
    .done(function (data) {
      window.trans = data;
    });
};

window.getQueryParam = function(name, url) {
  if (!url) { url = window.location.href; }
  name = name.replace(/[\[\]]/g, "\\$&");

  var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
  results = regex.exec(url);

  if (!results) return null;
  if (!results[2]) return '';

  return decodeURIComponent(results[2].replace(/\+/g, " "));
};

window.redirectToLogin = function() {
  window.location.href = '/login?redirect='+window.location.pathname;
};

// http://stackoverflow.com/a/37192700/738052
window.autoHeightTextarea = function(textarea) {
  $(textarea)
    .each(function () { adjustHeight(this); })
    .on('input', function () { adjustHeight(this); });

  function adjustHeight(ctrl) {
    $(ctrl).css({'height':'auto','overflow-y':'hidden'}).height(ctrl.scrollHeight);
  }
};

// https://www.lullabot.com/articles/importing-css-breakpoints-into-javascript
window.screenSize = function () {
  return window
    .getComputedStyle(document.querySelector('body'), ':before')
    .getPropertyValue('content')
    .replace(/\"/g, '');
};
