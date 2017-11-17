<?php

class TiposHaberController extends \BaseController {
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
        $permisosTabla = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#tabla-haberes');
        $permisosIngreso = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-haberes');
        $tiposHaber = TipoHaber::all()->sortBy("codigo");
        $listaImponibles=array();
        $listaNoImponibles=array();
        $cuentas = Cuenta::listaCuentas();
        
        if($tiposHaber->count()){
            foreach($tiposHaber as $tipoHaber){
                if($tipoHaber->id>15){
                    if($tipoHaber->imponible){
                        $listaImponibles[]=array(
                            'id' => $tipoHaber->id,
                            'sid' => $tipoHaber->sid,
                            'codigo' => $tipoHaber->codigo,
                            'nombre' => $tipoHaber->nombre,
                            'tributable' => $tipoHaber->tributable ? true : false,
                            'calculaHorasExtras' => $tipoHaber->calcula_horas_extras ? true : false,
                            'proporcionalDiasTrabajados' => $tipoHaber->proporcional_dias_trabajados ? true : false,
                            'calculaSemanaCorrida' => $tipoHaber->calcula_semana_corrida ? true : false,
                            'imponible' => $tipoHaber->imponible ? true : false,
                            'gratificacion' => $tipoHaber->gratificacion ? true : false,
                            'cuenta' => $tipoHaber->cuenta($cuentas)
                        );
                    }else{
                        $listaNoImponibles[]=array(
                            'id' => $tipoHaber->id,
                            'sid' => $tipoHaber->sid,
                            'codigo' => $tipoHaber->codigo,
                            'nombre' => $tipoHaber->nombre,
                            'tributable' => $tipoHaber->tributable ? true : false,
                            'calculaHorasExtras' => $tipoHaber->calcula_horas_extras ? true : false,
                            'proporcionalDiasTrabajados' => $tipoHaber->proporcional_dias_trabajados ? true : false,
                            'calculaSemanaCorrida' => $tipoHaber->calcula_semana_corrida ? true : false,
                            'imponible' => $tipoHaber->imponible ? true : false,
                            'gratificacion' => $tipoHaber->gratificacion ? true : false,
                            'cuenta' => $tipoHaber->cuenta($cuentas)
                        );
                    }
                }
            }
        }
        
        
        $datos = array(
            'accesosTabla' => $permisosTabla,
            'accesosIngreso' => $permisosIngreso,
            'imponibles' => $listaImponibles,
            'noImponibles' => $listaNoImponibles,
            'isCuentas' => TipoHaber::isCuentas()
        );
        
