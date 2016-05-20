describe('HomePageController', function () {
    var controller = null, $scope = null;
    
    beforeEach(function () {
        module('madisonApp');
    });
    
    beforeEach(inject(function ($controller, $rootScope) {
        $scope = $rootScope.$new();
        controller = $controller('HomePageController', {
            $scope: $scope
        });
    }));
    
    it('Initially has a variable', function () {
        assert.equal($scope.docSort, "created_at");
    });
});