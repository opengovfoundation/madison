angular.module('madisonApp.controllers')
  .controller('MyDocumentsController', ['$scope', '$http', 'growl', 'SessionService', 'AuthService', '$state', 'USER_ROLES',
    function ($scope, $http, growl, SessionService, AuthService, $state, USER_ROLES) {
      "use strict";

      AuthService.getMyDocs();

      if (!AuthService.isAuthorized([USER_ROLES.admin, USER_ROLES.independent, USER_ROLES.groupMember])) {
        $scope.canCreate = false;
      } else {
        $scope.canCreate = true;
      }

      $scope.$on('docsChanged', function () {
        $scope.docs = SessionService.getDocs();
      });

      $scope.createDocument = function () {
        var title = $scope.newDocTitle;

        if (!title || !title.trim()) {
          growl.error('You must enter a document title to create a new document.');
        }

        $http.post('/api/docs', {title: title})
          .success(function (data) {
            $state.go('edit-doc', {id: data.id});
          })
          .error(function () {
            $scope.newDocTitle = null;
          });
      };

    }]);
