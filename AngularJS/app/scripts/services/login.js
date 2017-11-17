'use strict';

/**
 * @ngdoc service
 * @name angularjsApp.login
 * @description
 * # login
 * Factory in the angularjsApp.
 */
angular.module('angularjsApp')
    .factory('login', function ($http, $localStorage, $rootScope, $timeout, constantes, valorIndicador) {
        return{
            Login : function(username, password, empresa, callback){
                $http.post(constantes.URL + 'login',{
                        username: username, 
                        password: password,
                        empresa : empresa
                    }).then(function (response) {
                    callback(response.data);
                });
            },
            SetCredentials : function(username, password, nomUsuario, menu, accesos, defecto, imagen, nombre, empresas, empresa, max, uID, listaMesesDeTrabajo, uf, utm, uta){
                var authdata = username;
 
                $rootScope.globals = {
                    currentUser: {
                        username: username,
                        authdata: authdata,
                        nomUsuario : nomUsuario,
                        listaMesesDeTrabajo : listaMesesDeTrabajo,
                        menu : menu,
                        imagen : imagen,
                        nombre : nombre,
                        accesos : accesos,
                        default : defecto,
                        empresas : empresas,
                        empresa : empresa,
                        max : max,
                        uID : uID
                    },
                    indicadores: {
                      uf : uf, 
                      utm : utm, 
                      uta : uta
                    }
                };

                if(!$rootScope.globals.indicadores.uf || !$rootScope.globals.indicadores.utm || !$rootScope.globals.indicadores.uta ){
                  $rootScope.globals.isIndicadores = false;
                }else{
                  $rootScope.globals.isIndicadores = true;
                }

                $localStorage.globals = $rootScope.globals;
  //              $http.defaults.headers.common['Authorization'] = 'Basic ' + authdata; // jshint ignore:line
                
            },
            ClearCredentials : function(){
                $rootScope.globals = {};
                $rootScope.menu = {};
                $localStorage.$reset();
     //           $http.defaults.headers.common.Authorization = 'Basic ';
            }
        };
    });
    