<?php

class CentrosCostoController extends \BaseController {
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
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#centro-costo');
        $centrosCosto = CentroCosto::all();
        $listaCentrosCosto=array();
        if( $centrosCosto->count() ){
            foreach( $centrosCosto as $centroCosto ){
                $listaCentrosCosto[]=array(
                    'id' => $centroCosto->id,
                    'sid' => $centroCosto->sid,
                    'codigo' => $centroCosto->codigo,
                    'nombre' => $centroCosto->nombre
                );
            }
        }
        
        
        $datos = array(
            'accesos' => $permisos,
            'datos' => $listaCentrosCosto
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
        $errores = CentroCosto::errores($datos);      
        
        if(!$errores){
            $centroCosto = new CentroCosto();
            $centroCosto->nombre = $datos['nombre'];
            $centroCosto->codigo = $datos['codigo'];
            $centroCosto->sid = Funciones::generarSID();
            $centroCosto->save();
            $respuesta=array(
            	'success' => true,
            	'mensaje' => "La Información fue almacenada correctamente",
            	'id' => $centroCosto->id
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
        $centroCosto = CentroCosto::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $errores = CentroCosto::errores($datos);       
        
        if(!$errores and $centroCosto){
            $centroCosto->nombre = $datos['nombre'];
            $centroCosto->codigo = $datos['codigo'];
            $centroCosto->save();
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $centroCosto->sid
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
        CentroCosto::whereSid($sid)->delete();
        return Response::json(array('success' => true, 'mensaje' => $mensaje));
    }
    
    public function get_datos_formulario(){
        $datos = array(
            'codigo' => Input::get('codigo'),
            'nombre' => Input::get('nombre')
        );
        return $datos;
    }

}