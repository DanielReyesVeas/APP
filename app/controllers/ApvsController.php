<?php

class ApvsController extends \BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    
    public function index()
    {
        $apvs = Apv::all();
        $listaApvs=array();
        if( $apvs->count() ){
            foreach( $apvs as $apv ){
                $listaApvs[]=array(
                    'id' => $apv->id,
                    'sid' => $apv->sid,
                    'idTrabajador' => $apv->trabajador_id,
                    'idAfp' => $apv->afp_id,
                    'moneda' => $apv->moneda,
                    'monto' => $apv->monto
                );
            }
        }
        
        
        $datos = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $listaApvs
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
        $errores = Apv::errores($datos);      
        
        if(!$errores){
            $apv = new Apv();
            $apv->sid = Funciones::generarSID();
            $apv->trabajador_id = $datos['trabajador_id'];
            $apv->afp_id = $datos['afp_id'];
            $apv->moneda = $datos['moneda'];
            $apv->monto = $datos['monto'];
            $apv->save();
            $respuesta=array(
            	'success' => true,
            	'mensaje' => "La Información fue almacenada correctamente",
            	'sid' => $apv->sid
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
        $apv = Apv::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $errores = Apv::errores($datos);       
        
        if(!$errores and $apv){
            $apv->trabajador_id = $datos['trabajador_id'];
            $apv->afp_id = $datos['afp_id'];
            $apv->moneda = $datos['moneda'];
            $apv->monto = $datos['monto'];
            $apv->save();
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $apv->sid
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
        Apv::whereSid($sid)->delete();
        return Response::json(array('success' => true, 'mensaje' => $mensaje));
    }
    
    public function get_datos_formulario(){
        $datos = array(
            'trabajador_id' => Input::get('idTrabajador'),
            'afp_id' => Input::get('idAfp'),
            'moneda' => Input::get('moneda'),
            'monto' => Input::get('monto')
        );
        return $datos;
    }

}