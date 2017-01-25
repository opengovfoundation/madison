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
}
