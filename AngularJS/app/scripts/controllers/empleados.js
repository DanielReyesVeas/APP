'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:EmpleadosCtrl
 * @description
 * # EmpleadosCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('EmpleadosCtrl', function ($scope, $sce, $filter, $rootScope, $uibModal, Notification, constantes, funcionario) {
    
    $scope.datos = [];
    $scope.constantes = constantes;
    $scope.opciones = {};

    function open(obj, opc, isActivar) {
      var modalInstance = $uibModal.open({
        animation: $scope.animationsEnabled,
        templateUrl: 'views/forms/form-empleado.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'EmpeladosCtrl',
        size: '800',
        backdrop: 'static',
        resolve: {
          objeto : function(){
            return obj;
          },
          opciones: function () {
            return opc;
          },
          isActivar: function () {
            return isActivar;
          }
        }
      });
      modalInstance.result.then(function (obj) {
        Notification.success({message: obj.mensaje, title: 'Mensaje del Sistema'});
        $scope.cargarDatos();
        $scope.cargarOpciones();
      }, function () {
        var user = $filter('filter')( $scope.datos, {id :  obj.id }, true )[0];
        user.activo = false;
      });
    };

    $scope.cargarDatos = function(){
      $rootScope.cargando=true;
      var datosFunc = funcionario.empleados().get();
      datosFunc.$promise.then(function(response){
        $scope.datos = response.datos;
        $rootScope.cargando=false;
      });
    };

    $scope.cargarOpciones = function(){
      $rootScope.cargando=true;
      var datosOpc = funcionario.opciones().get();
      datosOpc.$promise.then(function(response){
        $scope.opciones = response;
        $rootScope.cargando=false;
      });
    };

    $scope.activar = function(user){
      if(user.activo){
        $rootScope.cargando=true;
        var datos = funcionario.usuario().get({sid:user.sid});
        datos.$promise.then(function(response){
          open( response.datos, response.opciones, true );
          $rootScope.cargando=false;
        });
      }
    }

    $scope.editar = function(user){
      $rootScope.cargando=true;
      var datos = funcionario.usuario().get({sid:user.sid});
      datos.$promise.then(function(response){
        open( response.datos, response.opciones, false );
        $rootScope.cargando=false;
      });
    };

    $scope.toolTipEdicion = function( nombre ){
      return 'Editar los datos del trabajador <b>' + nombre + '</b>';
    };

    $scope.cargarDatos();
    $scope.cargarOpciones();
  })

  .controller('EmpeladosCtrl', function ($scope, $rootScope, $timeout, isActivar, $uibModal, $uibModalInstance, Notification, $filter, funcionario, objeto, opciones, constantes ) {
    
    $scope.usuario = angular.copy(objeto);
    $scope.select = { all : false };
    $scope.isActivar = isActivar;
    crearModels();

    function crearModels(){
      for(var i=0,len=$scope.usuario.accesos.length; i<len; i++){
        if(!$scope.usuario.accesos[i].check){
          $scope.select.all = false;
          return;
        }
      }
      $scope.select.all = true;
    }

    $scope.seleccionarOpcion = function(opc){
      for(var i=0,len=$scope.usuario.accesos.length; i<len; i++){
        if(!$scope.usuario.accesos[i].check){
          $scope.select.all = false;
          return;
        }
      }
      $scope.select.all = true;
    }

    $scope.selectAll = function(){
      for(var i=0,len=$scope.usuario.accesos.length; i<len; i++){
        $scope.usuario.accesos[i].check = $scope.select.all;
      }
    }

    $scope.close = function(){
      $uibModalInstance.dismiss($scope.objeto);
    }

    $scope.guardar = function () {
      if($scope.isActivar){
        $scope.usuario.activo = true;
      }
      $rootScope.cargando=true;
      var response;
      response = funcionario.cambiarPermisos().post($scope.usuario);
      response.$promise.then(
        function(response){
          if(response.success){
            $uibModalInstance.close({ mensaje : response.mensaje});
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
