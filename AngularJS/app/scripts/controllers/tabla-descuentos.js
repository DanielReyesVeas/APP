'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:DescuentosCtrl
 * @description
 * # DescuentosCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('TablaDescuentosCtrl', function ($rootScope, $scope, mesDeTrabajo, $uibModal, $filter, tipoDescuento, $anchorScroll, constantes, Notification) {
    $anchorScroll();
    
    $scope.datos = [];
    $scope.constantes = constantes;    
    $scope.cargado = false;
    var tipos = [];

    $scope.open = function(){
      $rootScope.cargando = true;
      var datos = mesDeTrabajo.centralizar().get();
      datos.$promise.then(function(response){
        openForm(null, response);
        $rootScope.cargando = false;      
      });
    }

    function openForm(obj, cuentas) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nuevo-tipo-descuento.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormTipoDescuentoCtrl',
        resolve: {
          objeto: function () {
            return obj;
          },
          tipos: function () {
            return tipos;
          }
        }
      });
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        $scope.cargarDatos();
      }, function () {
        javascript:void(0)
      });
    };

    $scope.eliminar = function(objeto){
      $rootScope.cargando=true;
      $scope.result = tipoDescuento.datos().delete({ sid: objeto.sid });
      $scope.result.$promise.then( function(response){
        if(response.success){
          Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
          $scope.cargarDatos();
        }else{
          Notification.error({message: response.errores, title: 'Campo con Dependencias', delay: 10000});
          $rootScope.cargando = false;
        }
      });
    }

    $scope.editar = function(des){
      $rootScope.cargando=true;
      var datos = tipoDescuento.datos().get({sid:des.sid});
      datos.$promise.then(function(response){
        openForm( response.datos, response.cuentas);
        $rootScope.cargando=false;
      });
    };
    
    $scope.cargarDatos = function(){
      $rootScope.cargando = true;
      var datos = tipoDescuento.datos().get();
      datos.$promise.then(function(response){
        $scope.datos = response.datos;
        $scope.accesos = response.accesos;
        tipos = response.tipos;
        $rootScope.cargando = false;
        $scope.cargado = true;
      });
    };

    $scope.cargarDatos();

  })
  .controller('FormTipoDescuentoCtrl', function ($scope, tipos, $filter, $uibModal, $uibModalInstance, objeto, Notification, $rootScope, tipoDescuento, mesDeTrabajo) {

    $scope.tipos = angular.copy(tipos);

    if(objeto){
      $scope.tipoDescuento = angular.copy(objeto);
      $scope.tipoDescuento.tipo = $filter('filter')( $scope.tipos, {id :  $scope.tipoDescuento.tipo.id }, true )[0];
      $scope.titulo = 'Modificación Descuentos';
      $scope.encabezado = $scope.tipoDescuento.nombre;
    }else{
      $scope.tipoDescuento = { tipo : $scope.tipos[0] };
      $scope.titulo = 'Ingreso Descuentos';
      $scope.encabezado = 'Nuevo Descuento';
    }

    $scope.guardar = function(){
      $rootScope.cargando=true;
      var response;
      if( $scope.tipoDescuento.sid ){
        response = tipoDescuento.datos().update({sid:$scope.tipoDescuento.sid}, $scope.tipoDescuento);
      }else{
        response = tipoDescuento.datos().create({}, $scope.tipoDescuento);
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