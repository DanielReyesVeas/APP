'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:LiquidacionesDeSueldoCtrl
 * @description
 * # LiquidacionesDeSueldoCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('LiquidacionesDeSueldoCtrl', function ($scope, constantes, $uibModal, $filter, $anchorScroll, trabajador, $rootScope, Notification, liquidacion) {
    
    $anchorScroll();
    $scope.objeto = [];
    $scope.isSelect = [ false, false ];
    $scope.cargado = false;
    $scope.constantes = constantes;
    $scope.mensaje = [ "", "", ""];

    if($rootScope.globals.currentUser.empresa){
      $scope.uf = $rootScope.globals.indicadores.uf.valor;
      $scope.utm = $rootScope.globals.indicadores.utm.valor;
      $scope.empresa = $rootScope.globals.currentUser.empresa;
    }
    function cargarDatos(){
      $rootScope.cargando = true;
      var datos = trabajador.trabajadoresLiquidaciones().get();
      datos.$promise.then(function(response){
        $scope.accesos = response.accesos;
        $scope.trabajadores = [ response.sinLiquidacion, response.conLiquidacion ];
        $rootScope.cargando = false;
        crearModels();
        limpiarChecks();
      });
    }

    cargarDatos();

    function generarLiquidacion(trabajadores){
      $rootScope.cargando = true;
      var datos = trabajador.liquidacion().post({}, trabajadores);
      datos.$promise.then(function(response){
        if(response.success){
          Notification.success({message: response.mensaje, title: 'Mensaje del Sistema'});
          cargarDatos();
          recibirLiquidaciones(response.datos);
        $rootScope.cargando = false;
        }else{
          Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
          $rootScope.cargando = false;
        }
      });
    }

    /*$scope.generar = function(trabajador){
      $rootScope.cargando = true;
      var url = $scope.constantes.URL + 'trabajadores/miLiquidacion/generar/' + trabajador.sid;
      $rootScope.cargando = false;
      openFrame(url);
    }

    function openFrame(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-frame.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormFrameCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });
     miModal.result.then(function (datos) {
        ingresar(datos);              
      }, function () {
        limpiarChecks();
      });
    }*/

    function recibirLiquidaciones(datos){
      if(datos.length>1){
        openLiquidaciones(datos);
      }else{
        open(datos[0]);
      }
      //window.open(url);
      /*if(bool){
        openLiquidaciones(datos);
      }else{
        openLiquidacion(datos[0], bool);
      }*/
    }    

    function ingresar(datos){   
        
      for(var i=0,len=datos.length; i<len; i++){
        datos[i].uf = $scope.uf;
        datos[i].utm = $scope.utm;
        datos[i].empresa = $scope.empresa;
        datos[i].cuerpo = $('#htmlLiquidacion').html();
      }


      $rootScope.cargando=true;
      var response;
      response = liquidacion.datos().create({}, datos);
      
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
          $rootScope.cargando=false;
        }
      )
    }

    function crearModels(){
      for(var i=0, len=$scope.trabajadores[0].length; i<len; i++){
        $scope.trabajadores[0][i].check = false;
      }         
      for(var i=0, len=$scope.trabajadores[1].length; i<len; i++){
        $scope.trabajadores[1][i].check = false;
      }         
      $scope.cargado = true;
    }

    $scope.select = function(index, ind){
      if(!$scope.trabajadores[ind][index].check){
        if($scope.objeto.todos[ind]){
          $scope.objeto.todos[ind] = false; 
        }
        countSelected(ind);
        $scope.isSelect[ind] = isThereSelected(ind);       
      }else{
        $scope.isSelect[ind] = true;
        countSelected(ind);
      }
    }

    function isThereSelected(index){
      var bool = false;
      for(var i=0, len=$scope.trabajadores[index].length; i<len; i++){
        if($scope.trabajadores[index][i].check){
          bool = true;
          return bool;
        }
      }
      return bool;
    }

    function countSelected(index){
      var count = 0;
      var nom;
      for(var i=0, len=$scope.trabajadores[index].length; i<len; i++){
        if($scope.trabajadores[index][i].check){
          nom = $scope.trabajadores[index][i].nombreCompleto;
          count++;
          $scope.mensaje[0] = 'Se generarán las Liquidaciones de Sueldo de los <b>' + count + '</b> trabajadores seleccionados.';
          $scope.mensaje[1] = 'Se sobreescribirán las Liquidaciones de Sueldo de los <b>' + count + '</b> trabajadores seleccionados.';
          $scope.mensaje[2] = 'Se eliminarán las Liquidaciones de Sueldo de los <b>' + count + '</b> trabajadores seleccionados.';
        }
      }
      if(count===1){
        count = nom;
        $scope.mensaje[0] = 'Se generará una nueva Liquidación de Sueldo de <b>' + count + '</b>, sobreescribiendo la anterior.';
        $scope.mensaje[1] = 'Se eliminará la Liquidación de Sueldo de <b>' + count + '</b>.';
      }
      return count;
    }

    $scope.selectAll = function(index, check){
      if(check){
        var total = 0;
        for(var i=0, len=$scope.trabajadores[index].length; i<len; i++){
          $scope.trabajadores[index][i].check = true
          $scope.isSelect[index] = true;
          total++;  
        }
        countSelected(index);
      }else{
        for(var i=0, len=$scope.trabajadores[index].length; i<len; i++){
          $scope.trabajadores[index][i].check = false
          $scope.isSelect[index] = false;
        }
      }
    }

    /*$scope.generarAll = function(){
      var liquidaciones = { trabajadores : [] };

      for(var i=0,len=$scope.objeto.length; i<len; i++){
        if($scope.objeto[i].check){
          liquidaciones.trabajadores.push({ sid : $scope.sinLiquidacion[i].sid});
        }
      }
      generarLiquidacion(liquidaciones, true);
    }*/

    $scope.generar = function(sid, multi, update){
      var index = 0;
      if(update){
        index = 1;
      }
      var liquidaciones = { trabajadores : [], comprobar : update };
      if(multi){
        for(var i=0,len=$scope.trabajadores[index].length; i<len; i++){
          if($scope.trabajadores[index][i].check){
            liquidaciones.trabajadores.push({ sid : $scope.trabajadores[index][i].sidTrabajador});
          }
        }
      }else{
        liquidaciones.trabajadores.push({ sid : sid });        
      }
      generarLiquidacion(liquidaciones);
    }

    function limpiarChecks(){
      for(var i=0, len=$scope.trabajadores[0].length; i<len; i++){
        $scope.trabajadores[0][i].check = false
      }
      for(var i=0, len=$scope.trabajadores[1].length; i<len; i++){
        $scope.trabajadores[1][i].check = false
      }
      $scope.isSelect[0] = false;
      $scope.isSelect[1] = false;
      $scope.objeto.todos = [ false, false ];
    }

    function openLiquidaciones(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-liquidaciones.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormLiquidacionesCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });
     miModal.result.then(function (datos) {
        ingresar(datos);              
      }, function () {
        limpiarChecks();
      });
    };

    function openLiquidacion(obj){
      $rootScope.cargando = true;
      /*$scope.url = constantes.URL + 'trabajadores/liquidacion/descargar-pdf/' + $scope.objeto.sid;
      window.open(url);*/
      open(obj);
    }
    /*function openLiquidacion(obj, bool){
      var miModal = $uibModal.open({
        animation: true,
        backdrop: false,
        templateUrl: 'views/forms/form-liquidacion.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormLiquidacionCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;          
          },
          isIngresar: function () {
            return bool;          
          }
        }
      });
     miModal.result.then(function (datos) {
        ingresar(datos);            
      }, function () {
        limpiarChecks();
      });
    };   */ 
    function open(obj){
      var miModal = $uibModal.open({
        animation: true,
        backdrop: false,
        templateUrl: 'views/forms/form-mi-liquidacion.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormFrameCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });
     miModal.result.then(function (datos) {
        ingresar(datos);            
      }, function () {
        limpiarChecks();
      });
    }

    $scope.detalle = function(liq, nuevaVentana){
      $rootScope.cargando=true;
      if(nuevaVentana){
        var url = constantes.URL + 'trabajadores/liquidacion/descargar-pdf/' + liq.sidDocumento;
        window.open(url);
        $rootScope.cargando = false;
      }else{
        open(liq);
      }
    }

    $scope.eliminar = function(liq, multi){
      $rootScope.cargando=true;
      if(multi){
        var liquidaciones = { trabajadores : [] };
        for(var i=0,len=$scope.trabajadores[1].length; i<len; i++){
          if($scope.trabajadores[1][i].check){
            liquidaciones.trabajadores.push({ sid : $scope.trabajadores[1][i].sidDocumento });
          }
        }
        $scope.result = liquidacion.eliminarMasivo().post({}, liquidaciones );  
      }else{     
        $scope.result = liquidacion.datos().delete({sid:liq.sidDocumento});  
      }      
      $scope.result.$promise.then( function(response){
        if(response.success){
          Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
          cargarDatos();
        }
      });
    }

  })
  .controller('FormFrameCtrl', function ($scope, $sce, $uibModal, constantes, $filter, $uibModalInstance, objeto, Notification, $rootScope) {
    $scope.objeto = angular.copy(objeto);

    $scope.url = constantes.URL + 'trabajadores/liquidacion/descargar-pdf/' + $scope.objeto.sidDocumento;
    $scope.cargado = false;
    $rootScope.cargando = false;
    $scope.cargado = true;

    if($scope.objeto.liquidacion){
      $scope.trabajador = $scope.objeto.liquidacion;
    }else{
      $scope.trabajador = $scope.objeto;
    }

    $scope.trustSrc = function(src){
      return $sce.trustAsResourceUrl(src);
    }

    $scope.iframeLoadedCallBack = function(){
      $scope.cargado = true;
    }

  })
  .controller('FormLiquidacionesCtrl', function ($scope, $uibModal, $filter, $uibModalInstance, objeto, Notification, $rootScope) {
    $scope.datos = angular.copy(objeto);

    $scope.openLiquidacion = function(obj){
      var miModal = $uibModal.open({
        animation: true,
        backdrop: false,
        templateUrl: 'views/forms/form-mi-liquidacion.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormFrameCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;          
          }
        }
      });
      miModal.result.then(function (next) {                

      }, function () {
        javascript:void(0);
      });
    };
    
    $scope.ingresar = function(){
      $uibModalInstance.close($scope.datos);
    }

    $scope.eliminarLiquidacion = function(liquidacion){
      for(var i=0, len=$scope.datos.length; i<len; i++){
        if($scope.datos[i].id === liquidacion.id){
          $scope.datos.splice(i,1);
          break;        
        }
      };
    }

  })
  .controller('FormLiquidacionCtrl', function ($scope, $uibModalInstance, objeto, Notification, $rootScope, isIngresar, utilities, fecha) {

    $scope.empresa = $rootScope.globals.currentUser.empresa;
    $scope.trabajador = angular.copy(objeto);
    $scope.isIngresar = isIngresar;
    var mesActual = fecha.convertirFecha($scope.empresa.mesDeTrabajo.mes);
    $scope.anioActual = mesActual.getFullYear();
    $scope.mesActual = fecha.obtenerMesTexto(mesActual.getMonth());

    $scope.next = function(){
      $uibModalInstance.close(true);
    }

    $scope.convertirPalabras = function(valor){
      return utilities.convertirPalabras(valor);
    }       

    $scope.totalImponibles = function(){
      var total = 0;
      for(var i=0,len=$scope.haberes.length; i<len; i++){
        if($scope.haberes[i].tipo.imponible){
          total += $scope.haberes[i].monto;
        }
      }
      return total;
    }

    $scope.totalNoImponibles = function(){
      var total = 0;
      for(var i=0,len=$scope.haberes.length; i<len; i++){
        if(!$scope.haberes[i].tipo.imponible){
          total += $scope.haberes[i].monto;
        }
      }
      return total;
    }

    $scope.ingresar = function(){
      var array = [];
      array.push($scope.trabajador);
      $uibModalInstance.close(array);
    }

    $scope.totalDescuentos = function(){
      var total = 0;
      for(var i=0,len=$scope.descuentos.length; i<len; i++){
        total += $scope.descuentos[i].monto;
      }
      return total;
    }

  });
