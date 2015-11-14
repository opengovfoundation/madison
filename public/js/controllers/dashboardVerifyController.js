angular.module('madisonApp.controllers')
  .controller('DashboardVerifyController', ['$scope', '$http', '$translate',
    'pageService', 'SITE',
    function ($scope, $http, $translate, pageService, SITE) {
      pageService.setTitle($translate.instant('content.verifyusers.title',
        {title: SITE.name}));

      $scope.requests = [];
      $scope.formdata = {
        'status' : 'pending'
      };

      $scope.init = function () {
        $scope.getRequests();
      };

      $scope.getRequests = function () {
        $http.get('/api/user/verify', { params: $scope.formdata } )
          .success(function (data) {
            $scope.requests = data;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.update = function (request, status) {
        $http.post('/api/user/verify', {
          'request': request,
          'status': status
        })
          .success(function () {
            request.meta_value = status;
          })
          .error(function (data) {
            console.error(data);
          });
      };
    }
    ]);
