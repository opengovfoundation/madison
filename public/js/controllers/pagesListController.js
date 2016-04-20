angular.module('madisonApp.controllers')
.controller('PagesListController', ['$scope', 'Page', 'growl', '$translate',
  'pageService', 'SITE', 'modalService', '$state', '$rootScope',

function($scope, Page, growl, $translate, pageService, SITE,
  modalService, $state, $rootScope) {

  /**
   * Set the page title
   */
  $translate('content.admin.pages.list.title', { title: SITE.name })
  .then(function(translation) {
    pageService.setTitle(translation);
  });

  $scope.newPage = {};

  /**
   * Load pages from API
   */
  var loadPages = function() {
    Page.query(function(pages) {
      $scope.pages = pages;
      $rootScope.reloadPages(pages);
    }, function(err) {
      $translate('errors.general.load').then(function(translation) {
        growl.error(translation);
      });
    });
  }

  $scope.showCreatePageModal = function() {
    modalService.showModal({
      templateUrl: '/templates/partials/modals/create-page.html',
      scope: $scope
    }, {});
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
    Page.delete({ id: id }, loadPages);
  };

  /**
   * Initial loading of pages
   */
  loadPages();

}]);
