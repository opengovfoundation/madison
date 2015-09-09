angular.module('madisonApp.controllers')
  .controller('MyDocumentsController', ['$scope', '$http', 'growl', 'SessionService', 'AuthService', '$state', 'USER_ROLES',
    function ($scope, $http, growl, SessionService, AuthService, $state, USER_ROLES) {
      "use strict";

      $scope.newDoc = {
        'title': ''
      };
      $scope.docs = [];
      $scope.canCreate = false;

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
        var title = $scope.newDoc.title;

        if (!title || !title.trim()) {
          growl.error('You must enter a document title to create a new document.');
        }
        else {
          $http.post('/api/docs', {title: title})
            .success(function (data) {
              console.log(data.doc);
              $scope.newDoc.title = ''
              $state.go('edit-doc', {id: data.doc.id});
            })
            .error(function (error) {
              growl.error('There was an error creating the document. Please try again later.');
              console.log('Error: ', error);
            });
        }
      };

    }]);
