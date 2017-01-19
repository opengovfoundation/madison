$(function () {
  // For laracasts/flash
  $('#flash-overlay-modal').modal();
  $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
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
