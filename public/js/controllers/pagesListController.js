angular.module('madisonApp.controllers')
.controller('PagesListController', ['$scope', 'Page', 'growl', '$translate',
  'pageService', 'SITE', 'modalService', '$state', '$rootScope', '$timeout',

function($scope, Page, growl, $translate, pageService, SITE,
  modalService, $state, $rootScope, $timeout) {

  /**
   * Set the page title
   */
  $translate('content.admin.pages.list.title', { title: SITE.name })
  .then(function(translation) {
    pageService.setTitle(translation);
  });

  $scope.newPage = {};
  $scope.focusModalTitle = false;

  /**
   * Load pages from API
   */
  var loadPages = function() {
    $rootScope.loadPages().then(function(pages) {
      $scope.pages = pages;
    });
  }

  $scope.showCreatePageModal = function() {
    modalService.showModal({
      templateUrl: '/templates/partials/modals/create-page.html',
      scope: $scope
    }, {});

    // Timeout so the modal show animation can complete
    $timeout(function() {
      $scope.focusModalTitle = true;
    }, 500);
  };

  $scope.createPage = function() {
    Page.save({ nav_title: $scope.newPage.title }, function(resp) {
      loadPages();
      $state.go('dashboard-edit-page', { id: resp.id });
    }, function(err) {
      $translate('errors.general.save').then(function(translation) {
        growl.error(translation);
      });
    });
  };

  $scope.deletePage = function(id) {
    modalService.showModal({}, {
      closeButtonText:
        $translate.instant('form.general.cancel'),
      actionButtonText:
        $translate.instant('form.general.delete'),
      headerText:
        $translate.instant('form.verify.areyousure'),
      bodyText:
        $translate.instant('form.page.delete.confirm.body')
    }).then(function() {
      Page.delete({ id: id }, loadPages);
    });
  };

  /**
   * Initial loading of pages
   */
  loadPages();

}]);
