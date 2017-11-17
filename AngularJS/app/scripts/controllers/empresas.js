'use strict';

/**
 * @ngdoc function
 * @name angularjsApp.controller:EmpresasCtrl
 * @description
 * # EmpresasCtrl
 * Controller of the angularjsApp
 */
angular.module('angularjsApp')
	.controller('EmpresasCtrl', function ($scope, $location, $route, $filter, $rootScope, $timeout, $uibModal, Notification, empresa, constantes, $localStorage) {
   
    $scope.datos = [];
    $scope.constantes = constantes;
    var empresaActual = $rootScope.globals.currentUser.empresa;
    var mutuales;
    var cajas;

    $scope.getEmpresas = function(){
      $rootScope.cargando = true;
      var datos = empresa.empresasSistema().get();
      datos.$promise.then(function(response){
        $rootScope.cargando = false;      
      });
    }

    $scope.open = function (emp) {
      var modalInstance = $uibModal.open({
        animation: $scope.animationsEnabled,
        templateUrl: 'views/forms/form-nueva-empresa.html?v=' + $filter('date')(new Date(), 'ddMMyyyyHHmmss'),
        controller: 'ModalFormEmpresaCtrl',
        size: 'lg',
        backdrop:'static',
        resolve: {
          objeto : function(){
            return emp;
          },
          mutuales : function(){
            return mutuales;
          },
          cajas : function(){
            return cajas;
          }
        }
      });
      modalInstance.result.then(function (obj) {
        Notification.success({message: obj.mensaje, title: 'Mensaje del Sistema'});
        if(obj.crear){
          console.log('cambiar1')
          $scope.cambiarEmpresa(obj.empresa);
        }else if(obj.editar){          
          if(obj.empresa.id===empresaActual.id){
            console.log('cambiar2')
            $scope.cambiarEmpresa(obj.empresa);
          }else{
            console.log('nocambiar')
            $scope.cargarDatos();
          }
        }
      }, function () {
        javascript:void(0);
      });
    };

    $scope.cargarDatos = function(){
      $rootScope.cargando=true;
      var listado=false;
      var emp = empresa.datos().get();
      emp.$promise.then(function(response){
        $scope.datos = response.empresas;
        mutuales = response.mutuales;
        cajas = response.cajas;

        delete $localStorage.globals.currentUser.empresas;
        delete $rootScope.globals.currentUser.empresas;
        $localStorage.globals.currentUser.empresas=[];
        $rootScope.globals.currentUser.empresas=[];

        $rootScope.cargando=true;
        var datosEmp = empresa.listaSelect().query();
        datosEmp.$promise.then(function(response2){

          if( response2.length > 0 ){
            for( var ind in response2 ){
              if( response2[ind].id ){
                $localStorage.globals.currentUser.empresas.push( response2[ind] );
                if( $rootScope.globals.currentUser.empresa.id === response2[ind].id ){
                  listado=true;
                }
              }
            }
          }
          /*
          if(!listado && response2.length > 0 ){
              $scope.$parent.cambiarEmpresa( response2[0].id );
          }
          */
          $rootScope.cargando=false;
        });
      });
    };

    

    $scope.editar = function(emp){
      $rootScope.cargando=true;
      var datos = empresa.datos().get({id:emp.id});
      datos.$promise.then(function(response){
        $scope.open( response, mutuales, cajas );
        $rootScope.cargando=false;
      });
    };

    $scope.eliminar = function(emp){
      $rootScope.cargando=true;
      $scope.result = empresa.datos().delete({ id: emp.id });
      $scope.result.$promise.then( function(response){
        if(response.success){
          Notification.success({message: response.mensaje, title:'Notificación del Sistema'});
          var empresa = { id : null };
          if(response.empresas.length>0){
            empresa = response.empresas[0];
          }
          $scope.cambiarEmpresa(empresa);
        }else{
          Notification.error({message: response.mensaje, title: 'Mensaje del Sistema'});
        }
      });
    };

    $scope.toolTipEdicion = function( nombre ){
    	return 'Editar los datos de la Empresa: <b>' + nombre + '</b>';
    };

    $scope.cargarDatos();
      
  })
  .controller('ModalFormEmpresaCtrl', function ($scope, $route, mutuales, cajas, $rootScope, $timeout, $http, $uibModal, $uibModalInstance, $filter, Notification, objeto, empresa, constantes, $localStorage) {

    var anioActual = parseInt( $filter('date')(new Date(), 'yyyy') );
    $scope.mutuales = angular.copy(mutuales);
    $scope.cajas = angular.copy(cajas);
    $scope.anios=[];
    $scope.meses = angular.copy(constantes.MESES);    

    for( var i=anioActual; i >= 2010; i--){
      $scope.anios.push(i);
    }

    if(objeto){
      $scope.objeto = objeto;
      $scope.objeto.fotografiaBase64='';      
      $scope.objeto.mutual = $filter('filter')( $scope.mutuales, {id :  $scope.objeto.mutual.id }, true )[0];
      $scope.objeto.caja = $filter('filter')( $scope.cajas, {id :  $scope.objeto.caja.id }, true )[0];
    }else{
      $scope.objeto = { anioInicial : $scope.anios[0], mesInicial : $scope.meses[0], sis : true, tasaFijaMutual : 0.95, tasaAdicionalMutual : 0, tipoGratificacion : 'm', gratificacion : 'e', zona : 0.00, topeGratificacion : 4.75 };
      $scope.objeto.fotografiaBase64='';
    }
    console.log($scope.objeto)
    
    $scope.imagen={};
    $scope.erroresDatos = {};

    $scope.empresas = [
      { id:100000, empresa:'Ninguna' }
    ];

    var empresas = empresa.listaSelect().query();
    empresas.$promise.then(function(response){
      if( response.length > 0 ){
        for( var ind in response ){
          if( response[ind].id ){
            $scope.empresas.push(response[ind]);
          }
        }
      }
    });

    if( $scope.objeto.logo ){
      $scope.logo = constantes.URL + 'stories/' + objeto.logo;
    }else{
      $scope.logo = 'images/dashboard/EMPRESAS.png';
    }

    $scope.getComunas = function(val){
      return $http.get( constantes.URL + 'comunas/buscador/json', {
        params: {
          termino: val
        }
      }).then(function(response){
        return response.data.map(function(item){
          return item;
        });
      });
    };

    $scope.ocultarProgressBar = function(){
      $scope.creandoImagen=false;
      $scope.valorPB=0;
    };

    $scope.cambiarGratificacion = function(tipo){
      if(tipo === 'mensual'){
        if($scope.objeto.gratificacionMensual){
          $scope.objeto.gratificacionAnual = false;          
        }else{
          $scope.objeto.gratificacionAnual = true;          
        }
      }
      if(tipo === 'anual'){
        if($scope.objeto.gratificacionAnual){
          $scope.objeto.gratificacionMensual = false;
        }else{
          $scope.objeto.gratificacionMensual = true;          
        }
      }
      console.log($scope.objeto.gratificacion)
    }

    $scope.obtenerImagenB64 = function(){
      $scope.objeto.fotografiaBase64 = '';
      $scope.creandoImagen=true;
      $timeout(function(){
        var base64;
        var fileReader = new FileReader();
        fileReader.onload = function (event){
          base64 = event.target.result;
          $scope.objeto.fotografiaBase64 = base64;
          $timeout(function(){
              $scope.ocultarProgressBar();
          }, 250);
        };
        fileReader.readAsDataURL( $scope.imagen.flow.files[0].file );      
      }, 1000);
    };

    $scope.guardar = function () {
      $rootScope.cargando=true;
      var response;

      if( $scope.objeto.id ){
        response = empresa.datos().update({id:$scope.objeto.id}, $scope.objeto);
      }else{
        $scope.objeto.mesInicial.fecha = $scope.objeto.anioInicial + "-" + $scope.objeto.mesInicial.fecha;
        response = empresa.datos().create({}, $scope.objeto);
      }
      response.$promise.then(
        function(response){
          if(response.success){                        
            if( response.modMenu ){
              delete $localStorage.globals.currentUser.menu;
              delete $rootScope.globals.currentUser.menu;
              delete $localStorage.globals.currentUser.accesos;
              delete $rootScope.globals.currentUser.accesos;
              delete $localStorage.globals.currentUser.default;
              delete $rootScope.globals.currentUser.default;
              delete $localStorage.globals.currentUser.empresa;
              delete $rootScope.globals.currentUser.empresa;
              $localStorage.globals.currentUser.menu=response.menu.menu;
              $rootScope.globals.currentUser.menu=response.menu.menu;

              $localStorage.globals.currentUser.accesos=response.menu.accesos;
              $rootScope.globals.currentUser.accesos=response.menu.accesos;

              $localStorage.globals.currentUser.default=response.menu.inicio;
              $rootScope.globals.currentUser.default=response.menu.inicio;

              $localStorage.globals.currentUser.empresa=response.menu.empresa;
              $rootScope.globals.currentUser.empresa=response.menu.empresa;
            }
            $uibModalInstance.close({ mensaje : response.mensaje, empresa : response, crear : response.crear, editar : response.editar });            
          }else{
            Notification.error({message: response.mensaje, title:'Notificación del Sistema'});
            $scope.erroresDatos = response.errores;
          }
        }
      );
    };

    $scope.cancel = function () {
      $uibModalInstance.dismiss('cancel');
    };

    $scope.errores = function(name){
      var s = $scope.form[name];
      return s.$invalid && s.$touched;
    };
  });






/*


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


CREATE TABLE IF NOT EXISTS `afps` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



INSERT INTO `afps` (`codigo`,`nombre`, `updated_at`, `created_at`) VALUES
('96572800-7', 'Banmédica','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('96856780-2', 'Consalud','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('96502530-8', 'Vida Tres','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('76296619-0', 'Colmena','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('96501450-0', 'Isapre Cruz Blanca S.A.','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('61603000-0', 'Fonasa','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('79566720-2', 'Chuquicamata','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('96504160-5', 'Óptima Isapre','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('76334370-7', 'Institución de Salud Previsional Fusat','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('71235700-2', 'Isapre Bco. Estado','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('96522500-5', 'Más Vida','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('89441300-K', 'Río Blanco','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('79906120-1', 'San Lorenzo Isapre','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('76521250-2', 'Cruz del Norte','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('70360100-6', 'Asociación Chilena de Seguridad ACHS','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('70285100-9', 'Mutual de Seguridad CCHC','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('70015580-3', 'Instituto de Seguridad del Trabajo IST','2017-07-03 16:12:03', '2015-07-03 21:57:09'),
('61533000-0', 'Instituto de Seguridad Laboral ISL','2017-07-03 16:12:03', '2015-07-03 21:57:09');






*/