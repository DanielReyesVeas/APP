'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:TablaImpuestoUnicoCtrlhttp://localhost:9000/#/inicio
 * @description
 * # TablaImpuestoUnicoCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('TablaImpuestoUnicoCtrl', function ($rootScope, $anchorScroll, $scope, tablaImpuestoUnico, moneda, Notification) {

    $anchorScroll();
    $scope.advertencia = "Recuerde que cualquier modificación en estos valores afecta directamente el pago de las cotizaciones previsionales del sistema. ¿Ud. se encuentra seguro y responsable de efectuar modificaciones?";
    $scope.confirmacion = "Los valores han sido modifcados. ¿Ud. se encuentra seguro y responsable cambiar los valores modificados?";  
    $scope.isEdit = false;
    $scope.isPesos = false;
    $scope.cargado = false;

    $scope.hide = function(s){
      console.log(s.keyCode)
    }

    $scope.inputDesde = [];
    $scope.inputHasta = [];
    $scope.inputFactor = [];
    $scope.inputCantidad = [];

    $scope.convertirUTM = function(valor){
      return moneda.convertirUTM(valor);
    }

    function cargarDatos(){
      $rootScope.cargando=true;
      $scope.cargado=true;
      var datos = tablaImpuestoUnico.datos().get();
      datos.$promise.then(function(response){        
        $scope.tabla = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando=false;
        $scope.cargado=true;
        crearModels();
        $scope.isEdit = false;
      })
    };

    cargarDatos();

    /*$scope.isFila = function(){
      if($scope.tabla){
        var bool = true;
        for(var i=0,len=$scope.tabla.length; i<len; i++){
          if(!$scope.inputTramo[i] && !$scope.inputDesde[i] && !$scope.inputHasta[i] && !$scope.inputFactor[i] && !$scope.inputCantidad[i]){
            bool = false;
          }
        }
        return bool;
      }
    }     */

    function crearModels(){
      for(var i=0, len=$scope.tabla.length; i<len; i++){
        $scope.inputDesde[i] = $scope.tabla[i].imponibleMensualDesde;
        $scope.inputHasta[i] = $scope.tabla[i].imponibleMensualHasta;
        $scope.inputFactor[i] = $scope.tabla[i].factor;
        $scope.inputCantidad[i] = $scope.tabla[i].cantidadARebajar;
      }
    }    

    $scope.editar = function(){
      $scope.isEdit = true;      
    }

    $scope.cancelar = function(){
      $scope.isEdit = false;   
      crearModels();   
    }

    /*$scope.agregarFila = function(){
      if($scope.isFila()){
        $scope.tabla.push({});
      }
    }*/

    $scope.guardar = function(){
      /*if(!$scope.isFila()){
        $scope.tabla.pop();
      }*/
      var tabla = [];
      for(var i=0, len=$scope.tabla.length; i<len; i++){
        tabla.push({ id : $scope.tabla[i].id, imponibleMensualDesde : $scope.inputDesde[i], imponibleMensualHasta : $scope.inputHasta[i], factor : $scope.inputFactor[i], cantidadARebajar : $scope.inputCantidad[i] });
      }
      
      $rootScope.cargando=true;
      var response;      
      response = tablaImpuestoUnico.modificar().post({}, tabla);

      response.$promise.then(
        function(response){
          if(response.success){
            Notification.success({message: response.mensaje, title: 'Mensaje del Sistema'});
            cargarDatos();
          }else{
            // error
            $scope.erroresDatos = response.errores;
            Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
          }
        }
      )      
    }

    $scope.cambiarPesos = function(){
      if($scope.isPesos){
        $scope.isPesos = false;
      }else{
        $scope.isPesos = true;        
      }
    }

    /*$scope.rentaImponible1Tramo1 = 0;
    $scope.rentaImponible2Tramo1 = 13.5;
    $scope.factorTramo1 = 0;
    $scope.cantidadTramo1 = 0;

    $scope.rentaImponible1Tramo2 = 13.5;
    $scope.rentaImponible2Tramo2 = 30;
    $scope.factorTramo2 = 4;
    $scope.cantidadTramo2 = 0.54;

    $scope.rentaImponible1Tramo3 = 30;
    $scope.rentaImponible2Tramo3 = 50;
    $scope.factorTramo3 = 8;
    $scope.cantidadTramo3 = 1.74;

    $scope.rentaImponible1Tramo4 = 50;
    $scope.rentaImponible2Tramo4 = 70;
    $scope.factorTramo4 = 13.5;
    $scope.cantidadTramo4 = 4.49;

    $scope.rentaImponible1Tramo5 = 70;
    $scope.rentaImponible2Tramo5 = 90;
    $scope.factorTramo5 = 23;
    $scope.cantidadTramo5 = 11.14;

    $scope.rentaImponible1Tramo6 = 90;
    $scope.rentaImponible2Tramo6 = 120;
    $scope.factorTramo6 = 30.4;
    $scope.cantidadTramo6 = 17.80;

    $scope.rentaImponible1Tramo7 = 120;
    $scope.rentaImponible2Tramo7 = 'Y más';
    $scope.factorTramo7 = 35;
    $scope.cantidadTramo7 = 23.32;*/

    /*$scope.isAll = function(){
      $scope.isTramo1 = false;
      $scope.isTramo2 = false;
      $scope.isTramo3 = false;
      $scope.isTramo4 = false;
      $scope.isTramo5 = false;
      $scope.isTramo6 = false;
      $scope.isTramo7 = false;
    }

    $scope.editarTramo1 = function(){
      if($scope.isTramo1){
        $scope.rentaImponible1Tramo1 = $scope.tramo.rentaImponible1Tramo1;
        $scope.rentaImponible2Tramo1 = $scope.tramo.rentaImponible2Tramo1;
        $scope.factorTramo1 = $scope.tramo.factorTramo1;
        $scope.cantidadTramo1 = $scope.tramo.cantidadTramo1;        
        $scope.isTramo1 = false;
      }else{
        $scope.tramo.rentaImponible1Tramo1 = $scope.rentaImponible1Tramo1;
        $scope.tramo.rentaImponible2Tramo1 = $scope.rentaImponible2Tramo1;
        $scope.tramo.factorTramo1 = $scope.factorTramo1;
        $scope.tramo.cantidadTramo1 = $scope.cantidadTramo1;
        $scope.isAll();
        $scope.isTramo1 = true;
      }
    }

    $scope.editarTramo2 = function(){
      if($scope.isTramo2){
        $scope.rentaImponible1Tramo2 = $scope.tramo.rentaImponible1Tramo2;
        $scope.rentaImponible2Tramo2 = $scope.tramo.rentaImponible2Tramo2;
        $scope.factorTramo2 = $scope.tramo.factorTramo2;
        $scope.cantidadTramo2 = $scope.tramo.cantidadTramo2;
        $scope.isTramo2 = false;
      }else{
        $scope.tramo.rentaImponible1Tramo2 = $scope.rentaImponible1Tramo2;
        $scope.tramo.rentaImponible2Tramo2 = $scope.rentaImponible2Tramo2;
        $scope.tramo.factorTramo2 = $scope.factorTramo2;
        $scope.tramo.cantidadTramo2 = $scope.cantidadTramo2;
        $scope.isAll();
        $scope.isTramo2 = true;
      }
    }

    $scope.editarTramo3 = function(){
      if($scope.isTramo3){
        $scope.rentaImponible1Tramo3 = $scope.tramo.rentaImponible1Tramo3;
        $scope.rentaImponible2Tramo3 = $scope.tramo.rentaImponible2Tramo3;
        $scope.factorTramo3 = $scope.tramo.factorTramo3;
        $scope.cantidadTramo3 = $scope.tramo.cantidadTramo3;
        $scope.isTramo3 = false;
      }else{
        $scope.tramo.rentaImponible1Tramo3 = $scope.rentaImponible1Tramo3;
        $scope.tramo.rentaImponible2Tramo3 = $scope.rentaImponible2Tramo3;
        $scope.tramo.factorTramo3 = $scope.factorTramo3;
        $scope.tramo.cantidadTramo3 = $scope.cantidadTramo3;
        $scope.isAll();
        $scope.isTramo3 = true;
      }
    }

    $scope.editarTramo4 = function(){
      if($scope.isTramo4){
        $scope.rentaImponible1Tramo4 = $scope.tramo.rentaImponible1Tramo4;
        $scope.rentaImponible2Tramo4 = $scope.tramo.rentaImponible2Tramo4;
        $scope.factorTramo4 = $scope.tramo.factorTramo4;
        $scope.cantidadTramo4 = $scope.tramo.cantidadTramo4;
        $scope.isTramo4 = false;
      }else{
        $scope.tramo.rentaImponible1Tramo4 = $scope.rentaImponible1Tramo4;
        $scope.tramo.rentaImponible2Tramo4 = $scope.rentaImponible2Tramo4;
        $scope.tramo.factorTramo4 = $scope.factorTramo4;
        $scope.tramo.cantidadTramo4 = $scope.cantidadTramo4;
        $scope.isAll();
        $scope.isTramo4 = true;
      }
    }

    $scope.editarTramo5 = function(){
      if($scope.isTramo5){
        $scope.rentaImponible1Tramo5 = $scope.tramo.rentaImponible1Tramo5;
        $scope.rentaImponible2Tramo5 = $scope.tramo.rentaImponible2Tramo5;
        $scope.factorTramo5 = $scope.tramo.factorTramo5;
        $scope.cantidadTramo5 = $scope.tramo.cantidadTramo5;
        $scope.isTramo5 = false;
      }else{
        $scope.tramo.rentaImponible1Tramo5 = $scope.rentaImponible1Tramo5;
        $scope.tramo.rentaImponible2Tramo5 = $scope.rentaImponible2Tramo5;
        $scope.tramo.factorTramo5 = $scope.factorTramo5;
        $scope.tramo.cantidadTramo5 = $scope.cantidadTramo5;
        $scope.isAll();
        $scope.isTramo5 = true;
      }
    }

    $scope.editarTramo6 = function(){
      if($scope.isTramo6){
        $scope.rentaImponible1Tramo6 = $scope.tramo.rentaImponible1Tramo6;
        $scope.rentaImponible2Tramo6 = $scope.tramo.rentaImponible2Tramo6;
        $scope.factorTramo6 = $scope.tramo.factorTramo6;
        $scope.cantidadTramo6 = $scope.tramo.cantidadTramo6;
        $scope.isTramo6 = false;
      }else{
        $scope.tramo.rentaImponible1Tramo6 = $scope.rentaImponible1Tramo6;
        $scope.tramo.rentaImponible2Tramo6 = $scope.rentaImponible2Tramo6;
        $scope.tramo.factorTramo6 = $scope.factorTramo6;
        $scope.tramo.cantidadTramo6 = $scope.cantidadTramo6;
        $scope.isAll();
        $scope.isTramo6 = true;
      }
    }

    $scope.editarTramo7 = function(){
      if($scope.isTramo7){
        $scope.rentaImponible1Tramo7 = $scope.tramo.rentaImponible1Tramo7;
        $scope.rentaImponible2Tramo7 = $scope.tramo.rentaImponible2Tramo7;
        $scope.factorTramo7 = $scope.tramo.factorTramo7;
        $scope.cantidadTramo7 = $scope.tramo.cantidadTramo7;
        $scope.isTramo7 = false;
      }else{
        $scope.tramo.rentaImponible1Tramo7 = $scope.rentaImponible1Tramo7;
        $scope.tramo.rentaImponible2Tramo7 = $scope.rentaImponible2Tramo7;
        $scope.tramo.factorTramo7 = $scope.factorTramo7;
        $scope.tramo.cantidadTramo7 = $scope.cantidadTramo7;
        $scope.isAll();
        $scope.isTramo7 = true;
      }
    }*/
  });
