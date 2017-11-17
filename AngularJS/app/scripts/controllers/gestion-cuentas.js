'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:GestionCuentasCtrl
 * @description
 * # GestionCuentasCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
  .controller('GestionCuentasCtrl', function ($scope, $uibModal, $filter, tipoHaber, tipoDescuento, $anchorScroll, aporte, constantes, mesDeTrabajo, $rootScope, Notification) {
    $anchorScroll();

    $scope.empresa = $rootScope.globals.currentUser.empresa;
    $scope.constantes = constantes;
    $scope.cargado = false;
    
    $scope.tabAportes = true;
    $scope.tabAfpEmpleador = false;
    $scope.tabAfpTrabajador = false;
    $scope.tabSalud = false;
    $scope.tabSeguroCesantiaEmpleador = false;
    $scope.tabSeguroCesantiaTrabajador = false;
    $scope.tabCuentasAhorroAfp = false;
    $scope.tabApvA = false;
    $scope.tabApvB = false;
    $scope.tabApvc = false;
    $scope.tabCCAF = false;
    $scope.tabExCaja = false;
    $scope.tabHaberesImp = false;
    $scope.tabHaberesNoImp = false;
    $scope.tabDescuentos = false;
    $scope.tabDescuentosLegales = false;

    $scope.openTab = function(tab){
      switch (tab) {
        case 'aportes':
          $scope.tabAportes = true;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'afpEmpleador':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = true;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'afpTrabajador':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = true;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'cuentasAhorroAfp':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = true;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'salud':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = true;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'seguroCesantiaEmpleador':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = true;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'seguroCesantiaTrabajador':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = true;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'apvA':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = true;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'apvB':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = true;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'apvc':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = true;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'exCaja':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = true;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'ccaf':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = true;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'imp':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = true;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'noImp':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = true;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales= false;
          break;
        case 'descuentos':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = true;
          $scope.tabDescuentosLegales = false;
          break;
        case 'descuentosLegales':
          $scope.tabAportes = false;
          $scope.tabAfpEmpleador = false;
          $scope.tabAfpTrabajador = false;
          $scope.tabSalud = false;
          $scope.tabSeguroCesantiaEmpleador = false;
          $scope.tabSeguroCesantiaTrabajador = false;
          $scope.tabCuentasAhorroAfp = false;
          $scope.tabApvA = false;
          $scope.tabApvB = false;
          $scope.tabApvc = false;
          $scope.tabCCAF = false;
          $scope.tabExCaja = false;
          $scope.tabHaberesImp = false;
          $scope.tabHaberesNoImp = false;
          $scope.tabDescuentos = false;
          $scope.tabDescuentosLegales = true;
          break;
      }
    }
    
    $scope.editar = function(apo){
      $rootScope.cargando = true;
      var datos = aporte.datos().get({ sid: apo.sid });
      datos.$promise.then(function(response){
        openForm(response.datos, response.cuentas, 'aporte');
        $rootScope.cargando = false;      
      });
    }

    $scope.editarHaber = function(apo){
      $rootScope.cargando = true;
      var datos = tipoHaber.datos().get({ sid: apo.sid });
      datos.$promise.then(function(response){
        openForm(response.datos, response.cuentas, 'haber');
        $rootScope.cargando = false;      
      });
    }

    $scope.editarDescuento = function(apo){
      $rootScope.cargando = true;
      var datos = tipoDescuento.cuenta().get({ sid: apo.sid });
      datos.$promise.then(function(response){
        openForm(response.datos, response.cuentas, 'descuento');
        $rootScope.cargando = false;      
      });
    }

    function openForm(obj, cuentas, tipo) {
      var miModal = $uibModal.open({
        animation: true,
        templateUrl: 'views/forms/form-cuenta.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormCuentaCtrl',
        resolve: {
          objeto: function () {
            return obj;
          },
          cuentas: function () {
            return cuentas;
          },
          tipo: function () {
            return tipo;
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

    function cargarDatos(){
      $scope.cargado = false;
      $rootScope.cargando = true;
      var datos = aporte.datos().get();
      datos.$promise.then(function(response){
        $scope.accesos = response.accesos;
        $scope.isCuentas = response.isCuentas;
        $scope.aportes = response.aportes;
        $scope.afpsEmpleador = response.afpsEmpleador;
        $scope.afpsTrabajador = response.afpsTrabajador;
        $scope.salud = response.salud;
        $scope.seguroCesantiaEmpleador = response.seguroCesantiaEmpleador;
        $scope.seguroCesantiaTrabajador = response.seguroCesantiaTrabajador;
        $scope.cuentasAhorroAfps = response.cuentasAhorroAfps;
        $scope.apvsA = response.apvsA;
        $scope.apvsB = response.apvsB;
        $scope.apvcs = response.apvcs;
        $scope.ccafs = response.ccafs;
        $scope.exCajas = response.exCajas;
        $scope.haberesImp = response.haberesImp;
        $scope.haberesNoImp = response.haberesNoImp;
        $scope.descuentos = response.descuentos;
        $scope.descuentosLegales = response.descuentosLegales;
        $rootScope.cargando = false;
        $scope.cargado = true;
      });
    };

    cargarDatos();

  })
  .controller('FormBuscarCuentaCtrl', function ($scope, $uibModalInstance, objeto, Notification, $rootScope, tipoHaber) {
    $scope.datos = angular.copy(objeto);    

    $scope.tiposCuenta = [
      { id:0, tipo : 'Todos'},
      { id:1, tipo : 'Activos'},
      { id:2, tipo : 'Pasivos'},
      { id:3, tipo : 'Perdidas'},
      { id:4, tipo : 'Ganancias'}
    ];

    $scope.filtro={
      filtro:'',
      tipoCuenta : $scope.tiposCuenta[0]
    };

    $scope.tipoCodigo = function(cuenta){
      if( $scope.filtro.tipoCuenta.id > 0 ){
        if( cuenta.codigo.indexOf($scope.filtro.tipoCuenta.id) === 0 ){
          return true;
        }else{
          return false;
        }
      }else{
        return true;
      }
    };

    $scope.seleccionar=function(cta){
      $uibModalInstance.close(cta);
    };

  })
  .controller('FormCuentaCtrl', function ($scope, tipo, tipoHaber, tipoDescuento, $uibModalInstance, $filter, $uibModal, mesDeTrabajo, cuentas, objeto, Notification, $rootScope, aporte) {

    $scope.cuentas = angular.copy(cuentas);
    $scope.objeto = angular.copy(objeto);
    $scope.tipo = angular.copy(tipo);
    $scope.titulo = 'Modificación de Cuentas de Aportes';
    $scope.encabezado = $scope.objeto.nombre;

    $scope.guardar = function () {
      $rootScope.cargando=true;
      if($scope.tipo==='aporte'){
        var response = aporte.updateCuenta().post({}, $scope.objeto);
      }else if($scope.tipo==='haber'){
        var response = tipoHaber.updateCuenta().post({}, $scope.objeto);
      }else if($scope.tipo==='descuento'){
        var response = tipoDescuento.updateCuenta().post({}, $scope.objeto);
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

    $scope.openCuentas = function(obj){
      var miModal = $uibModal.open({
        animation: $scope.animationsEnabled,
        templateUrl: 'views/forms/form-buscar-cuenta.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'FormBuscarCuentaCtrl',
        size: '700',
        resolve: {
          objeto: function() {
            return $scope.cuentas;
          }
        }
      });
      miModal.result.then(function(cuenta) {
        $scope.objeto.cuenta = cuenta;
      }, function () {
        javascript:void(0);
      });
    }
    
});
