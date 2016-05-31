/*jslint browser: true*/
/*global doc*/
/*global user*/
/*global Annotator*/
/*global alert*/

$(document).ready(function () {
  var annotator;
  var popup;

  function attach_handlers() {
    $('html').on('click.popup', function () {
      $('.popup').remove();

      $('html').off('click.popup');
    });
  }

  function ajaxify_form(inForm, callback) {
    var form = $(inForm);
    form.submit(function (e) {
      e.preventDefault();

      $.post(form.attr('action'), form.serialize(), function (response) {

        if (response.errors && Object.keys(response.errors).length) {
          var error_html = $('<ul></ul>');

          /*jslint unparam: true*/
          angular.forEach(response.errors, function (value, key) {
            error_html.append('<li>' + value + '</li>');
          });
          /*jslint unparam: false*/

          form.find('.errors').html(error_html);
        } else {
          callback(response);
        }
      });

    });
  }

  $('.affix-elm').each(function(i, elm) {
    elm = $(elm);
    var elmtop = 0;
    if(elm.data('offset-top')){
      elmtop = elm.data('offset-top');
    }
    var elmbottom = 0;
    if(elm.data('offset-bottom')){
      elmbottom = elm.data('offset-bottom');
    }

    elm.affix({
      offset: {
        top: elmtop,
        bottom: elmbottom
      }
    });
  });

  if (user.id === '') {

    Annotator.prototype.checkForEndSelection = function (event) {

      // This is what normally happens.
      var container, range, _k, _len2, _ref1;
      this.mouseIsDown = false;

      if (this.ignoreMouseup || $('.popup').length) {
        return;
      }
      this.selectedRanges = this.getSelectedRanges();
      _ref1 = this.selectedRanges;
      for (_k = 0, _len2 = _ref1.length; _k < _len2; _k++) {
        range = _ref1[_k];
        container = range.commonAncestor;
        if ($(container).hasClass("annotator-hl")) {
          container = $(container).parents("[class!=annotator-hl]")[0];
        }
        if (this.isAnnotator(container)) {
          return;
        }
      }
      if (event && this.selectedRanges.length) {
        // But we diverge from the norm here.

        if (event !== null) {
          event.preventDefault();
        }

        popup = $('<div class="popup unauthed-popup"><p>Login to comment.</p>' +
          '<input type="button" id="login" value="Login" class="btn btn-primary"/>' +
          '<input type="button" id="signup" value="Sign up" class="btn btn-primary" /></div>');

        popup.on('click.popup', function (event) {
          event.stopPropagation();
        });

        $('#login', popup).click(function (event) {
          event.stopPropagation();
          event.preventDefault();

          $.get('/api/user/login', {}, function (data) {
            data = $(data);

            ajaxify_form(data.find('form'), function () {
              $('html').trigger('click.popup');

              location.reload(false);
            });

            popup.html(data);
          });
        });

        $('#signup', popup).click(function (event) {
          event.stopPropagation();
          event.preventDefault();

          $.get('/api/user/signup', {}, function (data) {
            data = $(data);

            ajaxify_form(data.find('form'), function (result) {
              $('html').trigger('click.popup');
              alert(result.message);
            });

            popup.html(data);
          });
        });

        $('body').append(popup);

        this.ignoreMouseup = false;
        setTimeout(attach_handlers, 50);
      }

    };
  }

  annotator = $('#doc_content').annotator({
    //readOnly: user.id == ''
  });

  annotator.annotator('addPlugin', 'Unsupported');
  annotator.annotator('addPlugin', 'Tags');
  annotator.annotator('addPlugin', 'Markdown');
  annotator.annotator('addPlugin', 'Store', {
    annotationData: {
      'uri': window.location.pathname,
      'comments': []
    },
    prefix: '/api/docs/' + doc.id + '/comments',
    urls: {
      create: '',
      read: '/:id?is_ranged=true',
      update: '/:id',
      destroy: '/:id',
      search: '/search'
    }
  });

  annotator.annotator('addPlugin', 'Permissions', {
    user: user,
    permissions: {
      'read': [],
      'update': [user.id],
      'delete': [user.id],
      'admin': [user.id]
    },
    showViewPermissionsCheckbox: false,
    showEditPermissionsCheckbox: false,
    userId: function (user) {
      if (user && user.id) {
        return user.id;
      }

      return user;
    },
    userString: function (user) {
      if (user && user.name) {
        return user.name;
      }

      return user;
    }
  });

  annotator.annotator('addPlugin', 'Madison', {
    userId: user.id
  });
});
