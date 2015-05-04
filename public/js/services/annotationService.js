/*global Markdown*/
/*global window*/
angular.module('madisonApp.services')
  .service('annotationService', function ($rootScope, $sce, $location, AuthService, SessionService) {

    var converter = new Markdown.Converter();
    this.annotations = [];
    this.annotator = null;

    this.setAnnotations = function (annotations) {

      angular.forEach(annotations, function (annotation) {
        annotation.html = $sce.trustAsHtml(converter.makeHtml(annotation.text));
        this.annotations.push(annotation);
      }, this);

      this.broadcastUpdate();
    };

    this.addAnnotation = function (annotation) {
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

    this.destroyAnnotator = function () {
      //If we have an instance of annotator, delete it
      if (this.annotator !== null) {
        this.annotator = null;
        delete this.annotator;
      }

      //Reset our annotation store
      this.annotations = [];
    };

    this.createAnnotator = function (element, doc) {
      var user = SessionService.getUser();
      var path = $location.path();
      var userId = user === null ? null : user.id;

      this.annotator = element.annotator({
        readOnly: AuthService.isAuthenticated()
      });

      this.annotator.annotator('addPlugin', 'Unsupported');
      this.annotator.annotator('addPlugin', 'Tags');
      this.annotator.annotator('addPlugin', 'Markdown');

      this.annotator.annotator('addPlugin', 'Store', {
        annotationData: {
          'uri': path,
          'comments': []
        },
        prefix: '/api/docs/' + doc.id + '/annotations',
        urls: {
          create: '',
          read: '/:id',
          update: '/:id',
          destroy: '/:id',
          search: '/search'
        }
      });

      this.annotator.annotator('addPlugin', 'Permissions', {
        user: user,
        permissions: {
          'read': [],
          'update': [userId],
          'delete': [userId],
          'admin': [userId]
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

      this.annotator.annotator('addPlugin', 'Madison', {
        user: user,
        doc: doc,
        annotationService: this
      });
    };

    this.broadcastUpdate = function () {
      $rootScope.$broadcast('annotationsUpdated');
    };

    return this;
  });
