/*global annotator*/
/*global Markdown*/
angular.module('madisonApp.controllers')
  .controller('DeletedDocsController', ['$scope', '$state', 'growl',
      '$location', 'Doc', '$http', '$translate', 'pageService', 'SITE',
      'modalService',
    function ($scope, $state, growl, $location, Doc,
      $http, $translate, pageService, SITE, modalService) {

      // TODO: check from URL if is admin or not
      var IS_ADMIN = false;

      $scope.docs = [];
      $scope.pageTitle = '';

      pageService.setTitle(
        $translate.instant('content.deleteddocs.title',
        { title: SITE.name })
      );

      if (checkAdminRoute()) {
        IS_ADMIN = true;
        $scope.pageTitle += $translate.instant('menu.nav.admin') + ' ';
      }

      $scope.pageTitle += $translate.instant('content.deleteddocs.header');

      $scope.init = function() {
        getDeletedDocs()
        .success(function(data) {
          $scope.docs = data;
        }).error(function(data) {
          growl.error($translate.instant('errors.general.load'));
        });
      };

      $scope.deletedByFromPublishState = function(publishState) {
        return publishState.replace('deleted-', '');
      };

      $scope.restoreDoc = function(doc) {
        var modalOptions = {
          closeButtonText:
            $translate.instant('form.general.cancel'),
          actionButtonText:
            $translate.instant('document.action.restore'),
          headerText:
            $translate.instant('form.verify.areyousure'),
          bodyText:
            $translate.instant('form.document.restore.confirm.body')
        };

        modalService.showModal({}, modalOptions)
        .then(function() {
          $http.get('/api/docs/' + doc.id + '/restore')
          .success(function(data) {
            growl.success($translate.instant('form.document.restore.success'));
            $state.go('edit-doc', { id: doc.id });
          }).error(function(data) {
            growl.error($translate.instant('errors.document.restore'));
          });
        });
      };

      function getDeletedDocs() {
        if (IS_ADMIN) {
          return $http.get('/api/docs/deleted/admin');
        } else {
          return $http.get('/api/docs/deleted');
        }
      }

      function checkAdminRoute() {
        return $location.path().match(/administrative\-dashboard/);
      }

      // Do things!
      $scope.init();

    }]);
