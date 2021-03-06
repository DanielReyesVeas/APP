'use strict';

describe('Controller: ClausulasContratoCtrl', function () {

  // load the controller's module
  beforeEach(module('angularjsApp'));

  var ClausulasContratoCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    ClausulasContratoCtrl = $controller('ClausulasContratoCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(ClausulasContratoCtrl.awesomeThings.length).toBe(3);
  });
});
