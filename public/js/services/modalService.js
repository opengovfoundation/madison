//Built using the tutorial at http://weblogs.asp.net/dwahlin/building-an-angularjs-modal-service
angular.module('madisonApp.services')
  .service('modalService', ['$uibModal',
    function ($modal) {

      //Set modal defaults
      var modalDefaults = {
        backdrop: true,
        keyboard: true,
        modalFade: true,
        templateUrl: '/templates/modal.html'
      };

      var modalOptions = {
        closeButtonText: 'Close',
        actionButtonText: false,
        headerText: 'Notice',
        bodyText: 'Hmm... someone forgot the content here...'
      };

      this.showModal = function (customModalDefaults, customModalOptions) {

        if (!customModalDefaults) {
          customModalDefaults = {};
        }

        //Accepts either true or 'static'.  'static' doesn't close the modal on click.
        customModalDefaults.backdrop = true;

        return this.show(customModalDefaults, customModalOptions);
      };

      this.show = function (customModalDefaults, customModalOptions) {
        //Create temp objects to work with since we're in a singleton service
        var tempModalDefaults = {};
        var tempModalOptions = {};

        //Map angular-ui modal custom defaults to modal defaults defined in service
        angular.extend(tempModalDefaults, modalDefaults, customModalDefaults);

        //Map modal.html $scope custom properties to defaults defined in service
        angular.extend(tempModalOptions, modalOptions, customModalOptions);

        if (!tempModalDefaults.controller) {
          tempModalDefaults.controller = function ($scope, $uibModalInstance) {
            $scope.modalOptions = tempModalOptions;

            $scope.modalOptions.ok = function (result) {
              $uibModalInstance.close(result);
            };

            $scope.modalOptions.close = function (result) {
              $uibModalInstance.dismiss('cancel');
            };
          };
        }

        return $modal.open(tempModalDefaults).result;
      };
    }]);
