angular.module('madisonApp.controllers')
  .controller('DashboardVerifyGroupController', ['$scope', '$http', '$translate',
    'growl', 'growlMessages', 'pageService', 'SITE',
    function ($scope, $http, $translate, growl, growlMessages, pageService,
      SITE) {
      $translate('content.verifygroups.title', {title: SITE.name}).then(function(translation) {
        pageService.setTitle(translation);
      });

      $scope.requests = null;
      $scope.formdata = {
        'status' : 'pending'
      };

      $scope.getRequests = function () {
        $http.get('/api/groups/verify', { params: $scope.formdata } )
          .success(function (data) {
            if(data.length) {
              $scope.requests = {};
              for(var i in data) {
                $scope.requests[data[i].id] = data[i];
              }
            }
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.update = function (group, status) {
        growlMessages.destroyAllMessages();

        // Copy the object.
        var newGroup = angular.extend({}, group, {status: status});

        $http.put('/api/groups/verify/' + newGroup.id, newGroup)
          .success(function (data) {
            $scope.requests[data.id] = group = data;
          })
          .error(function (data) {
            growl.error('Unable to update group.');
            console.error(data);
          });
      };

      $scope.getRequests();
    }]);
