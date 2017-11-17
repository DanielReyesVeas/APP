'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:IngresoDescuentosCtrl
 * @description
 * # IngresoDescuentosCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('IngresoDescuentosCtrl', function ($rootScope, $uibModal, $filter, $scope, $anchorScroll, tipoDescuento, constantes, Notification, trabajador) {
    
    $anchorScroll();

    $scope.datos = [];
    $scope.constantes = constantes;
    $scope.cargado = false;

    $scope.tabDescuentos = true;
    $scope.tabAfp = false;
    $scope.tabCCAF = false;

    $scope.openTab = function(tab){
      switch (tab) {
        case 'descuentos':
          $scope.tabDescuentos = true;
          $scope.tabAfp = false;
          $scope.tabCCAF = false;
          break;
        case 'afp':
          $scope.tabDescuentos = false;
          $scope.tabAfp = true;
          $scope.tabCCAF = false;
          break;
        case 'ccaf':
          $scope.tabDescuentos = false;
          $scope.tabAfp = false;
          $scope.tabCCAF = true;
          break;
      }
    }

    $scope.cargarDatos = function(){
      $rootScope.cargando = true;
      $scope.cargado = false;
      var datos = tipoDescuento.ingresoDescuentos().get();
      datos.$promise.then(function(response){
        $scope.datos = response.datos;
        $scope.datosAfp = response.datosAfp;
        $scope.datosCCAF = response.datosCCAF;
        $scope.accesos = response.accesos;
        $rootScope.cargando = false;
        $scope.cargado = true;
      });
    };

    $scope.cargarDatos();
    
    $scope.reporteTrabajadores = function(){
      $rootScope.cargando = true;
      var datos = trabajador.input().get();
      datos.$promise.then(function(response){
        openReporteTrabajadores(response);
        $rootScope.cargando = false;
      });
    }

    $scope.importarPlanilla = function (obj) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-importar-planilla-descuento.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormImportarPlanillaDescuentoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
      }, function () {
        javascript:void(0)
      });
    }

    function openReporteTrabajadores(obj) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-reporte-trabajadores-descuento.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormReporteTrabajadoresDescuentoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (object) {
        Notification.success({message: object.mensaje, title: 'Mensaje del Sistema'});
        $scope.cargarDatos();
      }, function () {
        javascript:void(0)
      });
    }

    $scope.openIngresoDescuento = function (obj) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nuevo-ingreso-descuento.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormIngresoDescuentoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (object) {
        Notification.success({message: object.mensaje, title: 'Mensaje del Sistema'});
        $scope.cargarDatos();
      }, function () {
        javascript:void(0)
      });
    }

    $scope.openIngresoTotalDescuento = function (obj) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nuevo-ingreso-total-descuento.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormIngresoTotalDescuentoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        $scope.cargarDatos();
      }, function () {
        javascript:void(0)
      });
    }

    $scope.ingresoSeccion = function(des){
      $scope.cargado = false;
      $rootScope.cargando = true;
      var datos = trabajador.secciones().get();
      datos.$promise.then(function(response){
        openIngresoSeccionDescuento(des, response);
        $rootScope.cargando = false;
        $scope.cargado = true;
      });
    }

    function openIngresoSeccionDescuento(obj, trab) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nuevo-ingreso-seccion-descuento.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormIngresoSeccionDescuentoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          },
          trabajadores: function () {
            return trab;
          }
        }
      });
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        $scope.cargarDatos();
      }, function () {
        javascript:void(0)
      });
    }

    $scope.openReporteDescuento = function (obj) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-reporte-descuento.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormReporteDescuentoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        $scope.cargarDatos();
      }, function () {
        javascript:void(0)
      });
    }

    $scope.reporte = function(des){
      $rootScope.cargando=true;
      var datos = tipoDescuento.datos().get({sid: des.sid});
      datos.$promise.then(function(response){
        $scope.openReporteDescuento( response );
        $rootScope.cargando=false;
      });
    };    

  })
  .controller('FormImportarPlanillaDescuentoCtrl', function ($scope, $uibModal, $uibModalInstance, $http, $filter, constantes, $rootScope, Notification, Upload, objeto, descuento) {

    var des = angular.copy(objeto);
    $scope.descuento = {};
    $scope.error = {};
    $scope.datos=[];
    $scope.listaErrores=[];
    $scope.constantes = constantes;
    $scope.mesActual = $rootScope.globals.currentUser.empresa.mesDeTrabajo.mesActivo;

    $scope.$watch('files', function () {
      $scope.upload($scope.files);
    });

    $scope.upload = function (files) {
      if(files) {              
        $scope.error = {};
        $scope.datos=[];
        $scope.listaErrores=[];
        var file = files;
        Upload.upload({
          url: constantes.URL + 'descuentos/planilla/importar',
          data: { file : file, descuento : des }
        }).progress(function (evt) {
          var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
          $scope.dynamic = progressPercentage;
        }).success(function (data){
          $scope.dynamic=0;
          if( data.success ){
              $scope.datos = data.datos;
              $scope.descuento = data.descuento;
          }else{
            if( data.errores ){
              $scope.listaErrores = data.errores;
              Notification.error({message: 'Errores en los datos del archivo', title: 'Mensaje del Sistema'});
            }else{
              Notification.error({message: data.mensaje, title: 'Mensaje del Sistema'});                            
            }
          }
        });                
      }
    };

    $scope.confirmarDatos = function(){
      $rootScope.cargando=true;
      var obj = { trabajadores : $scope.datos, descuento : $scope.descuento };
      var datos = descuento.importar().post({}, obj);
      datos.$promise.then(function(response){
        if(response.success){
          $uibModalInstance.close(response.mensaje);
        }else{
          // error
          $scope.erroresDatos = response.errores;
          Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
        }
        $rootScope.cargando = false;
      });
    }

  })
  .controller('FormReporteDescuentoCtrl', function ($scope, $uibModal, $uibModalInstance, objeto, $http, $filter, $rootScope, Notification, trabajador, tipoDescuento, descuento) {
    $scope.descuento = objeto.datos;
    $scope.accesos = objeto.accesos;

    $scope.reporteTrabajador = function(trab){
      $rootScope.cargando=true;
      var datos = trabajador.descuentos().get({sid: trab.sid});
      datos.$promise.then(function(response){
        $scope.openReporteTrabajadorDescuentos( response );
        $rootScope.cargando=false;
      });
    }

    $scope.cargarDatos = function(des){
      $rootScope.cargando=true;
      var datos = tipoDescuento.datos().get({sid: des});
      datos.$promise.then(function(response){
        $scope.descuento = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando=false;
      });
    }; 

    $scope.openReporteTrabajadorDescuentos = function (obj) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-reporte-trabajador-descuentos.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormReporteTrabajadorDescuentosCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (mensaje) {
        Notification.success({message: mensaje, title: 'Mensaje del Sistema'});
        $scope.reporteTrabajador();
      }, function () {
        javascript:void(0)
      });
    }

    $scope.editar = function(des){
      $rootScope.cargando=true;
      var datos = descuento.datos().get({sid: des.sid});
      datos.$promise.then(function(response){
        $scope.openEditarDescuento( response );
        $rootScope.cargando=false;
      });
    };

    $scope.eliminar = function(des, tipo){
      $rootScope.cargando=true;
      $scope.result = descuento.datos().delete({ sid: des.sid });
      $scope.result.$promise.then( function(response){
        if(response.success){
          Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
          $scope.cargarDatos(tipo);
        }
      });
    };

    $scope.openEditarDescuento = function (obj) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nuevo-ingreso-descuento.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormIngresoDescuentoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (object) {
        Notification.success({message: object.mensaje, title: 'Mensaje del Sistema'});
        $scope.cargarDatos(object.sidDescuento);
      }, function () {
        javascript:void(0)
      });
    }

  })
  .controller('FormReporteTrabajadorDescuentosCtrl', function ($scope, $uibModal, $uibModalInstance, objeto, $http, $filter, $rootScope, Notification, trabajador, descuento) {
    $scope.trabajador = objeto.datos;
    $scope.accesos = objeto.accesos;

    $scope.cargarDatos = function(trab){
      $rootScope.cargando=true;
      var datos = trabajador.descuentos().get({sid: trab});
      datos.$promise.then(function(response){
        $scope.trabajador = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando=false;
      });
    }; 

    $scope.editar = function(des){
      $rootScope.cargando=true;
      var datos = descuento.datos().get({sid: des.sid});
      datos.$promise.then(function(response){
        $scope.openEditarDescuento( response );
        $rootScope.cargando=false;
      });
    };

    $scope.eliminar = function(des, trab){
      $rootScope.cargando=true;
      $scope.result = descuento.datos().delete({ sid: des.sid });
      $scope.result.$promise.then( function(response){
        if(response.success){
          Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
          $scope.cargarDatos(trab);
        }
      });
    };

    $scope.openEditarDescuento = function (obj) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nuevo-ingreso-descuento.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormIngresoDescuentoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (object) {
        Notification.success({message: object.mensaje, title: 'Mensaje del Sistema'});
        $scope.cargarDatos(object.sidTrabajador);
      }, function () {
        javascript:void(0)
      });
    }

  })
  .controller('FormIngresoSeccionDescuentoCtrl', function ($scope, $uibModal, $uibModalInstance, objeto, $http, $filter, $rootScope, Notification, trabajador, trabajadores, moneda) {

    $scope.descuento = objeto;
    $scope.datos = angular.copy(trabajadores); 
    $scope.monedaActual = 'pesos';   
    $scope.monedaActualGlobal = 'pesos'; 
    $scope.cargado = true; 

    $scope.convertir = function(valor, mon){
      return moneda.convertir(valor, mon);
    }

    $scope.monedas = [
                { id : 1, nombre : '$' }, 
                { id : 2, nombre : 'UF' }, 
                { id : 3, nombre : 'UTM' } 
    ];

    $scope.uf = $rootScope.globals.indicadores.uf.valor;
    $scope.utm = $rootScope.globals.indicadores.utm.valor;

    $scope.monto = { moneda : $scope.monedas[0].nombre, montoGlobal : false }; 

    $scope.cambiarMoneda = function(){
      $scope.monto.global = null;
      switch($scope.monto.moneda){
        case '$':
          $scope.monedaActualGlobal = 'pesos'; 
          break;
        case 'UF':
          $scope.monedaActualGlobal = 'UF'; 
          break;
        case 'UTM':
          $scope.monedaActualGlobal = 'UTM'; 
          break;
      }    
    }

    $scope.openMeses = function (obj, des, mon) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-descuento-meses.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormIngresoDescuentoMesesCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          },
          des: function () {
            return des;
          },
          monto: function () {
            return mon;
          }
        }
      });
      miModal.result.then(function (object) {
        $uibModalInstance.close(object);
      }, function () {
        javascript:void(0)
      });
    }

    $scope.cambiarMonedaIndividual = function(index){
      $scope.datos.descuento[index].monto = null;
      switch($scope.datos.descuento[index].moneda){
        case '$':
          $scope.datos.descuento[index].monedaActual = 'pesos'; 
          break;
        case 'UF':
          $scope.datos.descuento[index].monedaActual = 'UF'; 
          break;
        case 'UTM':
          $scope.datos.descuento[index].monedaActual = 'UTM'; 
          break;
      }
    }

    function getTrabajadoresSeccion(sid){
      $scope.cargado = false;
      $rootScope.cargando = true;

      var array = [];
      for(var i=0,len=$scope.datos.trabajadores.length; i<len; i++){
        if($scope.datos.trabajadores[i].seccion.sid===sid){
          array.push($scope.datos.trabajadores[i]);
        }
      }
      $scope.datos.trabajadores = array;
      $rootScope.cargando = false;
      $scope.cargado = true;
      crearModels();
    }

    $scope.cambiarSeccion = function(){
      if(!$scope.objeto.descuento.seccion){
        $scope.datos = angular.copy(trabajadores); 
        crearModels();
      }
    }

    $scope.seleccionarSeccion = function(seccion){      
      getTrabajadoresSeccion(seccion.sid);
    }

    function crearModels(){
      $scope.datos.descuento = [];
      for(var i=0, len=$scope.datos.trabajadores.length; i<len; i++){
        $scope.datos.descuento.push({ check : false, monto : null, tipo_descuento_id : $scope.descuento.id, trabajador_id : $scope.datos.trabajadores[i].id, moneda : $scope.monedas[0].nombre, monedaActual : 'pesos' });
      }      
    }

    crearModels();

    $scope.cambiarMontoGlobal = function(){
      if($scope.monto.montoGlobal){
        $scope.monto.montoGlobal = false;
      }else{
        $scope.monto.moneda = $scope.monedas[0].nombre;
        $scope.monto.global = null;
        $scope.cambiarMoneda();
        $scope.monto.montoGlobal = true;
      }
    }

    $scope.select = function(i){
      if(!$scope.datos.descuento[i].check){
        $scope.datos.descuento[i].monto = null;
        $scope.datos.descuento[index].moneda = $scope.monedas[0].nombre;
        $scope.cambiarMonedaIndividual(index);
        if($scope.datos.todos){
          $scope.datos.todos = false;
        }
      }
    }

    $scope.selectAll = function(){
      if($scope.datos.todos){
        for(var i=0, len=$scope.datos.trabajadores.length; i<len; i++){
          $scope.datos.descuento[i].check = true
        }
      }else{
        for(var i=0, len=$scope.datos.trabajadores.length; i<len; i++){
          $scope.datos.descuento[i].check = false
        }
      }
    }    

  })
  .controller('FormIngresoDescuentoMesesCtrl', function ($scope, $uibModal, $uibModalInstance, objeto, $http, $filter, $rootScope, trabajador, Notification, descuento, des, monto, fecha) {

    $scope.descuento = des;
    $scope.monto = monto;
    $scope.datos = { trabajadores : objeto };
    $scope.mesActual = $rootScope.globals.currentUser.empresa.mesDeTrabajo;
    $scope.objeto = { descuento : { mensual : true, rangoMeses : false, permanente : false, anual : false, mes : fecha.convertirFecha($scope.mesActual.mes), desde : fecha.convertirFecha($scope.mesActual.mes)  } };

    $scope.cambiarMes = function(mes){
      if(mes==='mensual'){
        if($scope.objeto.descuento.mensual){
          $scope.objeto.descuento.rangoMeses = false;
          $scope.objeto.descuento.permanente = false;
        }else{
          $scope.objeto.descuento.rangoMeses = true;
        }
      }
      if(mes==='rangoMeses'){
        if($scope.objeto.descuento.rangoMeses){
          $scope.objeto.descuento.mensual = false;
          $scope.objeto.descuento.permanente = false;
        }else{
          $scope.objeto.descuento.mensual = true;
        }
      }
      if(mes==='permanente'){
        if($scope.objeto.descuento.permanente){
          $scope.objeto.descuento.mensual = false;
          $scope.objeto.descuento.rangoMeses = false;
        }else{
          $scope.objeto.descuento.mensual = true;
        }
      }
      $scope.objeto.descuento.anual = false;
    }

    $scope.ingresoMasivoDescuento = function(){
      var ingresoMasivo = { descuentos : [] };
      $rootScope.cargando=true;

      for(var i=0, len=$scope.datos.trabajadores.length; i<len; i++){
        if($scope.datos.trabajadores[i].check){
          if($scope.monto.montoGlobal){
            $scope.datos.trabajadores[i].monto = $scope.monto.global;
            $scope.datos.trabajadores[i].moneda = $scope.monto.moneda;
          }

          $scope.datos.trabajadores[i].por_mes = $scope.objeto.descuento.mensual;
          $scope.datos.trabajadores[i].rango_meses = $scope.objeto.descuento.rangoMeses;
          $scope.datos.trabajadores[i].permanente = $scope.objeto.descuento.permanente;
          $scope.datos.trabajadores[i].todos_anios = $scope.objeto.descuento.anual;

          if($scope.datos.trabajadores[i].por_mes){
            $scope.datos.trabajadores[i].mes_id = $scope.mesActual.id;
            $scope.datos.trabajadores[i].mes = $scope.mesActual.mes;
            $scope.datos.trabajadores[i].desde = null;
            $scope.datos.trabajadores[i].hasta = null;
          }else if($scope.datos.trabajadores[i].rango_meses){
            $scope.datos.trabajadores[i].mes_id = null;
            $scope.datos.trabajadores[i].mes = null;
            $scope.datos.trabajadores[i].desde = $scope.objeto.descuento.mes;
            $scope.datos.trabajadores[i].hasta = $scope.objeto.descuento.hasta;
          }else{
            $scope.datos.trabajadores[i].mes_id = null;
            $scope.datos.trabajadores[i].mes = null;
            $scope.datos.trabajadores[i].desde = null;
            $scope.datos.trabajadores[i].hasta = null;
          }

          ingresoMasivo.descuentos.push($scope.datos.trabajadores[i]);
        }
      }

      $rootScope.cargando = true;
      var datos = descuento.masivo().post({}, ingresoMasivo);
      datos.$promise.then(function(response){
          if(response.success){
            $uibModalInstance.close(response.mensaje);
          }else{
            // error
            $scope.erroresDatos = response.errores;
            Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
          }
          $rootScope.cargando = false;
      });
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

    $scope.openFechaHasta = function() {
      $scope.popupFechaHasta.opened = true;
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

    $scope.popupFechaHasta = {
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
  .controller('FormReporteTrabajadoresDescuentoCtrl', function ($scope, $uibModal, $uibModalInstance, objeto, $http, $filter, $rootScope, trabajador, Notification, descuento) {

    $scope.mostrar = false;
    $scope.datos = angular.copy(objeto.datos);

    function cargarDatosTrabajador(sid){
      $rootScope.cargando=true;
      $scope.mostrar = false;
      var datos = trabajador.descuentos().get({sid: sid});
      datos.$promise.then(function(response){
        $scope.trabajador = response.datos;
        $scope.accesos = response.accesos;
        $rootScope.cargando=false;
        $scope.mostrar = true;
      });
    }

    $scope.seleccionarTrabajador = function(sid){      
      cargarDatosTrabajador(sid);
    }

    $scope.editar = function(des){
      $rootScope.cargando=true;
      var datos = descuento.datos().get({sid: des.sid});
      datos.$promise.then(function(response){
        $scope.openEditarDescuento( response );
        $rootScope.cargando=false;
      });
    };

    $scope.eliminar = function(des, trab){
      $rootScope.cargando=true;
      $scope.result = descuento.datos().delete({ sid: des.sid });
      $scope.result.$promise.then( function(response){
        if(response.success){
          Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
          cargarDatosTrabajador(trab);
        }
      });
    };

    $scope.openEditarDescuento = function (obj) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-nuevo-ingreso-descuento.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormIngresoDescuentoCtrl',
        size: 'lg',
        resolve: {
          objeto: function () {
            return obj;
          }
        }
      });
      miModal.result.then(function (object) {
        Notification.success({message: object.mensaje, title: 'Mensaje del Sistema'});
        cargarDatosTrabajador(object.sidTrabajador);
      }, function () {
        javascript:void(0)
      });
    }

  })
  .controller('FormIngresoDescuentoCtrl', function ($scope, $uibModalInstance, objeto, $http, $filter, $rootScope, trabajador, Notification, descuento, fecha, moneda) {
    
    $scope.monedaActual = 'pesos'; 

    $scope.convertir = function(valor, mon){
      return moneda.convertir(valor, mon);
    }

    $scope.monedas = [
                { id : 1, nombre : '$' }, 
                { id : 2, nombre : 'UF' }, 
                { id : 3, nombre : 'UTM' } 
    ];

    $scope.uf = $rootScope.globals.indicadores.uf;
    $scope.utm = $rootScope.globals.indicadores.utm;
    $scope.mesActual = $rootScope.globals.currentUser.empresa.mesDeTrabajo;

    $scope.cambiarMes = function(mes){
      if(mes==='mensual'){
        if($scope.objeto.descuento.mensual){
          $scope.objeto.descuento.rangoMeses = false;
          $scope.objeto.descuento.permanente = false;
        }else{
          $scope.objeto.descuento.rangoMeses = true;
        }
      }
      if(mes==='rangoMeses'){
        if($scope.objeto.descuento.rangoMeses){
          $scope.objeto.descuento.mensual = false;
          $scope.objeto.descuento.permanente = false;
        }else{
          $scope.objeto.descuento.mensual = true;
        }
      }
      if(mes==='permanente'){
        if($scope.objeto.descuento.permanente){
          $scope.objeto.descuento.mensual = false;
          $scope.objeto.descuento.rangoMeses = false;
        }else{
          $scope.objeto.descuento.mensual = true;
        }
      }
      $scope.objeto.descuento.anual = false;
    }

    if(objeto.trabajador){
      $scope.objeto = {};
      $scope.objeto.descuento = { trabajador : { nombreCompleto : objeto.trabajador.nombreCompleto, id : objeto.trabajador.id, sid : objeto.trabajador.sid} , monto : objeto.monto, sid : objeto.sid, moneda : objeto.moneda, mensual : objeto.porMes, rangoMeses : objeto.rangoMeses, permanente : objeto.permanente, anual : objeto.todosAnios };
      $scope.trabajador = objeto.trabajador;
      $scope.descuento = objeto.tipo;

      if($scope.objeto.descuento.mensual){
        $scope.objeto.descuento.mes = fecha.convertirFecha(objeto.mes.mes);
        $scope.objeto.descuento.hasta = null;
      }else if($scope.objeto.descuento.rangoMeses){
        $scope.objeto.descuento.mes = fecha.convertirFecha(objeto.desde);
        $scope.objeto.descuento.hasta = fecha.convertirFecha(objeto.hasta);
      }else{
        $scope.objeto.descuento.mes = null;
        $scope.objeto.descuento.hasta = null;
      }

      switch($scope.objeto.descuento.moneda){
        case '$':
          $scope.monedaActual = 'pesos'; 
          break;
        case 'UF':
          $scope.monedaActual = 'UF'; 
          break;
        case 'UTM':
          $scope.monedaActual = 'UTM'; 
          break;
      }

      $scope.mostrar = true;
      $scope.isEdit = true;
      $scope.titulo = 'Modificación Descuento';
    }else{
      $scope.descuento = objeto;
      $scope.objeto = {};
      $scope.objeto.descuento = { moneda : $scope.monedas[0].nombre, mensual : true, rangoMeses : false, permanente : false, anual : false, mes : fecha.convertirFecha($scope.mesActual.mes), desde : fecha.convertirFecha($scope.mesActual.mes) };
      $scope.mostrar = false;
      $scope.isEdit = false;
      $scope.titulo = 'Ingreso Individual Descuento';
    }    

    $scope.cambiarMoneda = function(){
      $scope.objeto.descuento.monto = null;
      switch($scope.objeto.descuento.moneda){
        case '$':
          $scope.monedaActual = 'pesos'; 
          break;
        case 'UF':
          $scope.monedaActual = 'UF'; 
          break;
        case 'UTM':
          $scope.monedaActual = 'UTM'; 
          break;
      }
    }  

    $scope.cargarDatos = function(){
      $rootScope.cargando = true;
      var datos = trabajador.input().get();
      datos.$promise.then(function(response){
        $scope.datos = response.datos;        
        $rootScope.cargando = false;
      });
    };

    $scope.cargarDatos();

    $scope.seleccionarTrabajador = function(trabajador){
      $scope.trabajador = trabajador.trabajador;
      $scope.mostrar = true;
    }

    $scope.ingresoIndividualDescuento = function(obj, des){
      $rootScope.cargando=true;
      var response;
      var Descuento = { idTrabajador : obj.trabajador.id, porMes : obj.mensual, rangoMeses : obj.rangoMeses, permanente : obj.permanente, todosAnios : obj.anual, idTipoDescuento : des.id, moneda : obj.moneda, monto : obj.monto };

      if(Descuento.porMes){
        Descuento.idMes = $scope.mesActual.id;
        Descuento.mes = $scope.mesActual.mes;
        Descuento.desde = null;
        Descuento.hasta = null;
      }else if(Descuento.rangoMeses){
        Descuento.idMes = null;
        Descuento.mes = null;
        Descuento.desde = obj.mes;
        Descuento.hasta = obj.hasta;
      }else{
        Descuento.idMes = null;
        Descuento.mes = null;
        Descuento.desde = null;
        Descuento.hasta = null;
      }

      if( obj.sid ){
        response = descuento.datos().update({sid:$scope.objeto.descuento.sid}, Descuento);
      }else{
        response = descuento.datos().create({}, Descuento);
      }
      

      response.$promise.then(
        function(response){
          if(response.success){
            $uibModalInstance.close({ mensaje : response.mensaje, sidDescuento : des.sid, sidTrabajador : obj.trabajador.sid});
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

    $scope.openFechaHasta = function() {
      $scope.popupFechaHasta.opened = true;
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

    $scope.popupFechaHasta = {
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

  });

