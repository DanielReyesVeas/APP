'use strict';

/**
 * @ngdoc service
 * @name angularjsApp.mesDeTrabajo
 * @description
 * # mesDeTrabajo
 * Factory in the angularjsApp.
 */
angular.module('angularjsApp')
  .factory('mesDeTrabajo', function ($resource, constantes) {
    return {
      datos: function () {
        return $resource(constantes.URL + 'mes-de-trabajo/:sid',
          {sid : '@sid'},
          {
            update : { 'method': 'PUT' },
            delete : { 'method': 'DELETE' },
            create : { 'method': 'POST' }
          }
        );
      },
      centralizar : function(){
        return $resource(constantes.URL + 'mes-de-trabajo/cuentas/obtener',
            {},
            { post : { 'method': 'POST' } }
        );
      }
    };
  });
