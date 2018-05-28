'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:AtrasosCtrl
 * @description
 * # AtrasosCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('AtrasosCtrl', function ($scope, $uibModal, $filter, $anchorScroll, trabajador, constantes, $rootScope, Notification) {
    $anchorScroll();
    
    $scope.datos = [];
    $scope.constantes = constantes;
    $scope.cargado = false;

    function cargarDatos(){
      $rootScope.cargando=true;
      var datos = trabajador.totalAtrasos().get();
      datos.$promise.then(function(response){
        $scope.datos = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando=false;
        $scope.cargado = true;
      });
    };

    cargarDatos();

    $scope.openAtraso = function(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nuevo-atraso.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormAtrasoCtrl',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });
      miModal.result.then(function (object) {
        Notification.success({message: object.mensaje, title: 'Mensaje del Sistema'});
        cargarDatos();         
      }, function () {
        javascript:void(0)
      });
    };

    $scope.openDetalleAtrasos = function(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-detalle-atrasos.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormDetalleAtrasosCtrl',
        size: 'lg',
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

    $scope.detalle = function(sid){
      $rootScope.cargando=true;
      var datos = trabajador.atrasos().get({sid: sid});
      datos.$promise.then(function(response){
        $scope.openDetalleAtrasos( response );
        $rootScope.cargando=false;
      });
    };

  })
  .controller('FormDetalleAtrasosCtrl', function ($rootScope, $uibModal, $filter, Notification, $scope, $uibModalInstance, objeto, atraso, trabajador) { 
    
    $scope.trabajador = angular.copy(objeto.datos);
    $scope.accesos = angular.copy(objeto.accesos);

    function cargarDatos(tra){
      $rootScope.cargando=true;
      var datos = trabajador.atrasos().get({sid: tra});
      datos.$promise.then(function(response){
        $scope.trabajador = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando=false;
      });
    };

    $scope.editar = function(ina, tra){
      $rootScope.cargando=true;
      var datos = atraso.datos().get({sid: ina.sid});
      datos.$promise.then(function(response){
        $scope.openAtraso( response );
        $rootScope.cargando=false;
      });
    };

    $scope.eliminar = function(ina, tra){
      $rootScope.cargando=true;
      $scope.result = atraso.datos().delete({ sid: ina.sid });
      $scope.result.$promise.then( function(response){
        if(response.success){
          Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
          cargarDatos(tra);
        }
      });
    }

    $scope.openAtraso = function(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nuevo-atraso.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormAtrasoCtrl',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });
      miModal.result.then(function (object) {
        Notification.success({message: object.mensaje, title: 'Mensaje del Sistema'});
        cargarDatos(object.sidTrabajador);         
      }, function () {
        javascript:void(0)
      });
    };

  })
  .controller('FormAtrasoCtrl', function ($rootScope, Notification, $scope, $uibModalInstance, objeto, atraso, fecha) {

    var mesActual = $rootScope.globals.currentUser.empresa.mesDeTrabajo;
    
    if(objeto.trabajador){
      $scope.trabajador = angular.copy(objeto.trabajador);
      var hora = new Date(objeto.fecha);
      hora.setHours(objeto.horas, objeto.minutos)
      $scope.atraso = { id : objeto.id, sid : objeto.sid, fecha : fecha.convertirFecha(objeto.fecha), hora : hora, observacion : objeto.observacion };
      $scope.isEdit = true;
      $scope.titulo = 'Modificación Atraso';
    }else{
      $scope.trabajador = angular.copy(objeto);
      $scope.isEdit = false;
      $scope.titulo = 'Ingreso Atraso';
      $scope.atraso = { fecha : fecha.fechaActiva(), hora : fecha.fechaActiva(), observacion : null };
    }

    $scope.guardar = function(atr, trabajador){
      $rootScope.cargando=true;
      var response;
      var Atraso = { idTrabajador : trabajador.id, fecha : fecha.convertirFechaFormato(atr.fecha), horas : atr.hora.getHours(), minutos : atr.hora.getMinutes(), observacion : atr.observacion };
      console.log(Atraso)
      if( $scope.atraso.sid ){
        response = atraso.datos().update({sid:$scope.atraso.sid}, Atraso);
      }else{
        response = atraso.datos().create({}, Atraso);
      }
      response.$promise.then(
        function(response){
          if(response.success){
            $uibModalInstance.close({ mensaje : response.mensaje, sidTrabajador : trabajador.sid });
          }else{
            // error
            $scope.erroresDatos = response.errores;
            Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
          }
          $rootScope.cargando=false;
        }
      );
    }
    
    // Fecha

    $scope.dateOptions = {
      formatYear: 'yy',
      maxDate: fecha.convertirFecha(mesActual.fechaRemuneracion),
      minDate: fecha.convertirFecha(mesActual.mes),
      startingDay: 1
    };  

    $scope.openFecha = function() {
      $scope.popupFecha.opened = true;
    };

    $scope.format = ['dd-MMMM-yyyy'];

    $scope.popupFecha = {
      opened: false
    };

  });
