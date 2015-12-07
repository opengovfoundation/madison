angular.module('madisonApp.controllers')
  .controller('MyDocumentsController', ['$scope', '$http', '$translate', 'growl',
      'growlMessages', 'SessionService', 'AuthService', 'pageService', '$state',
      'USER_ROLES', 'SITE',
    function ($scope, $http, $translate, growl, growlMessages, SessionService,
      AuthService, pageService, $state, USER_ROLES, SITE) {
      "use strict";

      pageService.setTitle($translate.instant('content.mydocuments.title',
        {title: SITE.name}));

      $scope.newDoc = {
        'title': ''
      };
      $scope.docs = [];
      $scope.canCreate = false;

      AuthService.getMyDocs();

      if (!AuthService.isAuthorized([USER_ROLES.admin, USER_ROLES.independent,
        USER_ROLES.groupMember])) {
        $scope.canCreate = false;
      } else {
        $scope.canCreate = true;
      }

      $scope.$on('docsChanged', function () {
        $scope.docs = SessionService.getDocs();
      });

      $scope.createDocument = function () {
        growlMessages.destroyAllMessages();
        var title = $scope.newDoc.title;

        if (!title || !title.trim()) {
          growl.error( $translate.instant('errors.document.new.notitle') );
        }
        else {
          $http.post('/api/docs', {title: title})
            .success(function (data) {
              $scope.newDoc.title = '';
              $state.go('edit-doc', {id: data.doc.id});
            })
            .error(function (error) {
              growl.error( $translate.instant('errors.document.new.general') );
              console.log('Error: ', error);
            });
        }
      };

    }]);
