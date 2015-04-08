angular.module('madisonApp.controllers')
  .controller('MyDocumentsController', ['$scope', '$http', 'growl', 'SessionService', 'AuthService',
    function ($scope, $http, growl, SessionService, AuthService) {
      "use strict";

      AuthService.getMyDocs();

      $scope.$on('docsChanged', function () {
        $scope.docs = SessionService.getDocs();
      });

    }]);