        return Response::json($datos);
    }
    
    public function ingresoHaberes()
    {
        if(!\Session::get('empresa')){
            return Response::json(array('datos' => array(), 'accesosTabla' => array(), 'accesosIngreso' => array()));
        }
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-haberes');
        $tiposHaber = TipoHaber::all()->sortBy("codigo");
        $listaImponibles=array();
        $listaNoImponibles=array();
        
        if($tiposHaber->count()){
            foreach($tiposHaber as $tipoHaber){
                if($tipoHaber->id>15 || $tipoHaber->id==10 || $tipoHaber->id==11){
                    if($tipoHaber->imponible){
                        $listaImponibles[]=array(
                            'id' => $tipoHaber->id,
                            'sid' => $tipoHaber->sid,
                            'codigo' => $tipoHaber->codigo,
                            'nombre' => $tipoHaber->nombre
                        );
                    }else{
                        $listaNoImponibles[]=array(
                            'id' => $tipoHaber->id,
                            'sid' => $tipoHaber->sid,
                            'codigo' => $tipoHaber->codigo,
                            'nombre' => $tipoHaber->nombre
                        );
                    }
                }
            }
        }
        
        
        $datos = array(
            'accesos' => $permisos,
            'imponibles' => $listaImponibles,
            'noImponibles' => $listaNoImponibles
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
        $errores = TipoHaber::errores($datos);      
        
        if(!$errores){
            $tipoHaber = new TipoHaber();
            $tipoHaber->sid = Funciones::generarSID();
            $tipoHaber->codigo = $datos['codigo'];
            $tipoHaber->nombre = $datos['nombre'];
            $tipoHaber->tributable = $datos['tributable'];
            $tipoHaber->calcula_horas_extras = $datos['calcula_horas_extras'];
            $tipoHaber->proporcional_dias_trabajados = $datos['proporcional_dias_trabajados'];
            $tipoHaber->calcula_semana_corrida = $datos['calcula_semana_corrida'];
            $tipoHaber->imponible = $datos['imponible'];
            $tipoHaber->gratificacion = $datos['gratificacion'];
            $tipoHaber->cuenta_id = $datos['cuenta_id'];
            $tipoHaber->save();
            $respuesta=array(
            	'success' => true,
            	'mensaje' => "La Información fue almacenada correctamente",
            	'sid' => $tipoHaber->sid
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
        $permisos = MenuSistema::obtenerPermisosAccesosURL(Auth::user(), '#ingreso-haberes');
        $datosHaber = null;
        $cuentas = Cuenta::listaCuentas();
        if($sid){
            $tipoHaber = TipoHaber::whereSid($sid)->first();
            $datosHaber=array(
                'id' => $tipoHaber->id,
                'sid' => $tipoHaber->sid,
                'codigo' => $tipoHaber->codigo,
                'nombre' => $tipoHaber->nombre,
                'tributable' => $tipoHaber->tributable ? true : false,
                'calculaHorasExtras' => $tipoHaber->calcula_horas_extras ? true : false,
                'proporcionalDiasTrabajados' => $tipoHaber->proporcional_dias_trabajados ? true : false,
                'calculaSemanaCorrida' => $tipoHaber->calcula_semana_corrida ? true : false,
                'imponible' => $tipoHaber->imponible ? true : false,
                'gratificacion' => $tipoHaber->gratificacion ? true : false,
                'haberes' => $tipoHaber->misHaberes(),
                'cuenta' => $tipoHaber->cuenta()
            );
        }
        
                
        $datos = array(
            'accesos' => $permisos,
            'datos' => $datosHaber,
            'cuentas' => $cuentas
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
        $tipoHaber = TipoHaber::whereSid($sid)->first();
        $datos = $this->get_datos_formulario();
        $errores = TipoHaber::errores($datos);       
        
        if(!$errores and $tipoHaber){
            $tipoHaber->codigo = $datos['codigo'];
            $tipoHaber->nombre = $datos['nombre'];
            $tipoHaber->tributable = $datos['tributable'];
            $tipoHaber->calcula_horas_extras = $datos['calcula_horas_extras'];
            $tipoHaber->proporcional_dias_trabajados = $datos['proporcional_dias_trabajados'];
            $tipoHaber->calcula_semana_corrida = $datos['calcula_semana_corrida'];
            $tipoHaber->imponible = $datos['imponible'];
            $tipoHaber->gratificacion = $datos['gratificacion'];
            $tipoHaber->cuenta_id = $datos['cuenta_id'];
            $tipoHaber->save();
            $respuesta = array(
            	'success' => true,
            	'mensaje' => "La Información fue actualizada correctamente",
                'sid' => $tipoHaber->sid
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
        $haber = TipoHaber::whereSid($datos['sid'])->first();
        
        $haber->cuenta_id = $datos['cuenta']['id'];      
        $haber->save();
        
        $respuesta = array(
            'success' => true,
            'mensaje' => "La Información fue actualizada correctamente",
            'sid' => $haber->sid
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
        $mensaje="La Información fue eliminada correctamente";
        TipoHaber::whereSid($sid)->delete();
        return Response::json(array('success' => true, 'mensaje' => $mensaje));
    }
    
    public function get_datos_formulario(){
        $datos = array(
            'codigo' => Input::get('codigo'),
            'nombre' => Input::get('nombre'),
            'tributable' => Input::get('tributable'),
            'calcula_horas_extras' => Input::get('calculaHorasExtras'),
            'proporcional_dias_trabajados' => Input::get('proporcionalDiasTrabajados'),
            'calcula_semana_corrida' => Input::get('calculaSemanaCorrida'),
            'imponible' => Input::get('imponible'),
            'gratificacion' => Input::get('gratificacion'),
            'cuenta_id' => Input::get('cuenta')['id']
        );
        return $datos;
    }

}