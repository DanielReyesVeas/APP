<?php

class DescuentosController extends \BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    
    public function index()
    {
        $descuentos = Descuento::all();
        $listaDescuentos=array();
        if( $descuentos->count() ){
            foreach( $descuentos as $descuento ){
                $listaDescuentos[]=array(
                    'id' => $descuento->id,
                    'sid' => $descuento->sid,
                    'idTrabajador' => $descuento->trabajador_id,
                    'idTipoDescuento' => $descuento->tipo_descuento_id,
                    'desde' => $descuento->desde,
                    'hasta' => $descuento->hasta,
                    'tipo' => array(
                        'id' => $descuento->tipoDescuento ? $descuento->tipoDescuento->id : "",
                        'nombre' => $descuento->tipoDescuento ? $descuento->tipoDescuento->nombre : ""
                    ),
                    'mes' => array(
                        'id' => $descuento->mesDeTrabajo ? $descuento->mesDeTrabajo->id : "",
                        'nombre' => $descuento->mesDeTrabajo ? $descuento->mesDeTrabajo->nombre : "",
                        'mes' => $descuento->mesDeTrabajo ? $descuento->mesDeTrabajo->mes : ""
                    ), 
                    'porMes' => $descuento->por_mes,
                    'rangoMeses' => $descuento->rango_meses,
                    'permanente' => $descuento->permanente,
                    'todosAnios' => $descuento->todos_anios,
                    'moneda' => $descuento->moneda,
                    'monto' => $descuento->monto
                );
            }
        }
        
        
        $datos = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $listaDescuentos
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
        $errores = Descuento::errores($datos);      
        
        if(!$errores){
            $descuento = new Descuento();
            $descuento->sid = Funciones::generarSID();
            $descuento->trabajador_id = $datos['trabajador_id'];
            $descuento->tipo_descuento_id = $datos['tipo_descuento_id'];
            $descuento->mes_id = $datos['mes_id'];
            $descuento->mes = $datos['mes'];
            $descuento->desde = $datos['desde'];
            $descuento->hasta = $datos['hasta'];
            $descuento->por_mes = $datos['por_mes'];
            $descuento->rango_meses = $datos['rango_meses'];
            $descuento->permanente = $datos['permanente'];
            $descuento->todos_anios = $datos['todos_anios'];
            $descuento->moneda = $datos['moneda'];
            $descuento->monto = $datos['monto'];
            $descuento->save();
            $respuesta=array(
            	'success' => true,
            	'mensaje' => "La Información fue almacenada correctamente",
            	'sid' => $descuento->sid
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
    
    
    public function ingresoMasivo()
    {
        $datos = Input::all();
    
        foreach($datos['descuentos'] as $des){
            $errores = Descuento::errores($des);   
            if(!$errores){
                $descuento = new Descuento();
                $descuento->sid = Funciones::generarSID();
                $descuento->trabajador_id = $des['trabajador_id'];
                $descuento->tipo_descuento_id = $des['tipo_descuento_id'];
                $descuento->mes_id = $des['mes_id'];
                $descuento->mes = $des['mes'];
                $descuento->desde = $des['desde'];
                $descuento->hasta = $des['hasta'];
                $descuento->por_mes = $des['por_mes'];
                $descuento->rango_meses = $des['rango_meses'];
                $descuento->permanente = $des['permanente'];
                $descuento->todos_anios = $des['todos_anios'];
                $descuento->moneda = $des['moneda'];
                $descuento->monto = $des['monto'];
                $descuento->save(); 
                $respuesta=array(
                    'success' => true,
                    'mensaje' => "La Información fue almacenada correctamente"
                );
            }else{
                $respuesta=array(
                    'success' => false,
                    'mensaje' => "La acción no pudo ser completada debido a errores en la información ingresada",
                    'errores' => $errores
                );
            }
        }
        
        return Response::json($respuesta);
    }
    
    public function generarIngresoMasivo()
    {
        $datos = Input::all();
        $descuentoIngresar = $datos['descuento'];
        $trabajadores = $datos['trabajadores'];
        $idMes = null;
        $mes = null;
        $permanente = false;
        $porMes = false;
        $rangoMeses = false;
        $desde = null;
        $hasta = null;
        $idTipoDescuento = $descuentoIngresar['id'];
        
        foreach($trabajadores as $trabajador){
            $rut = $trabajador['trabajador']['rut'];
            $idTrabajador = Trabajador::where('rut', $rut)->first()->id;
            if(!$trabajador['trabajador']['descuento']['temporalidad']){
                $mes = \Session::get('mesActivo');
                $idMes = $mes->id;
                $mes = $mes->mes;
                $porMes = true;
            }else if($trabajador['trabajador']['descuento']['temporalidad']=='permanente'){
                $permanente = true;
            }else{
                $rangoMeses = true;
                $meses = Funciones::rangoMeses($trabajador['trabajador']['descuento']['temporalidad']);
                $desde = $meses->desde;
                $hasta = $meses->hasta;
            }
            
            $descuento = new Descuento();
            $descuento->sid = Funciones::generarSID();
            $descuento->trabajador_id = $idTrabajador;
            $descuento->mes_id = $idMes;
            $descuento->mes = $mes;
            $descuento->tipo_descuento_id = $idTipoDescuento;
            $descuento->permanente = $permanente;
            $descuento->por_mes = $porMes;
            $descuento->todos_anios = false;
            $descuento->rango_meses = $rangoMeses;
            $descuento->desde = $desde;
            $descuento->hasta = $hasta;
            $descuento->moneda = $trabajador['trabajador']['descuento']['moneda'];
            $descuento->monto = $trabajador['trabajador']['descuento']['monto'];
            $descuento->save();                        
        }
        
        $respuesta=array(
            'success' => true,
            'mensaje' => "La Información fue almacenada correctamente"
        );
        
        return Response::json($respuesta);
    }
    
    public function importarPlanilla()
    {
        $insert = array();
        $descuentoIngresar = Input::get('descuento');
        
        if(Input::hasFile('file')){            
            $file = Input::file('file')->getRealPath();
            $data = Excel::load($file, function($reader){                
            })->get();
            if(!empty($data) && $data->count()){
                foreach($data as $key => $value){
                    if(isset($value->rut) && isset($value->moneda) && isset($value->monto)){
                        $insert[] = array(
                            'rut' => $value->rut,              
                            'moneda' => $value->moneda,                 
                            'monto' => $value->monto,                 
                            'temporalidad' => $value->temporalidad               
                        );
                    }else{
                        $errores = array();
                        $errores[] = 'El formato no corresponde con el archivo de la planilla. Por favor vuelva a descargar la planilla.';
                    }
                }
            }
        }
        
        if(!isset($errores)){
            $errores = $this->comprobarErrores($insert);
        }
        
        if(!$errores){            
            $tabla = array();
            foreach($insert as $dato){
                $trabajador = Trabajador::where('rut', $dato['rut'])->first();
                $nombreCompleto = $trabajador->ficha()->nombreCompleto();
                
                $tabla[] = array(
                    'trabajador' => array(
                        'id' => $trabajador->id,
                        'rut' => $dato['rut'],
                        'rutFormato' => Funciones::formatear_rut($dato['rut']),
                        'nombreCompleto' => $nombreCompleto,
                        'descuento' => array(
                            'moneda' => $dato['moneda'],
                            'monto' => $dato['monto'],
                            'temporalidad' => $dato['temporalidad']
                        )
                    )                        
                );
            }
            
            $respuesta=array(
                'success' => true,
                'mensaje' => "La Información fue almacenada correctamente",
                'datos' => $tabla,
                'descuento' => $descuentoIngresar
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
    
    public function comprobarErrores($datos)
    {
        $trabajadores = Trabajador::all();
        $ruts = $trabajadores->lists('rut');
        $listaErrores = array();
        
        foreach($datos as $dato){
            if($dato){
                if(!in_array($dato['rut'], $ruts)){
                    $listaErrores[] = 'El trabajador con RUT: ' . Funciones::formatear_rut($dato['rut']) . ' no existe.';
                }
                if($dato['moneda']!='$' && $dato['moneda']!='UF' && $dato['moneda']!='UTM'){
                    $listaErrores[] = 'Formato de Moneda "' . $dato['moneda'] . '" incorrecto, recuerda que los formatos son $, UF o UTM.';
                }
                if(!is_numeric($dato['monto'])){
                    $listaErrores[] = 'Formato del Monto "' . $dato['monto'] . '" incorrecto, recuerda que este campo acepta sólo valores numéricos.';
                }
                if(strtolower($dato['temporalidad'])!='permanente' && $dato['temporalidad']!=null && !Funciones::comprobarFecha($dato['temporalidad'])){
                    $listaErrores[] = 'Formato de Temporalidad "' . $dato['temporalidad'] . '" incorrecto, recuerda que este campo puede estar en blanco, "permanente" o en un rango de fechas "03-2017 - 06-2017".';
                }
            }
        }
        
        return $listaErrores;
    }    
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($sid)
    {
        $descuento = Descuento::whereSid($sid)->first();

        $datos=array(
            'id' => $descuento->id,
            'sid' => $descuento->sid,            
            'mes' => array(
                'id' => $descuento->mesDeTrabajo ? $descuento->mesDeTrabajo->id : "",
                'sid' => $descuento->mesDeTrabajo ? $descuento->mesDeTrabajo->sid : "",
                'nombre' => $descuento->mesDeTrabajo ? $descuento->mesDeTrabajo->nombre : "",
                'mes' => $descuento->mesDeTrabajo ? $descuento->mesDeTrabajo->mes : ""
            ), 
            'desde' => $descuento->desde,
            'hasta' => $descuento->hasta,
            'porMes' => $descuento->por_mes ? true : false,
            'rangoMeses' => $descuento->rango_meses ? true : false,
            'permanente' => $descuento->permanente ? true : false,
            'todosAnios' => $descuento->todos_anios ? true : false,
            'monto' => $descuento->monto,
            'moneda' => $descuento->moneda,
            'tipo' => array(
                'nombre' => $descuento->tipoDescuento->nombre,
                'sid' => $descuento->tipoDescuento->sid,
                'id' => $descuento->tipoDescuento->id,
            ),            
            'trabajador' => $descuento->trabajadorDescuento()
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
        $descuento = Descuento::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $errores = Descuento::errores($datos);       
        
        if(!$errores and $descuento){
            $descuento->trabajador_id = $datos['trabajador_id'];
            $descuento->tipo_descuento_id = $datos['tipo_descuento_id'];
            $descuento->mes_id = $datos['mes_id'];
            $descuento->mes = $datos['mes'];
            $descuento->desde = $datos['desde'];
            $descuento->hasta = $datos['hasta'];
            $descuento->por_mes = $datos['por_mes'];
            $descuento->rango_meses = $datos['rango_meses'];
            $descuento->permanente = $datos['permanente'];
            $descuento->todos_anios = $datos['todos_anios'];
            $descuento->moneda = $datos['moneda'];
            $descuento->monto = $datos['monto'];
            $descuento->save();
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $descuento->sid
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($sid)
    {
        $mensaje="La Información fue eliminada correctamente";
        Descuento::whereSid($sid)->delete();
        return Response::json(array('success' => true, 'mensaje' => $mensaje));
    }
    
    public function get_datos_formulario(){
        $datos = array(
            'trabajador_id' => Input::get('idTrabajador'),
            'tipo_descuento_id' => Input::get('idTipoDescuento'),
            'mes_id' => Input::get('idMes'),
            'mes' => Input::get('mes'),
            'desde' => Input::get('desde'),
            'hasta' => Input::get('hasta'),
            'por_mes' => Input::get('porMes'),
            'rango_meses' => Input::get('rangoMeses'),
            'permanente' => Input::get('permanente'),
            'todos_anios' => Input::get('todosAnios'),
            'moneda' => Input::get('moneda'),
            'monto' => Input::get('monto')
        );
        return $datos;
    }

}