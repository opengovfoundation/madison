angular.module('madisonApp.controllers')
  .controller('DashboardVerifyUserController', ['$scope', '$http', '$translate',
    'pageService', 'SITE', 'modalService',
    function ($scope, $http, $translate, pageService, SITE, modalService) {
      $translate('content.verifyindependent.title', {title: SITE.name}).then(function(translation) {
        pageService.setTitle(translation);
      });

      $scope.requests = [];
      $scope.formdata = {
        'status' : 'pending'
      };

      $scope.getRequests = function () {
        $http.get('/api/user/independent/verify', { params: $scope.formdata } )
          .success(function (data) {
            $scope.requests = data;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.deny = function(request, idx) {
        var modalOptions = {
          closeButtonText: $translate.instant('form.general.cancel'),
          actionButtonText: $translate.instant('form.verify.deny'),
          headerText: $translate.instant('form.verify.areyousure'),
          bodyText: $translate.instant('form.verify.deny.confirm.body')
        };

        //Open the dialog
        var res = modalService.showModal({}, modalOptions);

        res.then(function () {
          $scope.update(request, idx, 'denied');
        });
      };

      $scope.update = function (request, idx, status) {
        $http.post('/api/user/independent/verify',
            {'request': request, 'status': status})
          .success(function (data) {
            switch (status) {
              case 'verified':
                $scope.requests[idx].meta_value = '1';
                break;
              case 'pending':
                $scope.requests[idx].meta_value = '0';
                break;
              case 'denied':
                $scope.requests.splice(idx, 1);
                break;
            }
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.getRequests();
    }]);
