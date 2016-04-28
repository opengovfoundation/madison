/*
  Build using angular-growl.js as a guide.
*/
angular.module('madisonApp.prompts', []);

//The Directive <header prompts></header>
angular.module('madisonApp.prompts')
  .directive('prompts', [function () {
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

          $scope.promptMessages = promptMessages;

          $scope.alertClasses = function (message) {
            return {
              'prompt-success': message.severity === 'success',
              'prompt-error': message.severity === 'error',
              'prompt-danger': message.severity === 'error',
              'prompt-info': message.severity === 'info',
              'prompt-warning': message.severity === 'warning'
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
        message.text = polation(message.variables);

        var addedPrompt = promptMessages.addMessage(message);

        $rootScope.$broadcast('promptMessage', message);

        //I think this re-runs the scope apply
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

//The promptMessages service
angular.module('madisonApp.prompts').service('promptMessages', [
  '$sce',
  '$timeout',
  function ($sce, $timeout) {
    'use strict';

    this.messages = [];

    this.addMessage = function (message) {
      this.messages.push(message);
    };

    this.getAllMessages = function (message) {
      return this.messages;
    };

    this.destroyAllMessages = function () {
      angular.forEach(this.messages, function (message) {
        message.destroy();
      });

      this.messages = [];
    };

    this.deleteMessage = function (message) {
      var index = this.messages.indexOf(message);

      if (index > -1) {
        this.messages[index].close = true;
        this.messages.splice(index, 1);
      }
    };

    return this;
  }
]);
