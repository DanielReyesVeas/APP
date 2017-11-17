'use strict';

/**
 * @ngdoc service
 * @name angularjsApp.haber
 * @description
 * # haber
 * Factory in the angularjsApp.
 */
angular.module('angularjsApp')
  .factory('tipoHaber', function (constantes, $resource) {
        return {
            datos: function () {
                return $resource(constantes.URL + 'tipos-haber/:sid',
                    {sid : '@sid'},
                    {   
                        update : { 'method': 'PUT' },
                        delete : { 'method': 'DELETE' },
                        create : { 'method': 'POST' }
                    }
                );
            },
            ingresoHaberes : function(){
              return $resource(constantes.URL + 'tipos-haber/ingreso-haberes/obtener');
            },
            updateCuenta : function(){
              return $resource(constantes.URL + 'tipos-haber/cuentas/actualizar',
                  {},
                  { post : { 'method': 'POST' } }
              );
            }
        };
  });