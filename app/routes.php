<?php
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

//ini_set('safe_mode_exec_dir', 'On');
//ini_set('display_errors', 'On');

ini_set('max_execution_time', 30000);
define('VERSION_SISTEMA', '1.0.9');
ini_set('memory_limit', '3048M');

if(Config::get('cliente.LOCAL')){
    header('Access-Control-Allow-Origin: http://localhost:9000');
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, X-Requested-With, Content-Type');

Route::get('respaldo/basededatos/Q1W2E3Y6T5R4', 'RespaldoController@index');

if( \Session::get('basedatos') ){
    Config::set('database.default', \Session::get('basedatos') );
}else{
    Config::set('database.default', 'principal' );
}

Route::get('/', function(){
    return View::make('index');
});

Route::get('inicio/version', function(){
    // comprobar sesion activa
    if (Auth::guest()) return Response::make('login-error-status', 200);
    return Response::json(array('version' => VERSION_SISTEMA ));
});

Route::get('actualizacion-sql', function(){

    $archivoActualizacion = "actualizacionesSQL/actualizacion-sql-".date("d-m-Y").".sql";

    if( file_exists(public_path()."/".$archivoActualizacion) ) {
        $userBaseDatos = Config::get('cliente.CLIENTE.USUARIO');

        // ejecucion local
       // $userBaseDatos = "root";
        echo "archivo SQL : ".$archivoActualizacion."<br/>";

        // obtengo las empresas
        $empresas = Empresa::all();
        if ($empresas->count()) {
            foreach ($empresas as $empresa) {
                $nombreBaseDatos = $empresa->base_datos;
                shell_exec("mysql -u ".$userBaseDatos." -peasy1q2w3e4r ".$nombreBaseDatos." < ".$archivoActualizacion);
                echo "Base de datos: " . $empresa->base_datos . " fue actualizada <br/>";
            }
        }
        echo "Finalizo el Proceso de Actualización";
    }else{
        echo "No se encontro el archivo de actualizacion";
    }
});

Route::get('empresas/lista-empresas/json', 'EmpresasController@lista_empresas_select');

Route::group(array('before'=>'auth_ajax'), function() {

    Route::post('cambiar-empresa', function(){
        $empresa_id = Input::get('empresa');
        if(!$empresa_id){
            return Response::json(array("success" => true, 'accesos' => array()));
        }
        $urlActual = Input::get('actual');
        $empresa = Empresa::find($empresa_id);
        $empresa->isCME = Empresa::isCME();
        \Session::put('empresa', $empresa);
        if($empresa){
            $recargar=false;
            \Session::put('basedatos', $empresa->base_datos);
            Config::set('database.default', $empresa->base_datos);
            $menuController = new MenuController();
            // se carga el menu con los permisos permitidos para la empresa
            if( Auth::user()->id > 1 ){
                $MENU_USUARIO = $menuController->generarMenuSistema($empresa_id, false);
                // si la posicion actual esta en el menu de la empresa se realiza una recarga
                // de lo contrario se deriva al inicio
                $url = "#".substr($urlActual, 1, 1000);
                $recargar = Auth::user()->comprobarUrlMenuEmpresa($url, $empresa_id);
                $recargar=true;

            }elseif(Auth::user()->id == 1){
                $MENU_USUARIO = $menuController->generarMenuSistema($empresa_id, false);
                $recargar=true;
            }

            $anioActual=date("Y");
            $varSistema = VariableSistema::where('variable', 'ANIO_CONTABLE')->first();
            if($varSistema){
                $anioActual = $varSistema->valor1;
            }
            
            $mesActual = MesDeTrabajo::selectMes();
            \Session::put('mesActivo', $mesActual);
            
            $fecha = \Session::get('mesActivo')->fechaRemuneracion;
            $indicadores = ValorIndicador::valorFecha($fecha);
            
            $empresaActual = array(
                'id' => $empresa->id,
                'logo' => $empresa->logo? "/stories/".$empresa->logo : "images/dashboard/EMPRESAS.png",
                'empresa' => $empresa->razon_social,
                'mesDeTrabajo' => $mesActual,
                'rutFormato' => $empresa->rut_formato(),
                'rut' => $empresa->rut,
                'direccion' => $empresa->direccion,
                'gratificacion' => $empresa->gratificacion,
                'tipoGratificacion' => $empresa->tipo_gratificacion,
                'isMutual' => Empresa::isMutual(),
                'isCaja' => Empresa::isCaja(),
                'isCME' => Empresa::isCME(),
                'comuna' => array(
                    'id' => $empresa->comuna->id,
                    'nombre' => $empresa->comuna->comuna,
                    'provincia' => $empresa->comuna->provincia->provincia,
                    'localidad' => $empresa->comuna->localidad(),
                    'domicilio' => $empresa->domicilio()
                )
            );
            
            if($mesActual){
                $listaMesesDeTrabajo = MesDeTrabajo::listaMesesDeTrabajo();
            }

            return Response::json(array('empresa' => $empresaActual, 'listaMesesDeTrabajo' => $listaMesesDeTrabajo, 'success' => true, "menu" => $MENU_USUARIO, "recargar" => $recargar, "anioActual" => $anioActual, 'indicadores' => $indicadores ));
        }else{  
            return Response::json(array('success' => false));
        }
    });
    
    Route::get('mes-de-trabajo/cuentas/obtener', function(){
        
        $empresa = \Session::get('empresa');
        $isCME = $empresa->isCME;
        $datosConexionCME = array();
        if($isCME){
            //$rutEmpresa = $empresa->rut;
            $rutEmpresa = 111111111;
            $client = new Client(); //GuzzleHttp\Client
            $result = $client->post('http://demo.cme-es.com/rest/rrhh/plan-de-cuentas', [
            //$result = $client->post('http://demo.cme-es.com/empresas', [
                'auth' => ['restfull', '1234'],
                'json' => [
                    'rutEmpresa' => $rutEmpresa
                ],
                'debug' => false
            ]);
            
            $datosConexionCME = $result->json(); 
        }

        
        return Response::json($datosConexionCME);     
    });
    
    Route::get('trabajadores/cuentas/obtener', function(){
        $client = new Client(); //GuzzleHttp\Client
        $result = $client->get('http://demo.rrhh-es.com/centralizacion1/cuentas/obtener');
        
        $datosConexionCME = $result->json(); 
        
        return Response::json($datosConexionCME);     
    });
    
    Route::get('centralizacion/cuentas/obtener', function(){
        
        $lista = array();
        /*$data = Input::all();
        $idMes = $data['fechaRemuneracion'];*/
        $lista = Trabajador::centralizar('2017/09/30', 2);
        
        return Response::json($lista);     
    });
    
    
    Route::get('cuentas/cuentas/obtener', function(){
        
        $lista = array();
        /*$data = Input::all();
        $idMes = $data['fechaRemuneracion'];*/
        $mes = MesDeTrabajo::where('fecha_remuneracion', '2017/06/31')->first();
        $liquidaciones = Liquidacion::where('mes', $mes['mes'])->orderBy('trabajador_apellidos')->get();
        
        foreach($liquidaciones as $liquidacion){
            $detalles = $liquidacion->detallesLiquidacion();
            $lista[] = array(
                'idTrabajador' => $liquidacion->trabajador_id,
                'rut' => $liquidacion->trabajador_rut,
                'nombreCompleto' => $liquidacion->trabajador_nombres . ' ' . $liquidacion->trabajador_apellidos,
                'sueldoBase' => $liquidacion->sueldo_base,
                'sueldo' => $liquidacion->sueldo,
                'haberes' => $detalles['haberes'],
                'descuentos' => $detalles['descuentos'],
                'aportes' => $detalles['aportes'],
                'rentaImponible' => $liquidacion->renta_imponible,
                'sueldoLiquido' => $liquidacion->sueldo_liquido
            );
        }
        
        return Response::json($lista);     
    });
    
    Route::get('empresas/sistemas/obtener', function(){
        
        $client = new Client(); //GuzzleHttp\Client
        //$result = $client->post('http://demo.cme-es.com/empresas/id', [
        $result = $client->get('http://demo.cme-es.com/empresas', [
            'auth' => ['restfull', '1234'],
            'debug' => false
        ]);

        $datosConexionCME = $result->json(); 
        return Response::json($datosConexionCME);     
    });
    
    Route::post('mes-de-trabajo/centralizar/enviar', function(){
        
        $lista = array();
        $data = Input::all();
        $idMes = $data['fechaRemuneracion'];
        $mes = MesDeTrabajo::where('fecha_remuneracion', $idMes)->first();
        $liquidaciones = Liquidacion::where('mes', $mes['mes'])->orderBy('trabajador_apellidos')->get();
        
        foreach($liquidaciones as $liquidacion){
            $detalles = $liquidacion->detallesLiquidacion();
            $lista[] = array(
                'idTrabajador' => $liquidacion->trabajador_id,
                'rut' => $liquidacion->trabajador_rut,
                'nombreCompleto' => $liquidacion->trabajador_nombres . ' ' . $liquidacion->trabajador_apellidos,
                'sueldoBase' => $liquidacion->sueldo_base,
                'sueldo' => $liquidacion->sueldo,
                'haberes' => $detalles['haberes'],
                'descuentos' => $detalles['descuentos'],
                'aportes' => $detalles['aportes'],
                'rentaImponible' => $liquidacion->renta_imponible,
                'sueldoLiquido' => $liquidacion->sueldo_liquido
            );
        }
        
        $datos = array(
            'general' => array(
                'rut' => '112223334',
                'nombre' => 'Usuario Admin',
                'periodo' => '2017-09-01'
            ),
            'detalle' => $lista                
        );
        
        /*$client = new Client(); //GuzzleHttp\Client
        $result = $client->post('http://demo.cme-es.com/empresas/id', [
        $result = $client->get('http://demo.cme-es.com/empresas', [
            'auth' => ['restfull', '1234'],
            'debug' => false
        ]);

        $datosConexionCME = $result->json(); 
        return Response::json($datosConexionCME);  */   
        return Response::json($datos);     
    });
    
    Route::get('crear-empresa', function(){
        
        $whmusername = "root";

# The contents of /root/.accesshash
                    $hash = "3a608b57f517be53f717b12222efe1f6
1b82d5d111ebaf26274527036fa0d675
a2e617ecd2dff57e95cab34231d49e9d
2d6f70dea85f0c08e49dfe133156f24c
644aa27093159c8c1328484ba0a5990e
7df86fbb522b82a15d0cc6bea8d6c366
3c1f370cd08fc988bd3c672ae7320584
45140b665cbfaf190d43c8529d42c2a2
ebc03fd38180c2ec0eed6a9db51dc1e9
daba09c12a350aaf287533c06a80cc04
733196767005fb5c0dd9608364f9910c
06f7ed9aed25b349d23b977f9bd2d273
f0f6af85c4551d743b4b89cf98ff384b
070d88d3d89803c0515601ffb777681e
fe7d4fa37ade0cb94333bfd8638b67db
3c09863eb0bc1afa5d32e8e3c33111d9
96f36670193b91d3811a121493755c20
81ff34c02ba0f9a1e2db80b98449deb4
720b524c3c2200a270e35d0343c633c0
8f247d0e739eca4f9c60ea1a723847bd
935aaad733a86309f06a61e80bc84cbc
fc735006727e13fae7a74a737e6958ff
f8a386b8bff90190c9e66552d41b5793
ba79921a196f78b11dc2842b81a64df3
c85699bdf0e10c5c0039708d4a693969
72b90b49846c65de47c5656ac9b6891a
878abd455bcbef7e2c6fd8a2a86f0995
62ec65222dfb818004f8e49d0c8fd9ee
c139f46406b37dedd4062fea30a5ccec
3355afed2b2d8d597f1ac8fd178892ef
";

                    $query1 = "https://184.154.202.34:2087/json-api/cpanel?user=easysystems&cpanel_jsonapi_module=Mysql&cpanel_jsonapi_func=adddb&cpanel_jsonapi_apiversion=1&arg-0=easysyst_123456";
                    $query2 = "https://184.154.202.34:2087/json-api/cpanel?user=easysystems&cpanel_jsonapi_module=Mysql&cpanel_jsonapi_func=adduserdb&cpanel_jsonapi_apiversion=1&arg-0=easysyst_123456&arg-1=easysyst_rrhh&arg-2=all";
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                    $header[0] = "Authorization: WHM $whmusername:" . preg_replace("'(\r|\n)'", "", $hash);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($curl, CURLOPT_URL, $query1);
                    $result = curl_exec($curl);
                    if ($result == false) {
                        error_log("curl_exec threw error " . curl_error($curl) . " for $query1");
                        die("curl_exec threw error " . curl_error($curl) . " for $query1");
                    }

                    curl_setopt($curl, CURLOPT_URL, $query2);
                    $result = curl_exec($curl);

                    if ($result == false) {
                        error_log("curl_exec threw error " . curl_error($curl) . " for $query2");
                        die("curl_exec threw error " . curl_error($curl) . " for $query2");
                    }
                    curl_close($curl);
        


    

 
    });
                
    Route::post('cambiar-mes-de-trabajo', function(){
        $mesActivo = Input::get('mes');
        $empresa_id = \Session::get('basedatos');
        $empresa = Empresa::where('base_datos', $empresa_id)->first();
        if($empresa){
            // si el anio seleccionado esta permitido
            $listaMesesDeTrabajo=array();
            Config::set('database.default', $empresa->base_datos);
            $varSistema = VariableSistema::where('variable', 'ANIO_CONTABLE')->first();
            if($varSistema){
                $listaMesesDeTrabajo=MesDeTrabajo::listaMesesDeTrabajo();
            }

            $mesActual = MesDeTrabajo::selectMes($mesActivo['id']);
            \Session::put('mesActivo', $mesActual);
            $fecha = \Session::get('mesActivo')->fechaRemuneracion;
            $indicadores = ValorIndicador::valorFecha($fecha);

            if($indicadores){
                $uf = $indicadores->uf;
                $utm = $indicadores->utm;
                $uta = $indicadores->uta;
            }else{
                $uf = null;
                $utm = null;
                $uta = null;
            }
            
            $listaMesesDeTrabajo=MesDeTrabajo::listaMesesDeTrabajo();

            $respuesta = array(
                'success' => true,
                'recargar' => true,
                'mesActual' => $mesActivo,
                'uf' => $uf,
                'utm' => $utm,
                'uta' => $uta,
                'listaMesesDeTrabajo' => $listaMesesDeTrabajo
            );

            return Response::json($respuesta);
        }else{
            return Response::json(array('success' => false, 'mensaje' => 'La referencia a la base de datos de la empresa se ha perdido'));
        }
    });

    Route::get('inicio', function(){
        // comprobar sesion activa
        return Response::json(array('success' => true));
    });

    /* MENU DEL SISTEMA */
    Route::resource('menu', 'MenuController');
    Route::get('menu/opciones-formulario/obtener', 'MenuController@objetosFormulario');
    Route::get('menu/perfiles-usuario/obtener', 'MenuController@menu_perfil_usuario');

    
    /*  VARIABLES DEL SISTEMA   */
    Route::resource('variables-sistema', 'VariablesSistemaController');
    

    /* MODIFICACION DE CONTRASENA Y VISTA A DATOS DEL PERFIL DE USUARIO */
    Route::get('misdatos', 'PerfilController@index');
    Route::post('misdatos', 'PerfilController@actualizar_password');

});


Route::group(array('before'=>'auth_ajax'), function() {
    /*  EMPRESAS    */
    Route::resource('empresas', 'EmpresasController');

    Route::get('empresas/exportar/excel/{version}', "EmpresasController@exportar_excel");

    /* PROVINCIAS Y COMUNAS */
    Route::get('provincias/listado/{region}', 'ProvinciaController@ajaxSelectListaProvincias');
    Route::get('comunas/listado/{provincia}', 'ComunaController@ajaxSelectListaComunas');
    Route::get('comunas/buscador/json', 'ComunaController@buscador_comunas');

    /*  PERFILES DE USUARIOS    */
    Route::resource('perfiles', 'PerfilesController');
    Route::get('perfiles/exportar/excel/{version}', "PerfilesController@exportar_excel");

    /*  FUNCIONARIOS    */
    Route::resource('usuarios', 'UsuariosController');
    Route::get('usuarios/opciones/formulario', 'UsuariosController@objetosFormulario');
    Route::get('usuarios/buscar-rut/{rut}', 'UsuariosController@buscar_rut');
    Route::get('usuarios/exportar/excel/{version}', "UsuariosController@exportar_excel");
    Route::get('usuarios/buscador/json', "UsuariosController@ajax_buscador_usuarios");
    Route::get('usuarios/buscar-todos/json', "UsuariosController@ajax_buscador_todos_usuarios");
    Route::get('usuarios/buscar-vendedor/json', "UsuariosController@ajax_buscador_vendedores");
    Route::get('usuarios/buscar-productManager/json', "UsuariosController@ajax_buscador_productManager");
    Route::get('usuarios/empleados/obtener', "UsuariosController@empleados");
    Route::get('usuario/empleado/obtener/{sid}', "UsuariosController@showUsuario");
    Route::post('usuarios/permisos/cambiar', "UsuariosController@cambiarPermisos");

    Route::get('usuarios/listado-vendedor/json', "UsuariosController@lista_vendedores");
    Route::get('usuarios/listado-productManager/json', "UsuariosController@lista_product_manager");


    /*  DEPARTAMENTOS   */
    Route::resource('departamentos', 'DepartamentosController');
    Route::get('departamentos/opciones/formulario', 'DepartamentosController@objetosFormulario');
    Route::get('departamentos/buscador/json', "DepartamentosController@ajax_buscador_departamentos");
    
    /*  JORNADAS    */
    Route::resource('jornadas', 'JornadasController');
    
    /*  TIPOS_CONTRATO    */
    Route::resource('tipos-contrato', 'TiposContratoController');
    
    /*  APORTES    */
    Route::resource('aportes', 'AportesController');
    Route::post('aportes/cuentas/actualizar', 'AportesController@updateCuenta');
    
    /*  TRABAJADORES    */
    Route::resource('trabajadores', 'TrabajadoresController');
    Route::get('trabajadores/secciones/obtener', 'TrabajadoresController@secciones');
    Route::get('trabajadores/total-inasistencias/obtener', 'TrabajadoresController@trabajadoresInasistencias');
    Route::get('trabajadores/total-licencias/obtener', 'TrabajadoresController@trabajadoresLicencias');
    Route::get('trabajadores/total-horas-extra/obtener', 'TrabajadoresController@trabajadoresHorasExtra');
    Route::get('trabajadores/total-prestamos/obtener', 'TrabajadoresController@trabajadoresPrestamos');
    Route::get('trabajadores/total-cargas-familiares/obtener', 'TrabajadoresController@trabajadoresCargas');
    Route::get('trabajadores/inasistencias/obtener/{sid}', 'TrabajadoresController@trabajadorInasistencias');
    Route::get('trabajadores/licencias/obtener/{sid}', 'TrabajadoresController@trabajadorLicencias');
    Route::get('trabajadores/horas-extra/obtener/{sid}', 'TrabajadoresController@trabajadorHorasExtra');
    Route::get('trabajadores/prestamos/obtener/{sid}', 'TrabajadoresController@trabajadorPrestamos');
    Route::get('trabajadores/documentos/obtener/{sid}', 'TrabajadoresController@trabajadorDocumentos');
    Route::get('trabajadores/cargas-familiares/obtener/{sid}', 'TrabajadoresController@trabajadorCargas');
    Route::get('trabajadores/cargas-familiares-autorizar/obtener/{sid}', 'TrabajadoresController@trabajadorCargasAutorizar');
    Route::post('trabajadores/autorizar-cargas-familiares/generar', 'TrabajadoresController@trabajadorAutorizarCargas');
    Route::get('trabajadores/haberes/obtener/{sid}', 'TrabajadoresController@haberes');
    Route::get('trabajadores/descuentos/obtener/{sid}', 'TrabajadoresController@descuentos');
    Route::get('trabajadores/opciones/afps', 'TrabajadoresController@listaAfps');
    Route::get('trabajadores/input/obtener', 'TrabajadoresController@input');
    Route::get('trabajadores/secciones/formulario', 'TrabajadoresController@seccionesFormulario');
    Route::get('trabajadores/seccion/obtener/{sid}', 'TrabajadoresController@seccion');
    Route::post('trabajadores/liquidacion/generar', 'TrabajadoresController@miLiquidacion');
    Route::get('trabajadores/ingresados/obtener', 'TrabajadoresController@ingresados');
    Route::get('trabajadores/archivo-previred/obtener', 'TrabajadoresController@archivoPrevired');
    Route::get('trabajadores/trabajadores-finiquitos/obtener', 'TrabajadoresController@trabajadoresFiniquitos');
    Route::get('trabajadores/trabajadores-documentos/obtener', 'TrabajadoresController@trabajadoresDocumentos');
    Route::get('trabajadores/vigentes/obtener', 'TrabajadoresController@vigentes');
    Route::get('trabajadores/trabajadores-liquidaciones/obtener', 'TrabajadoresController@trabajadoresLiquidaciones');
    Route::get('trabajadores/planilla-costo-empresa/obtener', 'TrabajadoresController@planillaCostoEmpresa');
    Route::get('trabajadores/trabajadores-cartas-notificacion/obtener', 'TrabajadoresController@trabajadoresCartasNotificacion');
    Route::get('trabajadores/trabajadores-cartas-notificacion/obtener', 'TrabajadoresController@trabajadoresCartasNotificacion');
    Route::get('trabajadores/trabajadores-certificados/obtener', 'TrabajadoresController@trabajadoresCertificados');
    Route::get('trabajadores/trabajadores-vacaciones/obtener', 'TrabajadoresController@trabajadoresVacaciones');
    Route::get('trabajadores/trabajadores-semana-corrida/obtener', 'TrabajadoresController@trabajadoresSemanaCorrida');
    Route::get('trabajadores/vacaciones/obtener/{sid}', 'TrabajadoresController@trabajadorVacaciones');
    Route::get('trabajadores/cartas-notificacion/obtener/{sid}', 'TrabajadoresController@trabajadorCartasNotificacion');
    Route::get('trabajadores/finiquitos/obtener/{sid}', 'TrabajadoresController@trabajadorFiniquitos');
    Route::get('trabajadores/certificados/obtener/{sid}', 'TrabajadoresController@trabajadorCertificados');
    Route::get('trabajadores/contratos/obtener/{sid}', 'TrabajadoresController@trabajadorContratos');
    Route::get('trabajadores/documento/obtener/{sid}', 'DocumentosController@documentoPDF');
    Route::get('trabajadores/reajuste/obtener', 'TrabajadoresController@reajuste');    
    Route::post('trabajadores/reajuste/masivo', 'TrabajadoresController@reajustarRMI');
    Route::post('trabajadores/finiquitar/generar', 'TrabajadoresController@finiquitar');
    Route::post('trabajadores/carta-notificacion/generar', 'TrabajadoresController@cartaNotificacion');
    Route::post('trabajadores/contrato/generar', 'TrabajadoresController@contrato');
    Route::post('trabajadores/finiquito/generar', 'TrabajadoresController@finiquito');
    Route::post('trabajadores/certificado/generar', 'TrabajadoresController@certificado');
    Route::get('trabajadores/miLiquidacion/generar/{sid}', 'TrabajadoresController@miLiquidacion');
    Route::post('trabajadores/libro-remuneraciones/generar-excel', 'TrabajadoresController@generarLibroExcel');
    Route::get('trabajadores/libro-remuneraciones/descargar-excel/{nombre}', 'TrabajadoresController@descargarLibroExcel');
    Route::post('trabajadores/nomina-bancaria/generar-excel', 'TrabajadoresController@generarNominaExcel');
    Route::get('trabajadores/nomina-bancaria/descargar-excel/{nombre}', 'TrabajadoresController@descargarNominaExcel');
    Route::post('trabajadores/planilla-costo-empresa/generar-excel', 'TrabajadoresController@generarPlanillaExcel');
    Route::get('trabajadores/planilla-costo-empresa/descargar-excel/{nombre}', 'TrabajadoresController@descargarPlanillaExcel');
    Route::get('trabajadores/archivo-previred/descargar-excel', 'TrabajadoresController@descargarArchivoPreviredExcel');
    Route::get('trabajadores/planilla/descargar-excel/{tipo}', 'TrabajadoresController@descargarPlantilla');
    Route::get('trabajadores/planilla-trabajadores/descargar', 'TrabajadoresController@descargarPlantillaTrabajadores');
    Route::get('trabajadores/liquidacion/descargar-pdf/{nombre}', 'TrabajadoresController@liquidacionPDF');
    Route::post('trabajadores/semana-corrida/actualizar', 'TrabajadoresController@updateSemanaCorrida');
    Route::post('trabajadores/planilla/importar', 'TrabajadoresController@importarPlanilla');
    Route::post('trabajadores/generar-ingreso/masivo', 'TrabajadoresController@generarIngresoMasivo');
    
    
    /*   NACIONALIDADES    */
    Route::resource('nacionalidades', 'NacionalidadesController');
    
    /*   ESTADOS_CIVILES    */
    Route::resource('estados-civiles', 'EstadosCivilesController');
    
    /*   CARGOS    */
    Route::resource('cargos', 'CargosController');
    
    /*   AREAS_A_CARGO    */
    Route::resource('areas-a-cargo', 'AreasACargoController');
    
    /*   TÍTULOS    */
    Route::resource('titulos', 'TitulosController');
    
    /*   CUENTAS    */
    Route::resource('cuentas', 'CuentasController');
    
    /*   BANCOS    */
    Route::resource('bancos', 'BancosController');
    
    /*   AFPS    */
    Route::resource('afps', 'AfpsController');
    
    /*   ISAPRES    */
    Route::resource('isapres', 'IsapresController');

    /*   TIPOS_CARGA    */
    Route::resource('tipos-carga', 'TiposCargaController');
    
    /*   TIPOS_HABER    */
    Route::resource('tipos-haber', 'TiposHaberController');
    Route::get('tipos-haber/ingreso-haberes/obtener', 'TiposHaberController@ingresoHaberes');    
    Route::post('tipos-haber/cuentas/actualizar', 'TiposHaberController@updateCuenta');

    /*   TIPOS_DESCUENTO    */
    Route::resource('tipos-descuento', 'TiposDescuentoController');
    Route::get('tipos-descuento/ingreso-descuentos/obtener', 'TiposDescuentoController@ingresoDescuentos');
    Route::get('tipos-descuento/cuentas/obtener/{sid}', 'TiposDescuentoController@cuentaDescuento');
    Route::post('tipos-descuento/cuentas/actualizar', 'TiposDescuentoController@updateCuenta');
    
    /*   TIPOS_DOCUMENTO    */
    Route::resource('tipos-documento', 'TiposDocumentoController');
    
    /*   APVS    */
    Route::resource('apvs', 'ApvsController');
    
    /*   HABERES    */
    Route::resource('haberes', 'HaberesController');
    Route::post('haberes/ingreso/masivo', 'HaberesController@ingresoMasivo');
    Route::post('haberes/planilla/importar', 'HaberesController@importarPlanilla');
    Route::post('haberes/generar-ingreso/masivo', 'HaberesController@generarIngresoMasivo');
    
    /*   DESCUENTOS    */
    Route::resource('descuentos', 'DescuentosController');
    Route::post('descuentos/ingreso/masivo', 'DescuentosController@ingresoMasivo');
    Route::post('descuentos/planilla/importar', 'DescuentosController@importarPlanilla');
    Route::post('descuentos/generar-ingreso/masivo', 'DescuentosController@generarIngresoMasivo');
    
    /*   INASISTENCIAS    */
    Route::resource('inasistencias', 'InasistenciasController');
        
    /*   LICENCIAS    */
    Route::resource('licencias', 'LicenciasController');
    
    /*   HORAS_EXTRA    */
    Route::resource('horas-extra', 'HorasExtraController');
    
    /*   TABLAS_ESTRUCTURANTES  */
    Route::get('tablas-estructurantes/obtener/tablas', 'TablasEstructurantesController@tablas');
    
    /*   TABLA_GLOBAL_MENSUAL  */
    Route::get('tabla-global-mensual/tablas/obtener', 'TablaGlobalMensualController@tablas');
    Route::post('tabla-global-mensual/modificar/masivo', 'TablaGlobalMensualController@modificar');
    
    /*   PRESTAMOS    */
    Route::resource('prestamos', 'PrestamosController');
    
    /*   CUOTAS    */
    Route::resource('cuotas', 'CuotasController');
    
    /*   CARGAS    */
    Route::resource('cargas', 'CargasController');
    
    /*   SECCIONES    */
    Route::resource('secciones', 'SeccionesController');
    
    /*   TABLAS    */
    Route::resource('tablas', 'TablasController');
    
    /*   TABLA_IMPUESTO_UNICO    */
    Route::resource('tabla-impuesto-unico', 'TablaImpuestoUnicoController');
    Route::post('tabla-impuesto-unico/modificar/masivo', 'TablaImpuestoUnicoController@modificar');
    
    /*   TASAS_CAJAS_EX_REGIMEN   */
    Route::resource('tasas-cajas-ex-regimen', 'TasasCajasExRegimenController');
    Route::post('tasas-cajas-ex-regimen/modificar/masivo', 'TasasCajasExRegimenController@modificar');
    
    /*   RECAUDADORES    */
    Route::resource('recaudadores', 'RecaudadoresController');
    
    /*   CODIGOS    */
    Route::resource('codigos', 'CodigosController');
    Route::post('codigos/ingreso/masivo', 'CodigosController@ingresoMasivo');
    Route::post('codigos/update/masivo', 'CodigosController@updateMasivo');
    
    /*   GLOSAS    */
    Route::resource('glosas', 'GlosasController');
    
    /*   MES_DE_TRABAJO    */
    Route::resource('mes-de-trabajo', 'MesDeTrabajoController');    
    
    /*   ANIOS    */
    Route::resource('anios', 'AniosRemuneracionesController');
    Route::get('anio-remuneracion/datos-cierre/obtener', 'AniosRemuneracionesController@datosCierre');
    Route::post('anio-remuneracion/cerrar-meses/generar', 'AniosRemuneracionesController@cerrarMeses');
    Route::post('anio-remuneracion/feriados/generar', 'AniosRemuneracionesController@feriados');
    Route::get('anio-remuneracion/calendario/obtener', 'AniosRemuneracionesController@calendario');
    Route::post('anio-remuneracion/gratificacion/generar', 'AniosRemuneracionesController@gratificacion');
    
    /*   VALORES_INDICADORES    */
    Route::resource('valores-indicadores', 'ValoresIndicadoresController');
    Route::post('valor-indicador/ingreso/masivo', 'ValoresIndicadoresController@ingresoMasivo');
    Route::post('valor-indicador/modificar/masivo', 'ValoresIndicadoresController@modificar');
    Route::get('valores-indicadores/indicadores/obtener/{fecha}', 'ValoresIndicadoresController@indicadores');
    
    /*   LIQUIDACIONES    */
    Route::resource('liquidaciones', 'LiquidacionesController');
    Route::resource('liquidaciones/libro-remuneraciones/obtener', 'LiquidacionesController@libroRemuneraciones');
    Route::post('liquidaciones/eliminar/masivo', 'LiquidacionesController@eliminarMasivo');
    
    /*   LIBRO_REMUNERACIONES    */
    Route::resource('libro-remuneraciones', 'LibrosRemuneracionesController');   
    
    /*  CAUSALES_FINIQUITO    */
    Route::resource('causales-finiquito', 'CausalesFiniquitoController');
    
    /*  CAUSALES_NOTIFICACION    */
    Route::resource('causales-notificacion', 'CausalesNotificacionController');
    
    /*  CLAUSULAS_CONTRATO    */
    Route::resource('clausulas-contrato', 'ClausulasContratoController');
    Route::get('clausulas-contrato/plantilla-contrato/obtener/{sid}', 'ClausulasContratoController@listaClausulasContrato');
    
    /*  CLAUSULAS_FINIQUITO    */
    Route::resource('clausulas-finiquito', 'ClausulasFiniquitoController');
    Route::get('clausulas-finiquito/plantilla-finiquito/obtener/{sid}', 'ClausulasFiniquitoController@listaClausulasFiniquito');
    
    /*  TRAMOS_HORAS_EXTRA    */
    Route::resource('tramos-horas-extra', 'TramosHorasExtraController');
    
    /*  PLANTILLAS_CARTAS_NOTIFICACION    */
    Route::resource('plantillas-cartas-notificacion', 'PlantillasCartasNotificacionController');
    
    /*  CARTAS_NOTIFICACION    */
    Route::resource('cartas-notificacion', 'CartasNotificacionController');
    
    /*  CONTRATOS    */
    Route::resource('contratos', 'ContratosController');
    
    /*  CERTIFICADOS    */
    Route::resource('certificados', 'CertificadosController');
    
    /*  PLANTILLAS_CONTRATOS    */
    Route::resource('plantillas-contratos', 'PlantillasContratosController');
    
    /*  PLANTILLAS_FINIQUITOS    */
    Route::resource('plantillas-finiquitos', 'PlantillasFiniquitosController');
    
    /*  PLANTILLAS_CERTIFICADOS    */
    Route::resource('plantillas-certificados', 'PlantillasCertificadosController');
    
    /*  FINIQUITOS    */
    Route::resource('finiquitos', 'FiniquitosController');
    Route::post('finiquitos/calculo/obtener', 'FiniquitosController@calcular');
    
    /*  DOCUMENTOS    */
    Route::resource('documentos', 'DocumentosController');
    Route::post('documentos/archivo/importar', 'DocumentosController@importarDocumento');
    Route::post('documentos/archivo/subir', 'DocumentosController@subirDocumento');
    
    /*  DOCUMENTOS_EMPRESA    */
    Route::resource('documentos-empresa', 'DocumentosEmpresaController');
    Route::post('documentos-empresa/archivo/importar', 'DocumentosEmpresaController@importarDocumento');
    Route::post('documentos-empresa/archivo/subir', 'DocumentosEmpresaController@subirDocumento');
    Route::get('documentos-empresa/documento/descargar-documento/{sid}', 'DocumentosEmpresaController@documentoPDF');
    
    /*  VACACIONES    */
    Route::resource('vacaciones', 'VacacionesController');
    Route::post('vacaciones/calculo/obtener', 'VacacionesController@calcularVacaciones');  
    
    /*   CENTROS_COSTO    */
    Route::resource('centros-costo', 'CentrosCostoController');
    
    /*   TIENDAS    */
    Route::resource('tiendas', 'TiendasController');
    
    /*   CUENTAS    */
    Route::resource('cuentas', 'CuentasController');

});


Route::post('login', function (){
    $_SESSION['basedatos']="";
    $indice=0;
    $userdata = array(
        'username' => Input::get('username'),
        'password' => Input::get('password')
    );
    if ( Auth::attempt($userdata) ){
        
        $empresaDestino = Input::get('empresa') ? Input::get('empresa') : array();
        if( array_key_exists('id', $empresaDestino) ){
            $empresaDestinoId = $empresaDestino['id'];
        }else{
            $empresaDestinoId = 0;
        }
        $empresa_id=0;
        $listaEmpresasPermisos=array();
        $menuController = new MenuController();
        if(Auth::user()->id > 1){
            $empresas=Auth::user()->listaEmpresasPerfil();
            $listaEmpresas=array();
            if( !in_array(100000, $empresas) ) {
                $empresas = Empresa::whereIn('id', $empresas)->orderBy('razon_social', 'ASC')->get();
            }else{
                $empresas = Empresa::orderBy('razon_social', 'ASC')->get();
            }
            if($empresas->count()){
                foreach( $empresas as $empresa ){
                    $listaEmpresas[]=array(
                        'id' => $empresa->id,
                        'empresa' => $empresa->razon_social,
                        'rutFormato' => $empresa->rut_formato(),
                        'rut' => $empresa->rut
                    );
                    $listaEmpresasPermisos[]=$empresa->id;
                }
            }

            if( $empresaDestinoId > 0 and in_array($empresaDestinoId, $listaEmpresasPermisos) ) {
                $empresa_id = $empresaDestinoId;
            }
            if(count($listaEmpresas)){
                $empresa_id=$empresas[0]['id'];
                $empresa = Empresa::find($empresa_id);
                \Session::put('basedatos', $empresa->base_datos);
                $mesActual = MesDeTrabajo::selectMes();
                \Session::put('mesActivo', $mesActual);  
                $empresa->isCME = Empresa::isCME();
                \Session::put('empresa', $empresa);
                $MENU_USUARIO = $menuController->generarMenuSistema( $empresa_id, false );
            }else{
                $opciones = MenuSistema::where('administrador', '!=', '2')->get();
                $MENU_USUARIO = $menuController->generarMenuSistema( 0, false );
            }            

        }else{
            $listaEmpresas=array();
            $empresas = Empresa::orderBy('razon_social', 'ASC')->get();
            if($empresas->count()){
                foreach( $empresas as $empresa ){
                    $listaEmpresas[]=array(
                        'id' => $empresa->id,
                        'empresa' => $empresa->razon_social,
                        'rutFormato' => $empresa->rut_formato(),
                        'rut' => $empresa->rut
                    );
                    $listaEmpresasPermisos[]=$empresa->id;
                }
            }
            // el SUPERADMINISTRADOR carga el menu completamente
            if( !count($listaEmpresas) ){
                //no existen empresas por lo tanto solo se carga las opciones de administracion
                $opciones = MenuSistema::where('administrador', '!=', '2')->get();
                $MENU_USUARIO = $menuController->generarMenuSistema( 0, true );
            }else{                
                // se carga el menu completo
                if( $empresaDestinoId > 0 and in_array($empresaDestinoId, $listaEmpresasPermisos) ) {
                    $empresa_id = $empresaDestinoId;
                }else{
                    $empresa_id=$empresas[0]['id'];
                }
                $empresa = Empresa::find($empresa_id);
                $empresa->isCME = Empresa::isCME();
                \Session::put('empresa', $empresa);
                \Session::put('basedatos', $empresa->base_datos);
                                
                $MENU_USUARIO = $menuController->generarMenuSistema( $empresa_id, false );
            }
        }

        $varGlobal = VariableGlobal::where('variable', 'EMPRESAS')->first();
        if(!$varGlobal){
            $varGlobal = new VariableGlobal();
            $varGlobal->variable = "EMPRESAS";
            $varGlobal->valor = 5;
            $varGlobal->save();
        }

        $empresaInicial="";
        if( $empresa_id ){
            $empresa = Empresa::find($empresa_id);
            Config::set('database.default', $empresa->base_datos);
            
            $mesActual = MesDeTrabajo::selectMes();
            \Session::put('mesActivo', $mesActual);
            
            if($mesActual){
                $listaMesesDeTrabajo = MesDeTrabajo::listaMesesDeTrabajo();
            }
            $empresa->isCME = Empresa::isCME();
            \Session::put('empresa', $empresa);
            $empresaInicial = array(
                'id' => $empresa->id,
                'logo' => $empresa->logo? "/stories/".$empresa->logo : "images/dashboard/EMPRESAS.png",
                'empresa' => $empresa->razon_social,
                'mesDeTrabajo' => $mesActual,
                'rutFormato' => $empresa->rut_formato(),
                'rut' => $empresa->rut,
                'direccion' => $empresa->direccion,
                'gratificacion' => $empresa->gratificacion,
                'tipoGratificacion' => $empresa->tipo_gratificacion,
                'isCME' => Empresa::isCME(),
                'isMutual' => Empresa::isMutual(),
                'isCaja' => Empresa::isCaja(),
                'comuna' => array(
                    'id' => $empresa->comuna->id,
                    'nombre' => $empresa->comuna->comuna,
                    'provincia' => $empresa->comuna->provincia->provincia,
                    'localidad' => $empresa->comuna->localidad(),
                    'domicilio' => $empresa->domicilio()
                )
            );
                        
        }                        
        
        if(isset($mesActual)){
            $fecha = $mesActual->fechaRemuneracion;
            $indicadores = ValorIndicador::valorFecha($fecha);
        }

        if(isset($indicadores)){
            $uf = $indicadores->uf;
            $utm = $indicadores->utm;
            $uta = $indicadores->uta;
        }else{
            $uf = null;
            $utm = null;
            $uta = null;
        }
        if(!isset($listaMesesDeTrabajo)){
            $listaMesesDeTrabajo = array();
        }

        return Response::json(array("success" => true, "uf" => $uf, "utm" => $utm, "uta" => $uta, "listaMesesDeTrabajo" => $listaMesesDeTrabajo, "max" => $varGlobal->valor, "empresas" => $listaEmpresas, "empresa" => $empresaInicial? $empresaInicial : "" , "menu" => $MENU_USUARIO['menu'], "inicio" => str_replace("#", "/", $MENU_USUARIO['inicio']), 'accesos' => $MENU_USUARIO['secciones'], "nombre" => ucwords( mb_strtolower( Auth::user()->funcionario->nombre_completo()) ) , "imagen" => Auth::user()->funcionario->fotografia ? URL::to('stories/min_'.Auth::user()->funcionario->fotografia) : "images/usuario.png" , "usuario" => ucwords ( mb_strtolower( Auth::user()->funcionario->nombre_completo() ) ), "uID" => Auth::user()->id ));
    }else{
        return Response::json(array("success" => false, "mensaje" => "El Nombre de Usuario y/o la Contraseña son incorrectos"));
    }
});

