angular.module('madisonApp.controllers')
  .controller('MyDocumentsController', ['$scope', '$http', '$translate', 'growl',
      'growlMessages', 'SessionService', 'AuthService', 'pageService', '$state',
      'USER_ROLES', 'SITE',
    function ($scope, $http, $translate, growl, growlMessages, SessionService,
      AuthService, pageService, $state, USER_ROLES, SITE) {
      "use strict";

      pageService.setTitle($translate.instant('content.mydocuments.title',
        {title: SITE.name}));

      $scope.newDoc = {
        'title': '',
        'group_id': null
      };
      $scope.docs = [];
      $scope.canCreate = false;

      AuthService.getUser().then(function() {
        $scope.canCreate = false;
        $scope.groupOptions = [];

        AuthService.getMyDocs();

        if (AuthService.isAuthorized([USER_ROLES.admin, USER_ROLES.independent,
          USER_ROLES.groupMember])) {
          $scope.canCreate = true;
        }

        for(var i = 0; i < SessionService.groups.length; i++) {
          var group = SessionService.groups[i];
          if(group.status === 'active') {
          console.log(group.name, group.status);
            $scope.groupOptions.push([group.id, group.name]);
          }
        }

        // If there's not already a group, select the first one.
        if($scope.groupOptions.length && !$scope.newDoc.group_id) {
          $scope.newDoc.group_id = $scope.groupOptions[0][0];
        }

        $scope.$on('docsChanged', function () {
          $scope.docs = SessionService.getDocs();
        });

        $scope.createDocument = function () {
          growlMessages.destroyAllMessages();

          if (!$scope.newDoc.title || !$scope.newDoc.title.trim()) {
            growl.error( $translate.instant('errors.document.new.notitle') );
          }
          else {
            $http.post('/api/docs', $scope.newDoc)
              .success(function (data) {
                $scope.newDoc.title = '';
                $state.go('edit-doc', {id: data.doc.id});
              })
              .error(function (error) {
                growl.error( $translate.instant('errors.document.new.general') );
                console.log('Error: ', error);
              });
          }
        };
      });

    }]);
