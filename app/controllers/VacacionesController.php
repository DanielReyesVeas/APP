<?php

class VacacionesController extends \BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    
    public function index()
    {
        $vacaciones = Vacaciones::all();
        $listaVacaciones=array();
        if( $vacaciones->count() ){
            foreach( $vacaciones as $vac ){
                $listaVacaciones[]=array(
                    'id' => $vac->id,
                    'dias' => $vac->dias
                );
            }
        }
        
        
        $datos = array(
            'datos' => $listaVacaciones
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
        
        if(!$errores){
            $vacaciones = new Vacaciones();
            $vacaciones->sid = Funciones::generarSID();;
            $vacaciones->trabajador_id = $datos['trabajador_id'];
            $vacaciones->mes_id = $datos['mes_id'];
            $vacaciones->dias = $datos['dias'];
            $vacaciones->save();
            $respuesta=array(
            	'success' => true,
            	'mensaje' => "La Información fue almacenada correctamente",
            	'sid' => $vacaciones->sid
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
        //
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
        $vacaciones = Vacaciones::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $errores = Vacaciones::errores($datos);       
        
        if(!$errores and $vacaciones){
            $vacaciones->dias = $datos['dias'];
            $vacaciones->save();
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $vacaciones->sid
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
        Vacaciones::whereSid($sid)->first()->delete();
        return Response::json(array('success' => true, 'mensaje' => $mensaje));
    }
    
    public function get_datos_formulario(){
        $datos = array(
            'dias' => Input::get('dias')
        );
        return $datos;
    }

}