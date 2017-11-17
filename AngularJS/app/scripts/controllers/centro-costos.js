'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:CentroCostosCtrl
 * @description
 * # CentroCostosCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('CentroCostosCtrl', function ($scope, $uibModal, $filter, $anchorScroll, centroCosto, constantes, $rootScope, Notification) {
    $anchorScroll();

    $scope.datos = [];
    $scope.constantes = constantes;
    $scope.cargado = false;
    
    $scope.open = function (obj) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nuevo-centro-costo.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormCentrosCostoCtrl',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        cargarDatos();
      }, function () {
        javascript:void(0)
      });
    };

    $scope.eliminar = function(objeto){
      $rootScope.cargando=true;
      $scope.result = centroCosto.datos().delete({ sid: objeto.sid });
      $scope.result.$promise.then( function(response){
          if(response.success){
            Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
            cargarDatos();
          }
      });
    };

    function cargarDatos(){
      $scope.cargado = false;
      $rootScope.cargando = true;
      var datos = centroCosto.datos().get();
      datos.$promise.then(function(response){
        $scope.accesos = response.accesos;
        $scope.datos = response.datos;
        $rootScope.cargando = false;
        $scope.cargado = true;
      });
    };

    cargarDatos();

})
  .controller('FormCentrosCostoCtrl', function ($scope, $uibModalInstance, objeto, Notification, $rootScope, centroCosto) {

    if(objeto){
      $scope.centroCosto = angular.copy(objeto);
      $scope.tituloFormulario = 'Modificación Centros de Costo';
      $scope.encabezado = $scope.centroCosto.nombre;
    }else{
      $scope.centroCosto = {};
      $scope.tituloFormulario = 'Ingreso Centros de Costo';
      $scope.encabezado = 'Nuevo Centro de Costo';
    }
    
    $scope.guardar = function () {
      $rootScope.cargando=true;
      var response;
      if( $scope.centroCosto.sid ){
          response = centroCosto.datos().update({sid:$scope.centroCosto.sid}, $scope.centroCosto);
      }else{
          response = centroCosto.datos().create({}, $scope.centroCosto);
      }
      response.$promise.then(
        function(response){
          if(response.success){
            $uibModalInstance.close(response.mensaje);
          }else{
            // error
            $scope.erroresDatos = response.errores;
            Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
          }
          $rootScope.cargando=false;
        }
      );
    };
    
});
