'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:VacacionesCtrl
 * @description
 * # VacacionesCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('TrabajadoresVacacionesCtrl', function ($scope, $uibModal, $filter, $anchorScroll, trabajador, constantes, $rootScope, Notification, vacaciones) {
    
    $anchorScroll();
    $scope.objeto = [];
    $scope.isSelect = false;
    $scope.cargado = false;
    $scope.empresa = $rootScope.globals.currentUser.empresa;

    function cargarDatos(){
      $rootScope.cargando = true;
      $scope.cargado = false;
      var datos = trabajador.trabajadoresVacaciones().get();
      datos.$promise.then(function(response){
        $scope.datos = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando = false;
        $scope.cargado = true;
      });
    }

    $scope.detalle = function(obj){
      $rootScope.cargando=true;
      var datos = trabajador.vacaciones().get({sid: obj.sid});
      datos.$promise.then(function(response){
        openDetalleVacaciones( response );
        $rootScope.cargando=false;
      });
    }

    function openDetalleVacaciones(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-detalle-vacaciones.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormDetalleVacacionesCtrl',
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
        cargarDatos();      
      });
    };

    /*$scope.calcular = function(tra){
      $rootScope.cargando=true;
      var trabajador = { id : tra.id };
      var datos = vacaciones.calcular().post({}, trabajador);
      datos.$promise.then(function(response){
        $rootScope.cargando=false;
        cargarDatos();
      }); 
    }*/

    cargarDatos();

  })
  .controller('FormDetalleVacacionesCtrl', function ($rootScope, vacaciones, $uibModal, $filter, Notification, $scope, $uibModalInstance, objeto, licencia, trabajador) { 
    $scope.trabajador = angular.copy(objeto.datos);
    $scope.accesos = angular.copy(objeto.accesos);
    $scope.mes = angular.copy(objeto.datos.vacacionesMesActual);
    $scope.isEdit = false;
    $scope.isIngresar = false;
    $scope.vacaciones = { dias : null };

    function cargarDatos(tra){
      $rootScope.cargando=true;
      var datos = trabajador.licencias().get({sid: tra});
      datos.$promise.then(function(response){
        $scope.trabajador = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando=false;
      });
    };

    $scope.edit = function(){
      $scope.isEdit = !$scope.isEdit;
    };

    $scope.ingresarVacaciones = function(){
      $scope.isIngresar = !$scope.isIngresar;
      $scope.vacaciones.dias = null;
    }

    $scope.guardarVacaciones = function(){
      $scope.mes.dias = ($scope.mes.dias - $scope.vacaciones.dias);
      $scope.isIngresar = false;
      $scope.vacaciones.dias = null;
    }

    $scope.isEdited = function(){
      var bool = false;
      if($scope.mes.dias !== $scope.trabajador.vacacionesMesActual.dias){
        bool = true;
      }
      return bool;
    }

    $scope.guardar = function(){
      $rootScope.cargando=true;
      var vacas = { dias : $scope.mes.dias };
      var response = vacaciones.datos().update({sid:$scope.mes.sid}, vacas);
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
    }

  });
