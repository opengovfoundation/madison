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
      $scope.independent_sponsor = false;

      AuthService.getUser().then(function() {
        $scope.canCreate = false;
        $scope.groupOptions = [];

        AuthService.getMyDocs();

        if (AuthService.isAuthorized([USER_ROLES.admin, USER_ROLES.independent,
          USER_ROLES.groupMember])) {
          $scope.canCreate = true;
        }

        $scope.independent_sponsor = SessionService.user.independent_sponsor;

        for(var i = 0; i < SessionService.groups.length; i++) {
          var group = SessionService.groups[i];
          if(group.status === 'active') {
            $scope.groupOptions.push([group.id, group.name]);
          }
        }

        if($scope.independent_sponsor) {
          $scope.newDoc.group_id = '';
        }
        else if($scope.groupOptions.length) {
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
