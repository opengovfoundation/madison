/* 
 * @Author: Chris Birk
 * @Date:   2014-04-17 17:03:34
 * @Last Modified by:   Chris Birk
 * @Last Modified time: 2014-04-23 23:36:24
 */

/*global describe, beforeEach, inject, it, expect*/

describe('HomePageController Tests', function () {
  var $scope, $rootScope, $httpBackend;

  beforeEach(module('madisonApp'));
  beforeEach(module('madisonApp.controllers'));

  beforeEach(inject(function ($controller, $rootScope, $httpBackend) {
    var scope = $rootScope.$new();
    var ctrl = $controller('HomePageController', {
      '$scope': scope,
    });
  }));

  it('should create docSort model as "created_at"', function () {
    expect(scope.docSort).toBe('created_at');
  });

  it('should have an init function', function () {
    expect(typeof scope.init == 'function').toBeTruthy();
  });

  it('should load a list of documents', function () {
    expect(false).toBe(true);
  });

  it('should create an array of categories', function () {
    expect(false).toBe(true);
  };

  it('should create an array of statuses', function () {
    expect(false).toBe(true);
  });

  it('should create an array of sponsors', function () {
    expect(false).toBe(true);
  });

  it('should initialize the select2 element', function () {
    expect(false).toBe(true);
  });

  it('should filter by category', function () {
    expect(false).toBe(true);
  });

  it('should filter by status', function () {
    expect(false).toBe(true);
  });

  it('should filter by sponsor', function () {
    expect(false).toBe(true);
  });

  it('should only link to existing pages', function () {
    expect(false).toBe(true);
  });
});