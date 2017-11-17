<?php

class TiposDescuentoController extends \BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    
    public function index()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'accesosTabla' => array(), 'accesosIngreso' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#tabla-descuentos');
        
        $tiposDescuento = TipoDescuento::all()->sortBy("codigo");
        $estructuras = EstructuraDescuento::estructuras();
        $listaDescuentos = array();
        
        if( $tiposDescuento->count() ){
            foreach( $tiposDescuento as $tipoDescuento ){
                if($tipoDescuento->estructura_descuento_id<3){
                    $listaDescuentos[]=array(
                        'id' => $tipoDescuento->id,
                        'sid' => $tipoDescuento->sid,
                        'codigo' => $tipoDescuento->codigo,
                        'nombre' => $tipoDescuento->nombre,
                        'caja' => $tipoDescuento->caja ? true : false,
                        'descripcion' => $tipoDescuento->descripcion
                    );                    
                }
            }
        }
                
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaDescuentos,
            'tipos' => $estructuras
        );
        
        return Response::json($datos);
    }
    
    public function ingresoDescuentos()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'accesosTabla' => array(), 'accesosIngreso' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-descuentos');
        
        $tiposDescuento = TipoDescuento::all()->sortBy("codigo");
        $listaTiposDescuento=array();
        $listaTiposDescuentoAfp=array();
        $listaTiposDescuentoCCAF=array();
        
        if( $tiposDescuento->count() ){
            foreach( $tiposDescuento as $tipoDescuento ){
                if($tipoDescuento->estructura_descuento_id<3){                   
                    $listaTiposDescuento[]=array(
                        'id' => $tipoDescuento->id,
                        'sid' => $tipoDescuento->sid,
                        'codigo' => $tipoDescuento->codigo,
                        'nombre' => $tipoDescuento->nombre
                    );                    
                }else if($tipoDescuento->estructura_descuento_id==6){
                    $listaTiposDescuentoCCAF[]=array(
                        'id' => $tipoDescuento->id,
                        'sid' => $tipoDescuento->sid,
                        'codigo' => $tipoDescuento->codigo,
                        'nombre' => $tipoDescuento->nombre
                    );
                }else if($tipoDescuento->estructura_descuento_id==3){
                    $listaTiposDescuentoAfp[]=array(
                        'id' => $tipoDescuento->id,
                        'sid' => $tipoDescuento->sid,
                        'codigo' => $tipoDescuento->codigo,
                        'nombre' => 'APVC AFP ' . $tipoDescuento->nombreAfp()
                    );
                }else if($tipoDescuento->estructura_descuento_id==7){
                    $listaTiposDescuentoAfp[]=array(
                        'id' => $tipoDescuento->id,
                        'sid' => $tipoDescuento->sid,
                        'codigo' => $tipoDescuento->codigo,
                        'nombre' => 'Cuenta de Ahorro AFP ' . $tipoDescuento->nombreAfp()
                    );
                }
            }
        }
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaTiposDescuento,
            'datosAfp' => $listaTiposDescuentoAfp,
            'datosCCAF' => $listaTiposDescuentoCCAF
        );
        
        return Response::json($datos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $datos = $this->get_datos_formulario();
        $errores = TipoDescuento::errores($datos);      
        
        if(!$errores){
            $tipoDescuento = new TipoDescuento();
            $tipoDescuento->sid = Funciones::generarSID();
            $tipoDescuento->estructura_descuento_id = $datos['estructura_descuento_id'];
            $tipoDescuento->codigo = $datos['codigo'];
            $tipoDescuento->nombre = $datos['nombre'];
            $tipoDescuento->caja = false;
            $tipoDescuento->descripcion = $datos['descripcion'];
            $tipoDescuento->save();
            $respuesta=array(
            	'success' => true,
            	'mensaje' => "La Información fue almacenada correctamente",
            	'sid' => $tipoDescuento->sid
            );
        }else{
            $respuesta=array(
                'success' => false,
                'mensaje' => "La acción no pudo ser completada debido a errores en la información ingresada",
                'errores' => $errores
            );
        } 
        return Response::json($respuesta);
    }    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($sid)
    {
        $tipoDescuento = TipoDescuento::whereSid($sid)->first();
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-descuentos');
        $cuentas = Cuenta::listaCuentas();
        $datosDescuento = null;

        if($sid){
            $datosDescuento=array(
                'id' => $tipoDescuento->id,
                'sid' => $tipoDescuento->sid,
                'codigo' => $tipoDescuento->codigo,
                'nombre' => $tipoDescuento->nombre,
                'caja' => $tipoDescuento->caja ? true : false,
                'descripcion' => $tipoDescuento->descripcion,
                'descuentos' => $tipoDescuento->misDescuentos(),
                'tipo' => array(
                    'id' => $tipoDescuento->estructuraDescuento->id,
                    'nombre' => $tipoDescuento->estructuraDescuento->nombre
                )
            );
        }
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $datosDescuento
        );
        
        return Response::json($datos);
    }
    
    public function cuentaDescuento($sid)
    {
        $tipoDescuento = TipoDescuento::whereSid($sid)->first();
        $cuentas = Cuenta::listaCuentas();
        $datosDescuento = null;

        if($sid){
            if($tipoDescuento->estructuraDescuento->id==3){
                $nombre = 'APVC AFP ' . $tipoDescuento->nombreAfp();
            }else if($tipoDescuento->estructuraDescuento->id==4){
                $nombre = 'APV Régimen A AFP ' . $tipoDescuento->nombreAfp();
            }else if($tipoDescuento->estructuraDescuento->id==5){
                $nombre = 'APV Régimen B ' . $tipoDescuento->nombreAfp();
            }else if($tipoDescuento->estructuraDescuento->id==7){
                $nombre = 'Cuenta de Ahorro AFP ' . $tipoDescuento->nombreAfp();
            }else if($tipoDescuento->estructuraDescuento->id==9){
                $nombre = $tipoDescuento->nombreIsapre();
            }else{
                $nombre = $tipoDescuento->nombre;
            } 
            $datosDescuento=array(
                'id' => $tipoDescuento->id,
                'sid' => $tipoDescuento->sid,
                'codigo' => $tipoDescuento->codigo,
                'nombre' => $nombre,
                'caja' => $tipoDescuento->caja ? true : false,
                'descripcion' => $tipoDescuento->descripcion,
                'tipo' => array(
                    'id' => $tipoDescuento->estructuraDescuento->id,
                    'nombre' => $tipoDescuento->estructuraDescuento->nombre
                )
            );
        }
        
        $datos = array(
            'cuentas' => $cuentas,
            'datos' => $datosDescuento
        );
        
        return Response::json($datos);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($sid)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($sid)
    {
        $tipoDescuento = TipoDescuento::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $errores = TipoDescuento::errores($datos);       
        
        if(!$errores and $tipoDescuento){
            $tipoDescuento->estructura_descuento_id = $datos['estructura_descuento_id'];
            $tipoDescuento->codigo = $datos['codigo'];
            $tipoDescuento->nombre = $datos['nombre'];
            $tipoDescuento->descripcion = $datos['descripcion'];      
            $tipoDescuento->save();
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $tipoDescuento->sid
            );
        }else{
            $respuesta = array(
                'success' => false,
                'mensaje' => "La acción no pudo ser completada debido a errores en la información ingresada",
                'errores' => $errores
            );
        } 
        return Response::json($respuesta);
    }
    
    public function updateCuenta()
    {
        $datos = Input::all();
        $tipoDescuento = TipoDescuento::whereSid($datos['sid'])->first();
        
        $tipoDescuento->cuenta_id = $datos['cuenta']['id'];      
        $tipoDescuento->save();
        
        $respuesta = array(
            'success' => true,
            'mensaje' => "La Información fue actualizada correctamente",
            'sid' => $tipoDescuento->sid
        );
        
        return Response::json($respuesta);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($sid)
    {
        $tipoDescuento = TipoDescuento::whereSid($sid)->first();
        $descuentos = Descuento::where('tipo_descuento_id', $tipoDescuento->id)->get();
        if($descuentos->count()){
            $respuesta = array(
            	'success' => false,
            	'mensaje' => "La acción no pudo ser completada debido a errores en la información ingresada",
                'errores' => 'El tipo de Descuento seleccionado posee datos que dependen de él. <br />Asegúrese que no existan dependencias sobre los datos que desea eliminar.'
            );
        }else{
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue eliminada correctamente",
                'sid' => $tipoDescuento->sid
            );
            $tipoDescuento->delete();
        }
        return Response::json($respuesta);
    }
    
    public function get_datos_formulario(){
        $datos = array(
            'codigo' => Input::get('codigo'),
            'nombre' => Input::get('nombre'),
            'estructura_descuento_id' => Input::get('tipo')['id'],
            'descripcion' => Input::get('descripcion'),
            'cuenta_id' => Input::get('cuenta')['id'],
            'afp_id' => Input::get('afp')['id'],
            'forma_pago' => Input::get('formaPago')['id']
        );
        return $datos;
    }

}