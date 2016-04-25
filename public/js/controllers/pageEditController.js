angular.module('madisonApp.controllers')
.controller('PageEditController', ['$scope', 'Page', 'growl', '$translate',
  'pageService', 'SITE', 'modalService', '$state', '$rootScope', '$stateParams',
  '$timeout',

function($scope, Page, growl, $translate, pageService, SITE, modalService,
  $state, $rootScope, $stateParams, $timeout) {

  /**
   * Set the page title
   */
  $translate('content.admin.pages.edit.title', { title: SITE.name })
  .then(function(translation) {
    pageService.setTitle(translation);
  });

  /**
   * Load page from the API
   */
  Page.get({ id: $stateParams.id }, function(page) {
    $scope.page = page;

    /**
     * Watch for changes on the page and save.
     */
    $scope.$watch('page', function(newPage, oldPage) {
      debounceSaveUpdates(newPage, oldPage, $scope.savePage);
    }, true);

  }, function(err) {
    $translate('errors.general.load').then(function(translation) {
      growl.error(translation);
    });
  });

  /**
   * Load page content from the API
   */
  Page.getContent({ id: $stateParams.id }, function(resp) {
    $scope.content = resp.content;
    setupMarkdownEditor();

    /**
     * Watch page content, save on update.
     */
    $scope.$watch('content', function(newContent, oldContent) {
      debounceSaveUpdates(newContent, oldContent, $scope.saveContent);
    }, true);

  }, function(err) {
    $translate('errors.general.load').then(function(translation) {
      growl.error(translation);
    });
  });

  /**
   * Send the page to the API
   */
  $scope.savePage = function() {
    Page.update({ id: $scope.page.id }, $scope.page, function(resp) {
      $rootScope.loadPages();
      $translate('success.general.save').then(function(translation) {
        growl.info(translation);
      });
    }, function(err) {
      $translate('errors.general.save').then(function(translation) {
        growl.error(translation);
      });
    });
  };

  $scope.saveContent = function() {
    Page.updateContent({ id: $scope.page.id }, {
      content: $scope.content
    }, function(resp) {
      $translate('success.general.save').then(function(translation) {
        growl.info(translation);
      });
    }, function(err) {
      $translate('errors.general.save').then(function(translation) {
        growl.error(translation);
      });
    });
  };

  /**
   * Setup markdown editor.
   *
   * TODO: This is duplicated in the doc edit screen, maybe pull into a service?
   */
  function setupMarkdownEditor() {
    $scope.markdownEditor = new Markdown.Editor(
      Markdown.getSanitizingConverter()
    );

    $scope.markdownEditor.run();

    // We don't control the pagedown CSS, and this DIV needs to be
    // scrollable
    $("#wmd-preview").css("overflow", "scroll");

    // Resizing dynamically according to the textarea is hard,
    // so just set the height once (22 is padding)
    $("#wmd-preview").css("height", ($("#wmd-input").height() + 22));
    $("#wmd-input").scroll(function () {
      $("#wmd-preview").scrollTop($("#wmd-input").scrollTop());
    });
  }

  /**
   * TODO: seriously there has to be a way to debounce that doesn't involve me
   * rewriting this dumb debounce function over and over.
   */
  function debounceSaveUpdates(newObject, oldObject, saveFunc) {
    if (newObject !== oldObject) {
      $scope.saveStatus = 'saving';

      if ($scope.saveTimeout) {
        $timeout.cancel($scope.saveTimeout);
      }

      $scope.saveTimeout = $timeout(saveFunc, 1000, true);
    }
  }

}]);
