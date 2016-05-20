angular.module('madisonApp.directives')
  .directive('profileCompletionMessage', ['$http',
    function ($http) {
      return {
        restrict: 'A',
        templateUrl: '/templates/profile-completion-message.html',
        link: function (scope) {

          scope.updateEmail = function (newEmail, newPassword) {
            //Issue PUT request to update user
            $http.put('/api/user/' + scope.user.id + '/edit/email', {email: newEmail, password: newPassword})
              .success(function () {
                //Note: Growl message comes from server response
                scope.user.email = newEmail;
              }).error(function (data) {
                console.error("Error updating user email: %o", data);
              });
          };

          scope.resendEmailVerification = function () {
            $http.post('/api/user/' + scope.user.id + '/verify-email/resend')
              .success(function () {
                //Note: Growl message comes from server response
              }).error(function (data) {
                console.error("Error requesting verification email resend: %o", data);
              });
          };
        }
      };
    }]);
