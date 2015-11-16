angular.module('madisonApp.controllers')
  .controller('LoginPageController', ['$rootScope', '$scope', '$state', 'AuthService',
    'growl', '$translate', 'pageService', '$location', '$http', 'SITE',
    function ($rootScope, $scope, $state, AuthService, growl, $translate, pageService,
      $location, $http, SITE) {

      pageService.setTitle($translate.instant('content.login.title',
        {title: SITE.name}));

      $scope.credentials = {email: "", password: "", remember: false};

      /**
      * Attempt to log the user in
      *   Attempt to login with the form credentials,
      *     On success reset the credentials, reset the current user via
      *     getUser(), return to homepage with success message
      *     On error log the error to console.  If there are no growl error
      *     messages sent from the server, an error message is presented.
      */
      $scope.login = function () {
        var login = AuthService.login($scope.credentials);

        login.then(function () {
          growl.destroyAllMessages();
          $scope.credentials = {email: "", password: "", remember: false};

          $http.get('/api/user/current')
            .success(function (res) {
              var key;
              var user = {};
              if (!$.isEmptyObject(res) && Object.getOwnPropertyNames(res.user).length > 0) {
                for (key in res.user) {
                  if (res.user.hasOwnProperty(key)) {
                    user[key] = res.user[key];
                  }
                }
              } else {
                user = null;
              }
              AuthService.setUser(user)

              var url = 'index';
              if($rootScope.returnTo)
              {
                url = $rootScope.returnTo;
              }
              console.log('url', url);

              growl.success($translate.instant('form.login.success'));
              $location.url(url);


            });
        });
      };

      $scope.facebookLogin = function () {
        var login = AuthService.facebookLogin();
      };
    }]);
