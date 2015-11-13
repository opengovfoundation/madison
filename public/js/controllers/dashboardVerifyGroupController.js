angular.module('madisonApp.controllers')
  .controller('DashboardVerifyGroupController', ['$scope', '$http', '$translate',
    'pageService', 'SITE',
    function ($scope, $http, $translate, pageService, SITE) {
      pageService.setTitle($translate.instant('content.verifygroups.title',
        {title: SITE.name}));

      $scope.requests = [];
      $scope.formdata = {
        'status' : 'pending'
      };

      $scope.getRequests = function () {
        $http.get('/api/groups/verify', { params: $scope.formdata } )
          .success(function (data) {
            $scope.requests = data;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.update = function (request, status) {
        $http.post('/api/groups/verify', {'request': request, 'status': status})
          .success(function (data) {
            request.status = status;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.getRequests();
    }]);
