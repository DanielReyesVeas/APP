<?php

class MesDeTrabajoController extends \BaseController {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    
    public function index()
    {
        $listaMesesDeTrabajo = array();
    	$mesesDeTrabajo = MesDeTrabajo::orderBy('id', 'DESC')->get();
    	if( $mesesDeTrabajo->count() ){
            foreach( $mesesDeTrabajo as $mesDeTrabajo ){
                $listaMesesDeTrabajo[]=array(
                    'id' => $mesDeTrabajo->id,
                    'mes' => $mesDeTrabajo->mes,
                    'nombre' => $mesDeTrabajo->nombre,
                    'anio' => $mesDeTrabajo->anioRemuneracion->anio,
                    'idAnio' => $mesDeTrabajo->anio_id,
                    'fechaRemuneracion' => $mesDeTrabajo->fecha_remuneracion,
                    'isIngresado' => $mesDeTrabajo->estado()
                );
            }
    	}
        
        $datos = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $listaMesesDeTrabajo
        );
    	return $datos;
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
        $empresa =  \Session::get('empresa');
        $datos = $this->get_datos_formulario();
        $errores = MesDeTrabajo::errores($datos);      
        Config::set('database.default', 'admin' );                
        $mes = DB::table('meses')->where('mes', $datos['mes'])->first();
        Config::set('database.default', $empresa->base_datos );
        
        if(!$errores){
            $mesDeTrabajo = new MesDeTrabajo();
            $mesDeTrabajo->id = $mes->id;
            $mesDeTrabajo->sid = Funciones::generarSID();
            $mesDeTrabajo->mes = $datos['mes'];
            $mesDeTrabajo->nombre = $datos['nombre'];
            $mesDeTrabajo->fecha_remuneracion = $datos['fecha_remuneracion'];
            $mesDeTrabajo->anio_id = $datos['anio_id'];
            $mesDeTrabajo->save();
            Trabajador::crearSemanasCorridas($mesDeTrabajo);
            Trabajador::calcularVacaciones($mesDeTrabajo);
            ValorIndicador::crearIndicadores($mesDeTrabajo->mes, $datos['fecha_remuneracion']);
            Config::set('database.default', $empresa->base_datos );
            $respuesta=array(
            	'success' => true,
            	'mensaje' => "La Información fue almacenada correctamente",
            	'mes' => array(
                    'id' => $mes->id,
                    'nombre' => $mesDeTrabajo->nombre,
                    'fechaRemuneracion' => $mesDeTrabajo->fecha_remuneracion,
                    'idAnio' => $mesDeTrabajo->anio_id,
                    'mes' => $mesDeTrabajo->mes,
                    'isIngresado' => true,
                    'anio' => $mesDeTrabajo->anioRemuneracion->anio,
                    'mesActivo' => $mesDeTrabajo->nombre . ' ' . $mesDeTrabajo->anioRemuneracion->anio
                )
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
        $mesDeTrabajo = MesDeTrabajo::whereSid($sid)->first();

        $datosMesDeTrabajo=array(
            'id' => $mesDeTrabajo->id,
            'mes' => $mesDeTrabajo->mes,
            'anio' => $mesDeTrabajo->anioRemuneracion->anio,
            'idAnio' => $mesDeTrabajo->anio_id,
            'nombre' => $mesDeTrabajo->nombre,
            'fechaRemuneracion' => $mesDeTrabajo->fecha_remuneracion
        );        
        
        $datos = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $datosMesDeTrabajo
        );
        
        return Response::json($datos);
    }
    
    public function centralizar()
    {
        $mesDeTrabajo = MesDeTrabajo::whereSid($sid)->first();

        $datosMesDeTrabajo=array(
            'id' => $mesDeTrabajo->id,
            'mes' => $mesDeTrabajo->mes,
            'anio' => $mesDeTrabajo->anioRemuneracion->anio,
            'idAnio' => $mesDeTrabajo->anio_id,
            'nombre' => $mesDeTrabajo->nombre,
            'fechaRemuneracion' => $mesDeTrabajo->fecha_remuneracion
        );        
        
        $datos = array(
            'accesos' => array(
                'ver' => true,
                'editar' => true
            ),
            'datos' => $datosMesDeTrabajo
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
        $mesDeTrabajo = MesDeTrabajo::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $errores = MesDeTrabajo::errores($datos);       
        
        if(!$errores and $mesDeTrabajo){
            $mesDeTrabajo->mes = $datos['mes'];
            $mesDeTrabajo->nombre = $datos['nombre'];
            $mesDeTrabajo->fecha_remuneracion = $datos['fecha_remuneracion'];
            $mesDeTrabajo->anio = $datos['anio'];
            $mesDeTrabajo->save();
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $mesDeTrabajo->id
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
        MesDeTrabajo::whereSid($sid)->first()->delete();
        return Response::json(array('success' => true, 'mensaje' => $mensaje));
    }
    
    public function get_datos_formulario(){
        $datos = array(
            'mes' => Input::get('mes'),
            'nombre' => Input::get('nombre'),
            'fecha_remuneracion' => Input::get('fechaRemuneracion'),
            'anio_id' => Input::get('idAnio')
        );
        
        return $datos;
    }

}