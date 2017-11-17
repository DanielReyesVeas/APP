<?php

class CargasController extends \BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    
    public function index()
    {
        $cargas = Carga::all();
        $listaCargas=array();
        if( $cargas->count() ){
            foreach( $cargas as $carga ){
                $listaCargas[]=array(
                    'id' => $carga->id,
                    'sid' => $carga->sid,
                    'idTrabajador' => $carga->trabajador_id,
                    'parentesco' => $carga->parentesco,
                    'nombreCompleto' => $carga->nombre_completo,
                    'tipoCarga' => array(
                        'id' => $carga->tipoCarga->id,
                        'nombre' => $carga->tipoCarga->nombre
                    ),
                    'fechaNacimiento' => $carga->fecha_nacimiento,
                    'sexo' => $carga->sexo,
                    'esCarga' => $carga->es_carga ? true : false,
                    'esAutorizada' => $carga->es_autorizada ? true : false
                );
            }
        }
        
        
        $datos = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $listaCargas
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
        $errores = Carga::errores($datos);      
        
        if(!$errores){
            $carga = new Carga();
            $carga->sid = Funciones::generarSID();
            $carga->ficha_trabajador_id = $datos['ficha_trabajador_id'];
            $carga->parentesco = $datos['parentesco'];
            $carga->es_carga = $datos['es_carga'];
            $carga->tipo_carga_id = $datos['tipo_carga_id'];
            $carga->rut = $datos['rut'];
            $carga->nombre_completo = $datos['nombre_completo'];
            $carga->fecha_nacimiento = $datos['fecha_nacimiento'];
            $carga->sexo = $datos['sexo'];
            $carga->fecha_autorizacion = 0;
            $carga->es_autorizada = 0;
            $carga->save();
            $respuesta=array(
            	'success' => true,
            	'mensaje' => "La Información fue almacenada correctamente",
            	'sid' => $carga->sid
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
        $carga = Carga::whereSid($sid)->first();

        $datos=array(
            'id' => $carga->id,
            'sid' => $carga->sid,
            'idTrabajador' => $carga->trabajador_id,
            'rut' => $carga->rut,
            'parentesco' => $carga->parentesco,
            'nombreCompleto' => $carga->nombre_completo,
            'fechaNacimiento' => $carga->fecha_nacimiento,
            'tipo' => array(
                'id' => $carga->tipoCarga->id,
                'nombre' => $carga->tipoCarga->nombre
            ),
            'sexo' => $carga->sexo,
            'esCarga' => $carga->es_carga ? true : false,
            'esAutorizada' => $carga->es_autorizada ? true : false,
            'trabajador' => $carga->trabajadorCarga()
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
        $carga = Carga::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $errores = Carga::errores($datos);       
        
        if(!$errores and $carga){
            $carga->trabajador_id = $datos['trabajador_id'];
            $carga->parentesco = $datos['parentesco'];
            $carga->es_carga = $datos['es_carga'];
            $carga->rut = $datos['rut'];
            $carga->nombre_completo = $datos['nombre_completo'];
            $carga->fecha_nacimiento = $datos['fecha_nacimiento'];
            $carga->sexo = $datos['sexo'];
            $carga->save();
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $carga->sid
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
        Carga::whereSid($sid)->delete();
        return Response::json(array('success' => true, 'mensaje' => $mensaje));
    }
    
    public function get_datos_formulario(){
        $datos = array(
            'ficha_trabajador_id' => Input::get('idFichaTrabajador'),
            'parentesco' => Input::get('parentesco'),
            'es_carga' => Input::get('esCarga'),
            'tipo_carga_id' => Input::get('tipo'),
            'rut' => Input::get('rut'),
            'nombre_completo' => Input::get('nombreCompleto'),
            'fecha_nacimiento' => Input::get('fechaNacimiento'),
            'sexo' => Input::get('sexo')
        );
        return $datos;
    }

}