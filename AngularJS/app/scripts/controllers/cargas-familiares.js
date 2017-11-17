'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:CargasFamiliaresCtrl
 * @description
 * # CargasFamiliaresCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('CargasFamiliaresCtrl', function ($scope, $uibModal, $filter, $anchorScroll, trabajador, constantes, $rootScope, Notification) {
    $anchorScroll();

    $scope.datos = [];
    $scope.constantes = constantes;
    $scope.cargado = false;
    var tiposCargas;

    function cargarDatos(){
      $rootScope.cargando=true;
      $scope.cargado = false;
      var datos = trabajador.totalCargas().get();
      datos.$promise.then(function(response){
        $scope.datos = response.datos;
        tiposCargas = response.tiposCargas;
        $scope.accesos = response.accesos;
        $rootScope.cargando=false;
        $scope.cargado = true;
      });
    };

    cargarDatos();

    $scope.openCarga = function(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nueva-carga.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormCargasCtrl',
        resolve: {
          objeto: function () {
            return obj;          
          },
          tiposCargas: function () {
            return tiposCargas;          
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

    $scope.openDetalleCargas = function(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-detalle-cargas.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormDetalleCargasCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;          
          },
          tiposCargas: function () {
            return tiposCargas;          
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
      var datos = trabajador.cargas().get({sid: sid});
      datos.$promise.then(function(response){
        $scope.openDetalleCargas( response );
        $rootScope.cargando=false;
      });
    };

  })
  .controller('FormDetalleCargasCtrl', function ($rootScope, $uibModal, $filter, Notification, $scope, $uibModalInstance, objeto, tiposCargas, carga, trabajador) { 
    $scope.trabajador = angular.copy(objeto.datos);
    $scope.accesos = angular.copy(objeto.accesos);
    var tiposCargas = angular.copy(tiposCargas);

    function cargarDatos(tra){
      $rootScope.cargando=true;
      var datos = trabajador.cargas().get({sid: tra});
      datos.$promise.then(function(response){
        $scope.trabajador = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando=false;
      });
    };

    $scope.editar = function(car, tra){
      $rootScope.cargando=true;
      var datos = carga.datos().get({sid: car.sid});
      datos.$promise.then(function(response){
        $scope.openCarga( response );
        $rootScope.cargando=false;
      });
    };

    $scope.eliminar = function(car, tra){
      $rootScope.cargando=true;
      $scope.result = carga.datos().delete({ sid: car.sid });
      $scope.result.$promise.then( function(response){
        if(response.success){
          Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
          cargarDatos(tra);
        }
        $rootScope.cargando=false;
      });
    }

    $scope.openCarga = function(obj){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nueva-carga.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormCargasCtrl',
        resolve: {
          objeto: function () {
            return obj;          
          },
          tiposCargas: function () {
            return tiposCargas;          
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

    $scope.autorizar = function(isEdit){
      $rootScope.cargando=true;
      var datos = trabajador.cargasAutorizar().get({sid: $scope.trabajador.sid});
      datos.$promise.then(function(response){
        openAutorizar( response, isEdit );
        $rootScope.cargando=false;
      });
    }

    function openAutorizar(obj, isEdit){
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-autorizar-cargas.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormAutorizarCargasCtrl',
        resolve: {
          objeto: function () {
            return obj;          
          },
          isEdit: function () {
            return isEdit;          
          }
        }
      });
      miModal.result.then(function (object) {
        Notification.success({message: object.mensaje, title: 'Mensaje del Sistema'});
        cargarDatos(object.sid);                 
      }, function () {
        javascript:void(0)
      });
    }

  })
  .controller('FormAutorizarCargasCtrl', function ($rootScope, Notification, $scope, $uibModalInstance, objeto, isEdit, trabajador, carga, fecha, $filter) {

    $scope.trabajador = angular.copy(objeto.datos);
    $scope.tramos = angular.copy(objeto.tramos);
    $scope.isSelect = false;
    $scope.carga = {};
    $scope.isEdit = isEdit;

    crearModels();

    function crearModels(){
      $scope.objeto = [];
      for(var i=0, len=$scope.trabajador.cargas.length; i<len; i++){
        var esAutorizada = false;
        var date = new Date();
        if($scope.trabajador.cargas[i].esAutorizada){
          date = fecha.convertirFecha($scope.trabajador.cargas[i].fechaAutorizacion);
          esAutorizada = true;
          $scope.isSelect = true;
          $scope.carga.tramo = $filter('filter')( $scope.tramos, {id :  $scope.trabajador.tramo.id }, true )[0];
        }
        $scope.objeto.push({ check : esAutorizada, fecha : date, popupFecha : { opened : false } });
      }         
      var contador = countSelected();
      if($scope.trabajador.cargas.length===contador){
        $scope.objeto.todos = true;
      }
      $scope.cargado = true;
    }

    $scope.select = function(index){
      if(!$scope.objeto[index].check){
        if($scope.objeto.todos){
          $scope.objeto.todos = false; 
        }
        countSelected();
        $scope.isSelect = isThereSelected();       
      }else{
        $scope.isSelect = true;
        countSelected();
      }
    }

    function isThereSelected(){
      var bool = false;
      for(var i=0, len=$scope.trabajador.cargas.length; i<len; i++){
        if($scope.objeto[i].check){
          bool = true;
          return bool;
        }
      }
      return bool;
    }

    function countSelected(){
      var count = 0;
      for(var i=0, len=$scope.trabajador.cargas.length; i<len; i++){
        if($scope.objeto[i].check){
          count++;
          $scope.mensaje = 'Se generarán las Liquidaciones de Sueldo de los <b>' + count + '</b> trabajadores seleccionados.';
        }
      }
      if(count===1){
        count = $scope.trabajador.cargas[0].nombreCompleto;
        $scope.mensaje = 'Se generará la Liquidación de Sueldo de <b>' + count + '</b>.';
      }
      return count;
    }

    $scope.selectAll = function(){
      if($scope.objeto.todos){
        var total = 0;
        for(var i=0, len=$scope.trabajador.cargas.length; i<len; i++){
          $scope.objeto[i].check = true
          $scope.isSelect = true;
          total++;  
        }
        countSelected();
      }else{
        for(var i=0, len=$scope.trabajador.cargas.length; i<len; i++){
          $scope.objeto[i].check = false
          $scope.isSelect = false;
        }
      }
    }

    $scope.autorizar = function(){
      var cargas = [];
      for(var i=0,len=$scope.trabajador.cargas.length; i<len; i++){
        if($scope.objeto[i].check){
          cargas.push({ sid : $scope.trabajador.cargas[i].sid, fecha : $scope.objeto[i].fecha });
        }
      }
      var obj = { sidTrabajador : $scope.trabajador.sid, cargas : cargas, tramo : $scope.carga.tramo.tramo };
      $rootScope.cargando=true;
      var datos = trabajador.autorizarCargas().post({}, obj);
      datos.$promise.then(function(response){
        $rootScope.cargando=false;
        $uibModalInstance.close(response);    
      });
    }

    $scope.modificar = function(){
      var cargas = [];
      for(var i=0,len=$scope.trabajador.cargas.length; i<len; i++){
        if($scope.objeto[i].check){
          cargas.push({ sid : $scope.trabajador.cargas[i].sid, fecha : $scope.objeto[i].fecha });
        }
      }
      if(cargas.length===0){
        console.log('no cargas')
      }else{
        console.log('cargas')
      }
      /*var obj = { sidTrabajador : $scope.trabajador.sid, cargas : cargas, tramo : $scope.carga.tramo.id };
      $rootScope.cargando=true;
      var datos = trabajador.modificarCargas().post({}, obj);
      datos.$promise.then(function(response){
        $rootScope.cargando=false;
        $uibModalInstance.close(response);    
      });*/
    }

    // Fecha

    $scope.today = function() {
      $scope.dt = new Date();
    };
    $scope.today();
    $scope.inlineOptions = {
      customClass: getDayClass,
      minDate: new Date(),
      showWeeks: true
    };

    $scope.dateOptions = {
      dateDisabled: disabled,
      formatYear: 'yy',
      maxDate: new Date(2020, 5, 22),
      minDate: new Date(),
      startingDay: 1
    };  

    function disabled(data) {
      var date = data.date,
        mode = data.mode;
      return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
    }

    $scope.toggleMin = function() {
      $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
      $scope.dateOptions.minDate = $scope.inlineOptions.minDate;
    };

    $scope.toggleMin();

    $scope.openFecha = function(index) {
      $scope.objeto[index].popupFecha.opened = true;
    };

    $scope.setDate = function(year, month, day) {
      $scope.fecha = new Date(year, month, day);
    };

    $scope.format = ['dd-MMMM-yyyy'];

    $scope.popupFecha = {
      opened: false
    };

    function getDayClass(data) {
      var date = data.date,
        mode = data.mode;
      if (mode === 'day') {
        var dayToCheck = new Date(date).setHours(0,0,0,0);
        for (var i = 0; i < $scope.events.length; i++) {
          var currentDay = new Date($scope.events[i].date).setHours(0,0,0,0);
          if (dayToCheck === currentDay) {
            return $scope.events[i].status;
          }
        }
      }
      return '';
    }

  })
  .controller('FormCargasCtrl', function ($rootScope, Notification, $filter, $scope, tiposCargas, $uibModalInstance, objeto, carga, fecha) {

    $scope.tiposCargas = angular.copy(tiposCargas);

    $scope.parentescos = [ 'Hijo/a o Hijastro/a', 'Cónyuge', 'Nieto/a', 'Bisnieto/a', 'Madre', 'Padre', 'Madre Viuda', 'Abuelo/a', 'Bisabuelo/a', 'Otro' ];
    if(objeto.trabajador){
      $scope.trabajador = angular.copy(objeto.trabajador);
      $scope.carga = angular.copy(objeto);
      $scope.carga.fechaNacimiento = fecha.convertirFecha($scope.carga.fechaNacimiento);
      $scope.isEdit = true;
      $scope.carga.parentesco = $filter('filter')( $scope.parentescos, $scope.carga.parentesco, true )[0];
      $scope.carga.tipo = $filter('filter')( $scope.tiposCargas, { id :  $scope.carga.tipo.id }, true )[0];
      $scope.titulo = 'Modificación Carga Familiar';
    }else{
      $scope.trabajador = angular.copy(objeto);
      $scope.carga = { esCarga : false, rut : '', parentesco : '', nombreCompleto : '', fechaNacimiento : null, sexo : null, tipo : $scope.tiposCargas[0] };
      $scope.isEdit = false;
      $scope.titulo = 'Ingreso Carga Familiar';
    }

    $scope.guardar = function(car, trabajador){

      $rootScope.cargando=true;
      var response;
      var Carga = { idFichaTrabajador : trabajador.idFicha, esCarga : car.esCarga, rut : car.rut, parentesco : car.parentesco, nombreCompleto : car.nombreCompleto, fechaNacimiento : car.fechaNacimiento, sexo : car.sexo, tipo : car.tipo.id };

      if( $scope.carga.sid ){
        response = carga.datos().update({sid:$scope.carga.sid}, Carga);
      }else{
        response = carga.datos().create({}, Carga);
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

    $scope.today = function() {
      $scope.dt = new Date();
    };
    $scope.today();
    $scope.inlineOptions = {
      customClass: getDayClass,
      minDate: new Date(),
      showWeeks: true
    };

    $scope.dateOptions = {
      dateDisabled: disabled,
      formatYear: 'yy',
      maxDate: new Date(2020, 5, 22),
      minDate: new Date(),
      startingDay: 1
    };  

    function disabled(data) {
      var date = data.date,
        mode = data.mode;
      return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
    }

    $scope.toggleMin = function() {
      $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
      $scope.dateOptions.minDate = $scope.inlineOptions.minDate;
    };

    $scope.toggleMin();

    $scope.openFechaNacimientoCarga = function() {
      $scope.popupFechaNacimientoCarga.opened = true;
    };

    $scope.dateOptionsMes = {
      showWeeks: false,
      viewMode: "months", 
      minMode: 'month',
      format: "mm/yyyy"
    };

    $scope.setDate = function(year, month) {
      $scope.fecha = new Date(year, month);
    };

    $scope.format = ['MMMM-yyyy'];

    $scope.popupFechaNacimientoCarga = {
      opened: false
    };

    function getDayClass(data) {
      var date = data.date,
        mode = data.mode;
      if (mode === 'day') {
        var dayToCheck = new Date(date).setHours(0,0,0,0);
        for (var i = 0; i < $scope.events.length; i++) {
          var currentDay = new Date($scope.events[i].date).setHours(0,0,0,0);
          if (dayToCheck === currentDay) {
            return $scope.events[i].status;
          }
        }
      }
      return '';
    }
/*
  })
  .controller('FormCargasCtrl', function ($rootScope, Notification, $filter, $scope, tiposCargas, $uibModalInstance, objeto, carga, fecha) {

    $scope.tiposCargas = angular.copy(tiposCargas);

    $scope.parentescos = [ 'Hijo/a o Hijastro/a', 'Cónyuge', 'Nieto/a', 'Bisnieto/a', 'Madre', 'Padre', 'Madre Viuda', 'Abuelo/a', 'Bisabuelo/a', 'Otro' ];
    if(objeto.trabajador){
      $scope.trabajador = angular.copy(objeto.trabajador);
      $scope.carga = angular.copy(objeto);
      $scope.carga.fechaNacimiento = fecha.convertirFecha($scope.carga.fechaNacimiento);
      $scope.isEdit = true;
      $scope.carga.parentesco = $filter('filter')( $scope.parentescos, $scope.carga.parentesco, true )[0];
      $scope.carga.tipo = $filter('filter')( $scope.tiposCargas, { id :  $scope.carga.tipo.id }, true )[0];
      $scope.titulo = 'Modificación Carga Familiar';
    }else{
      $scope.trabajador = angular.copy(objeto);
      $scope.carga = { esCarga : false, rut : '', parentesco : '', nombreCompleto : '', fechaNacimiento : null, sexo : null, tipo : $scope.tiposCargas[0] };
      $scope.isEdit = false;
      $scope.titulo = 'Ingreso Carga Familiar';
    }

    $scope.guardar = function(car, trabajador){

      $rootScope.cargando=true;
      var response;
      var Carga = { idFichaTrabajador : trabajador.idFicha, esCarga : car.esCarga, rut : car.rut, parentesco : car.parentesco, nombreCompleto : car.nombreCompleto, fechaNacimiento : car.fechaNacimiento, sexo : car.sexo, tipo : car.tipo.id };

      if( $scope.carga.sid ){
        response = carga.datos().update({sid:$scope.carga.sid}, Carga);
      }else{
        response = carga.datos().create({}, Carga);
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

    $scope.today = function() {
      $scope.dt = new Date();
    };
    $scope.today();
    $scope.inlineOptions = {
      customClass: getDayClass,
      minDate: new Date(),
      showWeeks: true
    };

    $scope.dateOptions = {
      dateDisabled: disabled,
      formatYear: 'yy',
      maxDate: new Date(2020, 5, 22),
      minDate: new Date(),
      startingDay: 1
    };  

    function disabled(data) {
      var date = data.date,
        mode = data.mode;
      return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
    }

    $scope.toggleMin = function() {
      $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
      $scope.dateOptions.minDate = $scope.inlineOptions.minDate;
    };

    $scope.toggleMin();

    $scope.openFechaNacimientoCarga = function() {
      $scope.popupFechaNacimientoCarga.opened = true;
    };

    $scope.dateOptionsMes = {
      showWeeks: false,
      viewMode: "months", 
      minMode: 'month',
      format: "mm/yyyy"
    };

    $scope.setDate = function(year, month) {
      $scope.fecha = new Date(year, month);
    };

    $scope.format = ['MMMM-yyyy'];

    $scope.popupFechaNacimientoCarga = {
      opened: false
    };

    function getDayClass(data) {
      var date = data.date,
        mode = data.mode;
      if (mode === 'day') {
        var dayToCheck = new Date(date).setHours(0,0,0,0);
        for (var i = 0; i < $scope.events.length; i++) {
          var currentDay = new Date($scope.events[i].date).setHours(0,0,0,0);
          if (dayToCheck === currentDay) {
            return $scope.events[i].status;
          }
        }
      }
      return '';
    }
*/

  });