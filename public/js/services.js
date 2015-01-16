/*jslint browser: true*/
/*global alert*/
/*global Markdown*/
angular.module('madisonApp.services', [])
  .factory('UserService', ['$rootScope', '$http',
    function ($rootScope, $http) {
      var UserService = {};
      UserService.user = {};

      UserService.getUser = function () {
        UserService.exists = $http.get('/api/user/current')
          .success(function (data) {
            UserService.user = data.user;
            $rootScope.$broadcast('userUpdated');
          });
      };

      return UserService;
    }])
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
        var popup = $('<div class="popup unauthed-popup"><p>Por favor regístrate.</p>' +
          '<input type="button" id="login" value="Ingresar" class="btn btn-primary"/>' +
          '<input type="button" id="signup" value="Registrarse" class="btn btn-primary" /></div>');


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
  })
  //Built using the tutorial at http://weblogs.asp.net/dwahlin/building-an-angularjs-modal-service
  .service('modalService', ['$modal',
    function ($modal) {

      //Set modal defaults
      var modalDefaults = {
        backdrop: true,
        keyboard: true,
        modalFade: true,
        templateUrl: '/templates/modal.html'
      };

      var modalOptions = {
        closeButtonText: 'Close',
        actionButtonText: false,
        headerText: 'Notice',
        bodyText: 'Hmm... someone forgot the content here...'
      };

      this.showModal = function (customModalDefaults, customModalOptions) {

        if (!customModalDefaults) {
          customModalDefaults = {};
        }

        //Accepts either true or 'static'.  'static' doesn't close the modal on click.
        customModalDefaults.backdrop = true;

        return this.show(customModalDefaults, customModalOptions);
      };

      this.show = function (customModalDefaults, customModalOptions) {
        //Create temp objects to work with since we're in a singleton service
        var tempModalDefaults = {};
        var tempModalOptions = {};

        //Map angular-ui modal custom defaults to modal defaults defined in service
        angular.extend(tempModalDefaults, modalDefaults, customModalDefaults);

        //Map modal.html $scope custom properties to defaults defined in service
        angular.extend(tempModalOptions, modalOptions, customModalOptions);

        if (!tempModalDefaults.controller) {
          tempModalDefaults.controller = function ($scope, $modalInstance) {
            $scope.modalOptions = tempModalOptions;

            $scope.modalOptions.ok = function (result) {
              $modalInstance.close(result);
            };

            $scope.modalOptions.close = function (result) {
              $modalInstance.dismiss('cancel');
            };
          };
        }

        return $modal.open(tempModalDefaults).result;
      };
    }]);




