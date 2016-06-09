angular.module('madisonApp.controllers')
  .controller('ResendConfirmationController', ['$scope', '$http', '$state',
    '$stateParams', '$translate', 'pageService', 'SITE', '$timeout', 'AuthService',
    function ($scope, $http, $state, $stateParams, $translate, pageService,
      SITE, $timeout, AuthService) {
      $translate('content.confirmationresend.title', {title: SITE.name}).then(function(translation) {
        pageService.setTitle(translation);
      });

      // We do this in a `$timeout` because in IE9, when going
      // from regular URL to non-HTML5 version, the controller loads twice and
      // causes the token to be submitted twice.
      $timeout(function() {
        //If we're following a link from the verification email
        if ($stateParams.token) {
          $http.post('/api/user/verify-email', {
            token: $stateParams.token
          }).success(function (response) {

            $http.get('/api/user/current').success(function(res) {
              var user = {};
              if (!$.isEmptyObject(res) && Object.getOwnPropertyNames(res.user).length > 0) {
                for (var key in res.user) {
                  if (res.user.hasOwnProperty(key)) {
                    user[key] = res.user[key];
                  }
                }
              } else {
                user = null;
              }
              AuthService.setUser(user);

              // $location.url(url);
              $state.go('index', {}, {reload: true, notify: true});
            });

          }).error(function (response) {
            $state.go('login');
            console.error(response);
          });
        }
      }, 500);

      $scope.resendConfirmation = function () {
        $http.post('/api/verification/resend', {
          email: $scope.email,
          password: $scope.password
        }).success(function (response) {
          $state.go('login');
        }).error(function (response) {
          console.error(response);
        });
      };
    }]);
