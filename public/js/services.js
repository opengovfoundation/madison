/*jslint browser: true*/
/*global alert*/
/*global Markdown*/
angular.module('madisonApp.services', [])
  .factory('createLoginPopup', ['$document', '$timeout',
    function ($document, $timeout) {
      var body = $document.find('body');
      var html = $document.find('html');

      var attach_handlers = function () {
        html.on('click.popup', function () {
          $('.popup').remove();

          html.off('click.popup');
        });
      };

      var ajaxify_form = function (inForm, callback) {
        var form = $(inForm);
        form.submit(function (e) {
          e.preventDefault();

          $.post(form.attr('action'), form.serialize(), function (response) {

            if (response.errors && Object.keys(response.errors).length) {
              var error_html = $('<ul></ul>');

              /*jslint unparam:true*/
              $(response.errors).each(function (i, key) {
                error_html.append('<li>' + response.errors[key][0] + '</li>');
              });
              /*jslint unparam:false*/

              form.find('.errors').html(error_html);
            } else {
              callback(response);
            }
          });

        });
      };

      return function LoginPopup(event) {
        console.log(event);
        var popup = $('<div class="popup unauthed-popup"><p>Please log in.</p>' +
          '<input type="button" id="login" value="Login" class="btn btn-primary"/>' +
          '<input type="button" id="signup" value="Sign up" class="btn btn-primary" /></div>');


        popup.on('click.popup', function (event) {
          event.stopPropagation();
        });

        $('#login', popup).click(function (event) {
          event.stopPropagation();
          event.preventDefault();

          $.get('/api/user/login/', {}, function (data) {
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

          $.get('/api/user/signup/', {}, function (data) {
            data = $(data);

            ajaxify_form(data.find('form'), function (result) {
              $('html').trigger('click.popup');
              alert(result.message);
            });

            popup.html(data);
          });
        });

        body.append(popup);

        var position = {
          'top': event.clientY - popup.height(),
          'left': event.clientX
        };
        popup.css(position).css('position', 'absolute');
        popup.css('z-index', '999');

        $timeout(function () {
          attach_handlers();
        }, 50);
      };
    }
    ])
  .factory('annotationService', function ($rootScope, $sce) {
    var annotationService = {};
    var converter = new Markdown.Converter();
    annotationService.annotations = [];

    annotationService.setAnnotations = function (annotations) {

      angular.forEach(annotations, function (annotation) {
        annotation.html = $sce.trustAsHtml(converter.makeHtml(annotation.text));
        this.annotations.push(annotation);
      }, this);

      this.broadcastUpdate();
    };

    annotationService.addAnnotation = function (annotation) {
      if (annotation.id === undefined) {
        var interval = window.setInterval(function () {
          this.addAnnotation(annotation);
          window.clearInterval(interval);
        }.bind(this), 500);
      } else {
        annotation.html = $sce.trustAsHtml(converter.makeHtml(annotation.text));
        this.annotations.push(annotation);
        this.broadcastUpdate();
      }
    };

    annotationService.broadcastUpdate = function () {
      $rootScope.$broadcast('annotationsUpdated');
    };

    return annotationService;
  });