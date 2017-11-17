'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:LoginCtrl
 * @description
 * # LoginCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('LoginCtrl', function ($scope, $rootScope, $window, $timeout, $location, login, Notification, empresa, constantes) {
    $scope.alert = {};
		$scope.username='';
		$scope.password='';
    $scope.opciones={};
    $scope.opciones.empresas = [];
    $scope.constantes = constantes;
		$rootScope.cargando=false;

  	$scope.loginBtn = function(){
      $rootScope.habilitarVista=true;
      $rootScope.cargando = true;
      login.Login($scope.username, $scope.password, $scope.empresa, function (response) {
        if (response.success) {
          if(response.accesos.length>0){
            Notification.clearAll();
            login.SetCredentials($scope.username, $scope.password, response.usuario, response.menu, response.accesos, response.inicio, response.imagen, response.nombre, response.empresas, response.empresa, response.max, response.uID, response.listaMesesDeTrabajo, response.uf, response.utm, response.uta );                                  
            $location.path(response.inicio);
            $timeout(function(){
              $('#main-menu').smartmenus('refresh');
            }, 500);
          }else{
            Notification.clearAll();
            Notification.error({message: 'El usuario no cuenta con ningún acceso. <br />Por favor comuníquse con el <b>Administrador</b>.', title: 'Mensaje del Sistema', delay : ''});
            $rootScope.cargando= false;
          }
          /*
          $window.location.reload();
          */
        } else {
          Notification.clearAll();
          Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
          $rootScope.cargando= false;
          $scope.cerrarAlert();
        }
      });
    };

    $scope.cerrarAlert = function(){
      $timeout(function(){
        $scope.alert.mensaje='';
      }, 3000);
    };
    login.ClearCredentials();

    $scope.cargarDatos = function(){
      /*$rootScope.cargando=true;
      var datos = empresa.listaSelect().query();
      datos.$promise.then(function(respuesta){
        $scope.opciones.empresas = respuesta;
        $rootScope.cargando=false;
      })*/
    };

    $scope.cargarDatos();
        
  });
