angular.module('madisonApp.controllers')
  .controller('MyDocumentsController', ['$scope', '$http', 'growl', 'SessionService',
    function ($scope, $http, growl, SessionService) {

      $scope.user = SessionService.user;

      $scope.$on('sessionChanged', function () {
        $scope.user = SessionService.user;
      });

      $http.get('/api/user/' + $scope.user.id + '/docs')
        .success(function (data) {
          $scope.doc_count = data.doc_count;
          $scope.documents = data.documents;
        }).error(function (response) {
          growl.error('There was an error retrieving your documents.');
          console.error(response);
        });
    }]);