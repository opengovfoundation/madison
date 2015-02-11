angular.module('madisonApp.controllers')
  .controller('MyDocumentsController', ['$scope', '$state', '$http', 'growl', 'UserService',
    function ($scope, $state, $http, growl, UserService) {
      
      $scope.user = UserService.user;

      // if(!$scope.user.loggedin()){
      //   growl.error('You must be logged in to view your documents!');
      //   $state.go('index');
      // }

      $http.get('/api/user/' + $scope.user.id + '/docs')
        .success(function (data) {
          $scope.doc_count = data.doc_count;
          $scope.documents = data.documents;
        }).error(function (response) {
          growl.error('There was an error retrieving your documents.');
          console.error(response);
        });
    }
  ]);