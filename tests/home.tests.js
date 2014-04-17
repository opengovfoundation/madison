/* 
* @Author: Chris Birk
* @Date:   2014-04-17 17:03:34
* @Last Modified by:   Chris Birk
* @Last Modified time: 2014-04-17 17:43:50
*/

describe('HomePageController Tests', function(){
  var $scope, $rootScope, $httpBackend;

  beforeEach(module('madisonApp'));
  beforeEach(module('madisonApp.controllers'));

  beforeEach(inject(function($controller, $rootScope, $httpBackend){
    scope = $rootScope.$new();
    ctrl = $controller('HomePageController', {
      '$scope': scope,
    });
  }));

  it('should create docSort model as "created_at"', function(){
    expect(scope.docSort).toBe('created_at');
  });

  it('should have an init function', function(){
    expect(typeof scope.init == 'function').toBeTruthy();
  });
});