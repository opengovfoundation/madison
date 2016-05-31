/*global Markdown*/
/*global window*/
angular.module('madisonApp.services')
  .service('annotationService', function ($rootScope, $sce, $location, AuthService, SessionService, loginPopupService) {

    var converter = new Markdown.Converter();
    this.annotations = [];
    this.annotationGroups = {};
    this.annotator = null;
    this.count = 0;

    this.resetAnnotationService = function () {
      this.annotations = [];
      this.annotationGroups = {};
      this.annotator = null;
      this.count = 0;
    };

    this.setAnnotations = function (annotations) {
      var parentElements = 'h1,h2,h3,h4,h5,h6,li,p';

      angular.forEach(annotations, function (annotation) {
        annotation.html = $sce.trustAsHtml(converter.makeHtml(annotation.text));
        this.annotations.push(annotation);

        // Get the first highlight's parent, and show our toolbar link for it next to it.
        var annotationParent = $(annotation.highlights[0]).parents(parentElements).first();
        var annotationParentId;
        if (annotationParent.prop('id')) {
          annotationParentId = annotationParent.prop('id');
        } else {
          this.count++;
          annotationParentId = 'annotationGroup-' + this.count;
          annotationParent.prop('id', annotationParentId);
        }


        if( (typeof(this.annotationGroups[annotationParentId])).toLowerCase() === 'undefined' ) {
          this.annotationGroups[annotationParentId] = {
            annotations: [],
            parent: annotationParent,
            parentId: annotationParentId,
            commentCount: 0
          };
        }

        this.annotationGroups[annotationParentId].annotations.push(annotation);
        this.annotationGroups[annotationParentId].commentCount += annotation.comments.length;
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
      this.resetAnnotationService();
    };

    this.createAnnotator = function (element, doc) {
      var user = SessionService.getUser();
      var path = $location.path();
      var origin = $location.host();
      var userId = user === null ? null : user.id;
      var readOnly;

      if (!AuthService.isAuthenticated()) readOnly = true;

      if (doc.discussion_state === 'closed') {
        readOnly = true;
        userId = null;
        user = null;
      }

      this.annotator = element.annotator({
        readOnly: readOnly,
        discussionClosed: doc.discussion_state === 'closed'
      });

      this.annotator.annotator('addPlugin', 'Unsupported');
      this.annotator.annotator('addPlugin', 'Tags');
      this.annotator.annotator('addPlugin', 'Markdown');

      this.annotator.annotator('addPlugin', 'Store', {
        annotationData: {
          'uri': path,
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
        annotationService: this,
        showLoginForm: function(event) { loginPopupService.showLoginForm(event); },
        path: path,
        origin: origin
      });
    };

    this.broadcastUpdate = function () {
      $rootScope.$broadcast('annotationsUpdated');
    };

    this.broadcastSet = function () {
      $rootScope.$broadcast('annotationsSet');
    };

    return this;
  });
