angular.module('madisonApp.prompts', []);

//The Directive <header prompts></header>
angular.module('madisonApp.prompts')
  .directive('prompts', [function () {
    var content = 'Want to help DC craft its legislation?  <a href="">Create an account to annotate and comment &raquo;</a>';

    return {
      restrict: 'A',
      templateUrl: '/templates/partials/prompts.html',
      scope: {
        reference: '@',
        inline: '=',
      },
      controller: [
        '$scope',
        'prompts',
        'promptMessages',
        function ($scope, prompts, promptMessages) {
          $scope.referenceId = $scope.reference || 0;

          promptMessages.initDirective($scope.referenceId);

          $scope.promptMessages = promptMessages;

          $scope.alertClasses = function (message) {
            return {
              'alert-success': message.severity === 'success',
              'alert-error': message.severity === 'error',
              'alert-danger': message.severity === 'error',
              'alert-info': message.severity === 'info',
              'alert-warning': message.severity === 'warning',
              'icon': message.disableIcons === false,
              'alert-dismissable': !message.disableCloseButton
            };
          };
        }
      ]
    };
  }]);

//The prompts provider
angular.module('madisonApp.prompts').provider('prompts', function () {

});

//The promptsMessages service
angular.module('madisonApp.prompts').service('promptsMessages', [

]);
