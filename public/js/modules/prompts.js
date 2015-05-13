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
  this.$get = [
    '$rootScope',
    'promptMessages',
    '$sce',
    '$interpolate',
    '$timeout',
    function ($rootScope, promptMessages, $sce, $interpolate, $timeout) {
      function broadcastPrompt(message) {
        var polation = $interpolate(message.text);
        message.text = polation;

        var addedPrompt = promptMessages.addPrompt(message);

        $rootScope.$broadcast('promptMessage', message);

        // Is this necessary?
        // $timeout(function () {
        // }, 0);

        return addedPrompt;
      }

      function sendPrompt(text, severity) {
        var message = {
          text: text,
          severity: severity,
          destroy: function () {
            promptMessages.deletePrompt(message);
          },
          setText: function (newText) {
            message.text = $sce.trustAsHtml(String(newText));
          }
        };

        return broadcastPrompt(message);
      }

      function warning(text) {
        return sendPrompt(text, 'warning');
      }

      function error(text) {
        return sendPrompt(text, 'error');
      }

      function info(text) {
        return sendPrompt(text, 'info');
      }

      function success(text) {
        return sendPrompt(text, 'success');
      }

      return {
        warning: warning,
        error: error,
        info: info,
        success: success
      };
    }
  ];
});

// //The promptMessages service
// angular.module('madisonApp.prompts').service('promptMessages', [

// ]);
