'use strict';

/**
 * @ngdoc service
 * @name angularjsApp.descuento
 * @description
 * # descuento
 * Factory in the angularjsApp.
 */
angular.module('angularjsApp')
  .factory('descuento', function (constantes, $resource) {
        return {
            datos: function () {
                return $resource(constantes.URL + 'descuentos/:sid',
                    {sid : '@sid'},
                    {   
                        update : { 'method': 'PUT' },
                        delete : { 'method': 'DELETE' },
                        create : { 'method': 'POST' }
                    }
                );
            },
            masivo : function(){
                return $resource(constantes.URL + 'descuentos/ingreso/masivo',
                    {},
                    { post : { 'method': 'POST' } }
                );
            },
            importar : function(){
                return $resource(constantes.URL + 'descuentos/generar-ingreso/masivo',
                    {},
                    { post : { 'method': 'POST' } }
                );
            }
        };
  });
