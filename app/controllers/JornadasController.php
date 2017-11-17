<?php

class JornadasController extends \BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    
    public function index()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'permisos' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#jornadas');
        $jornadas = Jornada::all();
        $listaJornadas=array();
        if( $jornadas->count() ){
            foreach( $jornadas as $jornada ){
                $listaJornadas[]=array(
                    'id' => $jornada->id,
                    'sid' => $jornada->sid,
                    'nombre' => $jornada->nombre,
                    'tramoHoraExtra' => array(
                        'id' => $jornada->tramoHoraExtra->id,
                        'jornada' => $jornada->tramoHoraExtra->jornada,
                        'factor' => $jornada->tramoHoraExtra->factor
                    ),
                    'numeroHoras' => $jornada->numero_horas
                );
            }
        }
        
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaJornadas
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
        $errores = Jornada::errores($datos);      
        
        if(!$errores){
            $jornada = new Jornada();
            $jornada->sid = Funciones::generarSID();
            $jornada->nombre = $datos['nombre'];
            $jornada->numero_horas = $datos['numero_horas'];
            $jornada->tramo_hora_extra_id = $datos['tramo_hora_extra_id'];
            $jornada->save();
            $respuesta=array(
            	'success' => true,
            	'mensaje' => "La Información fue almacenada correctamente",
            	'sid' => $jornada->sid
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
        $jornada = Jornada::whereSid($sid)->first();
        $tramosHorasExtra = TramoHoraExtra::all();
        
        $datosJornada=array(
            'id' => $jornada->id,
            'sid' => $jornada->sid,
            'nombre' => $jornada->nombre,
            'numeroHoras' => $jornada->numero_horas,
            'tramoHoraExtra' => array(
                'id' => $jornada->tramoHoraExtra->id,
                'jornada' => $jornada->tramoHoraExtra->jornada,
                'factor' => $jornada->tramoHoraExtra->factor
            )
        );        
        
        $datos = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $datosJornada,
            'tramosHorasExtra' => $tramosHorasExtra
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
        $jornada = Jornada::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $errores = Jornada::errores($datos);       
        
        if(!$errores and $jornada){
            $jornada->nombre = $datos['nombre'];
            $jornada->numero_horas = $datos['numero_horas'];
            $jornada->tramo_hora_extra_id = $datos['tramo_hora_extra_id'];
            $jornada->save();
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $jornada->sid
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
        Jornada::whereSid($sid)->delete();
        return Response::json(array('success' => true, 'mensaje' => $mensaje));
    }
    
    public function get_datos_formulario(){
        $datos = array(
            'nombre' => Input::get('nombre'),
            'tramo_hora_extra_id' => Input::get('idTramoHoraExtra'),
            'numero_horas' => Input::get('numeroHoras')
        );
        return $datos;
    }

}