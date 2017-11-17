'use strict';

/**
 * @ngdoc service
 * @name angularjsApp.vacaciones
 * @description
 * # vacaciones
 * Factory in the angularjsApp.
 */
angular.module('angularjsApp')
  .factory('vacaciones', function (constantes, $resource) {
        return {
            datos: function () {
                return $resource(constantes.URL + 'vacaciones/:sid',
                    {sid : '@sid'},
                    {   
                        update : { 'method': 'PUT' },
                        delete : { 'method': 'DELETE' },
                        create : { 'method': 'POST' }
                    }
                );
            },
            calcular : function(){
              return $resource(constantes.URL + 'vacaciones/calculo/obtener',
                {},
                { post : { 'method': 'POST' } }
              );
            }
        };
  });
