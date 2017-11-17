<?php

class HaberesController extends \BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    
    public function index()
    {        
        $haberes = Haber::all();
        $listaHaberes=array();
        if( $haberes->count() ){
            foreach( $haberes as $haber ){
                $listaHaberes[]=array(
                    'id' => $haber->id,
                    'sid' => $haber->sid,
                    'trabajador' => $haber->trabajadorHaber(),
                    'mes' => array(
                        'id' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->id : "",
                        'nombre' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->nombre : ""
                    ),
                    'tipo' => array(
                        'id' => $haber->tipoHaber ? $haber->tipoHaber->id : "",
                        'imponible' => $haber->tipoHaber ? true : false,
                        'nombre' => $haber->tipoHaber ? $haber->tipoHaber->nombre : ""
                    ),
                    'desde' => $haber->desde,
                    'hasta' => $haber->hasta,
                    'moneda' => $haber->moneda,
                    'porMes' => $haber->por_mes ? true : false,
                    'rangoMeses' => $haber->rango_meses ? true : false,
                    'permanente' => $haber->permanente ? true : false,
                    'todosAnios' => $haber->todos_anios ? true : false,
                    'monto' => $haber->monto
                );
            }
        }
        
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaHaberes
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
        $errores = Haber::errores($datos);      
        
        if(!$errores){
            $haber = new Haber();
            $haber->sid = Funciones::generarSID();
            $haber->trabajador_id = $datos['trabajador_id'];
            $haber->mes_id = $datos['mes_id'];
            $haber->mes = $datos['mes'];
            $haber->tipo_haber_id = $datos['tipo_haber_id'];
            $haber->por_mes = $datos['por_mes'];
            $haber->rango_meses = $datos['rango_meses'];
            $haber->permanente = $datos['permanente'];
            $haber->todos_anios = $datos['todos_anios'];
            $haber->desde = $datos['desde'];
            $haber->hasta = $datos['hasta'];
            $haber->moneda = $datos['moneda'];
            $haber->monto = $datos['monto'];
            $haber->save();
            $respuesta=array(
            	'success' => true,
            	'mensaje' => "La Información fue almacenada correctamente",
            	'sid' => $haber->sid
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
    
    public function generarIngresoMasivo()
    {
        $datos = Input::all();
        $haberIngresar = $datos['haber'];
        $trabajadores = $datos['trabajadores'];
        $ingresoMasivo = array();
        $idMes = null;
        $mes = null;
        $permanente = false;
        $porMes = false;
        $rangoMeses = false;
        $desde = null;
        $hasta = null;
        $idTipoHaber = $haberIngresar['id'];
        
        foreach($trabajadores as $trabajador){
            $rut = $trabajador['trabajador']['rut'];
            $idTrabajador = Trabajador::where('rut', $rut)->first()->id;
            if(!$trabajador['trabajador']['haber']['temporalidad']){
                $mes = \Session::get('mesActivo');
                $idMes = $mes->id;
                $mes = $mes->mes;
                $porMes = true;
            }else if($trabajador['trabajador']['haber']['temporalidad']=='permanente'){
                $permanente = true;
            }else{
                $rangoMeses = true;
                $meses = Funciones::rangoMeses($trabajador['trabajador']['haber']['temporalidad']);
                $desde = $meses->desde;
                $hasta = $meses->hasta;
            }
            
            $haber = new Haber();
            $haber->sid = Funciones::generarSID();
            $haber->trabajador_id = $idTrabajador;
            $haber->mes_id = $idMes;
            $haber->mes = $mes;
            $haber->tipo_haber_id = $idTipoHaber;
            $haber->permanente = $permanente;
            $haber->por_mes = $porMes;
            $haber->todos_anios = false;
            $haber->rango_meses = $rangoMeses;
            $haber->desde = $desde;
            $haber->hasta = $hasta;
            $haber->moneda = $trabajador['trabajador']['haber']['moneda'];
            $haber->monto = $trabajador['trabajador']['haber']['monto'];
            $haber->save();                        
        }
        
        $respuesta=array(
            'success' => true,
            'mensaje' => "La Información fue almacenada correctamente"
        );
        
        return Response::json($respuesta);
    }
    
    public function ingresoMasivo()
    {
        $datos = Input::all();
    
        foreach($datos['haberes'] as $hab){
            $errores = Haber::errores($hab);   
            if(!$errores){
                $haber = new Haber();
                $haber->sid = Funciones::generarSID();
                $haber->trabajador_id = $hab['trabajador_id'];
                $haber->mes_id = $hab['mes_id'];
                $haber->mes = $hab['mes'];
                $haber->tipo_haber_id = $hab['tipo_haber_id'];
                $haber->por_mes = $hab['por_mes'];
                $haber->rango_meses = $hab['rango_meses'];
                $haber->permanente = $hab['permanente'];
                $haber->todos_anios = $hab['todos_anios'];
                $haber->desde = $hab['desde'];
                $haber->hasta = $hab['hasta'];
                $haber->moneda = $hab['moneda'];
                $haber->monto = $hab['monto'];
                $haber->save(); 
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
    
    public function importarPlanilla()
    {
        $insert = array();
        $haberIngresar = Input::get('haber');
        
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
                        'haber' => array(
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
                'haber' => $haberIngresar
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
        $haber = Haber::whereSid($sid)->first();

        $datos=array(
            'id' => $haber->id,
            'sid' => $haber->sid,            
            'monto' => $haber->monto,
            'moneda' => $haber->moneda,
            'tipo' => array(
                'id' => $haber->tipoHaber ? $haber->tipoHaber->id : "",
                'sid' => $haber->tipoHaber ? $haber->tipoHaber->sid : "",
                'imponible' => $haber->tipoHaber ? true : false,
                'nombre' => $haber->tipoHaber ? $haber->tipoHaber->nombre : ""
            ),
            'mes' => array(
                'id' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->id : "",
                'nombre' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->nombre : "",
                'mes' => $haber->mesDeTrabajo ? $haber->mesDeTrabajo->mes : ""
            ),     
            'porMes' => $haber->por_mes ? true : false,
            'rangoMeses' => $haber->rango_meses ? true : false,
            'permanente' => $haber->permanente ? true : false,
            'todosAnios' => $haber->todos_anios ? true : false,
            'desde' => $haber->desde,
            'hasta' => $haber->hasta,
            'trabajador' => $haber->trabajadorHaber()
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
        $haber = Haber::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $errores = Haber::errores($datos);       
        
        if(!$errores and $haber){
            $haber->mes_id = $datos['mes_id'];
            $haber->mes = $datos['mes'];
            $haber->tipo_haber_id = $datos['tipo_haber_id'];
            $haber->trabajador_id = $datos['trabajador_id'];
            $haber->por_mes = $datos['por_mes'];
            $haber->rango_meses = $datos['rango_meses'];
            $haber->permanente = $datos['permanente'];
            $haber->todos_anios = $datos['todos_anios'];
            $haber->desde = $datos['desde'];
            $haber->hasta = $datos['hasta'];
            $haber->moneda = $datos['moneda'];
            $haber->monto = $datos['monto'];
            $haber->save();
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $haber->sid
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
        Haber::whereSid($sid)->delete();
        return Response::json(array('success' => true, 'mensaje' => $mensaje));
    }
    
    public function get_datos_formulario(){
        $datos = array(
            'trabajador_id' => Input::get('idTrabajador'),
            'tipo_haber_id' => Input::get('idTipoHaber'),
            'mes_id' => Input::get('idMes'),
            'mes' => Input::get('mes'),
            'por_mes' => Input::get('porMes'),
            'rango_meses' => Input::get('rangoMeses'),
            'permanente' => Input::get('permanente'),
            'todos_anios' => Input::get('todosAnios'),
            'desde' => Input::get('desde'),
            'hasta' => Input::get('hasta'),
            'moneda' => Input::get('moneda'),
            'monto' => Input::get('monto')
        );
        return $datos;
    }

}