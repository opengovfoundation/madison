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
