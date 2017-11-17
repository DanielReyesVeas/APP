'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:ArchivoPreviredCtrl
 * @description
 * # ArchivoPreviredCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('ArchivoPreviredCtrl', function ($scope, $uibModal, $filter, $anchorScroll, trabajador, constantes, $rootScope, Notification) {
    $anchorScroll();
    $scope.datos = [];
    $scope.constantes = constantes;
    $scope.cargado = false;

    function isAllLiquidaciones(){
      var bool = true;
      for(var i=0,len=$scope.datos.length; i<len; i++){
        if(!$scope.datos[i].isLiquidacion){
          bool = false;
          break;
        }
      }

      $scope.isGenerar = bool;
    }

    function cargarDatos(){
      $rootScope.cargando = true;
      var datos = trabajador.previred().get();
      datos.$promise.then(function(response){
        $scope.datos = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando = false;      
        $scope.cargado = true;  
        isAllLiquidaciones();
      });
    };

    cargarDatos();

    $scope.generarArchivo = function(){
      $rootScope.cargando=true;
      var url = $scope.constantes.URL + 'trabajadores/archivo-previred/descargar-excel';
      window.open(url, "_self");
      $rootScope.cargando=false;
    }

  });