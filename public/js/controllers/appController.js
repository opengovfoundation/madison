angular.module('madisonApp.controllers')
  .controller('AppController', ['$scope', 'ipCookie', 'UserService',
    function ($scope, ipCookie, UserService) {
      //Watch for user data change
      $scope.$on('userUpdated', function () {
        $scope.user = UserService.user;
      });

      //Load user data
      UserService.getUser();

      //Set up Angular Tour
      $scope.step_messages = {
        step_0: 'Welcome to Madison!  Help create better policy in your community.  Click Next to continue.',
        step_1: 'Getting Started:  Choose a policy document.  You can browse, or filter by title, category, sponsor, or status. Go ahead, choose one!',
        step_2: 'Next, dive in! Scroll or use the Table of Contents to get to the good stuff.',
        step_3: 'Share ideas and questions with the document sponsor and other users in the Discussion tab.',
        step_4: 'Suggest specific changes to the text.  Just highlight part of the document and add your thoughts!'
      };

      $scope.currentStep = ipCookie('myTour') || 0;

      $scope.stepComplete = function () {
        ipCookie('myTour', $scope.currentStep, {path: '/', expires: 10 * 365});
      };

      $scope.tourComplete = function () {
        ipCookie('myTour', 99, {path: '/', expires: 10 * 365});
      };
    }]);