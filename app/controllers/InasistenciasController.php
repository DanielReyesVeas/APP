<?php

class InasistenciasController extends \BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    
    public function index()
    {
        $inasistencias = Inasistencia::all();
        $listaInasistencias=array();
        if( $inasistencias->count() ){
            foreach( $inasistencias as $inasistencia ){
                $listaInasistencias[]=array(
                    'id' => $inasistencia->id,
                    'sid' => $inasistencia->sid,
                    'idTrabajador' => $inasistencia->trabajador_id,
                    'idMes' => $inasistencia->mes_id,
                    'desde' => $inasistencia->desde,
                    'hasta' => $inasistencia->hasta,
                    'dias' => $inasistencia->dias,
                    'motivo' => $inasistencia->motivo,
                    'observacion' => $inasistencia->observacion,
                    'fechaCreacion' => $inasistencia->created_at
                );
            }
        }
        
        
        $datos = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $listaInasistencias
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
        $errores = null;  
        
        if(!$datos['dias']){
            $datos['dias'] = (($datos['hasta'] - $datos['desde']) + 1);
        }
        
        if(!$errores){
            $inasistencia = new Inasistencia();
            $inasistencia->sid = Funciones::generarSID();
            $inasistencia->trabajador_id = $datos['trabajador_id'];
            $inasistencia->mes_id = $datos['mes_id'];
            $inasistencia->dias = $datos['dias'];
            $inasistencia->desde = $datos['desde'];
            $inasistencia->hasta = $datos['hasta'];
            $inasistencia->motivo = $datos['motivo'];
            $inasistencia->observacion = $datos['observacion'];
            $inasistencia->save();
            
            $trabajador = $inasistencia->trabajador;
            $ficha = $trabajador->ficha();
            Logs::crearLog('#ingreso-inasistencias', $trabajador->id, $ficha->nombreCompleto(), 'Create', $inasistencia->id, $inasistencia->dias, NULL);
            
            $respuesta=array(
            	'success' => true,
            	'mensaje' => "La Información fue almacenada correctamente",
            	'id' => $inasistencia->id
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
        $inasistencia = Inasistencia::whereSid($sid)->first();

        $datos=array(
            'id' => $inasistencia->id,
            'sid' => $inasistencia->sid,            
            'idMes' => $inasistencia->mes_id,
            'desde' => $inasistencia->desde,
            'hasta' => $inasistencia->hasta,
            'motivo' => $inasistencia->motivo,
            'observacion' => $inasistencia->observacion,
            'dias' => $inasistencia->dias,        
            'trabajador' => $inasistencia->trabajadorInasistencia()
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
        $inasistencia = Inasistencia::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $errores = Inasistencia::errores($datos);       
        
        if(!$errores and $inasistencia){
            $inasistencia->trabajador_id = $datos['trabajador_id'];
            $inasistencia->mes_id = $datos['mes_id'];
            $inasistencia->desde = $datos['desde'];
            $inasistencia->hasta = $datos['hasta'];
            $inasistencia->dias = $datos['dias'];
            $inasistencia->motivo = $datos['motivo'];
            $inasistencia->observacion = $datos['observacion'];
            $inasistencia->save();
            
            $trabajador = $inasistencia->trabajador;
            $ficha = $trabajador->ficha();
            Logs::crearLog('#ingreso-inasistencias', $trabajador->id, $ficha->nombreCompleto(), 'Update', $inasistencia->id, $inasistencia->dias, NULL);
            
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $inasistencia->sid
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
        $inasistencia = Inasistencia::whereSid($sid)->first();
        
        $trabajador = $inasistencia->trabajador;
        $ficha = $trabajador->ficha();
        Logs::crearLog('#ingreso-inasistencias', $trabajador->id, $ficha->nombreCompleto(), 'Delete', $inasistencia['id'], $inasistencia['dias'], NULL);
        
        $inasistencia->delete();
        
        return Response::json(array('success' => true, 'mensaje' => $mensaje));
    }
    
    public function get_datos_formulario(){
        $datos = array(
            'trabajador_id' => Input::get('idTrabajador'),
            'mes_id' => Input::get('idMes'),
            'desde' => Input::get('desde'),
            'hasta' => Input::get('hasta'),
            'dias' => Input::get('dias'),
            'motivo' => Input::get('motivo'),
            'observacion' => Input::get('observacion')
        );
        return $datos;
    }

}