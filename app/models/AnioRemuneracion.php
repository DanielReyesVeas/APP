<?php

class AnioRemuneracion extends Eloquent {
    
    protected $table = 'anios_remuneraciones';
    
    public function MesDeTrabajo(){
        return $this->hasMany('MesDeTrabajo', 'anio_id');
    }
    
    public function meses()
    {
        $listaMeses = array();
        $id = $this->id;
        $meses = MesDeTrabajo::where('anio_id', $id)->orderBy('mes', 'ASC')->get();
        
        if($meses->count()){
            foreach($meses as $mes){
                $listaMeses[] = array(
                    'id' => $mes->id,
                    'sid' => $mes->sid,
                    'nombre' => $mes->nombre,
                    'mes' => $mes->mes,
                    'fechaRemuneracion' => $mes->fecha_remuneracion
                );
            }
        }
        
        return $listaMeses;        
    }
    
    public function mesesFestivos()
    {
        $listaMeses = array();
        $anio = $this->anio;
        $meses = array();
        
        for($i=1; $i<=12; $i++){
            if($i<10){
                $index = '0' . $i;
            }else{
                $index = $i;                
            }
            $nombre = Funciones::obtenerMesTexto($index);
            $mes = $anio . "-" . $index . "-01";
            $remuneracion = Funciones::obtenerFechaRemuneracion($nombre, $anio);
            $meses[] = array(
                'nombre' => $nombre,
                'mes' => $mes,
                'fechaRemuneracion' => $remuneracion,
                'feriados' => Feriado::feriados($mes, $remuneracion)
            );
        }
        
        return $meses;        
    }
    
    static function isMesAbierto()
    {
        $mes = \Session::get('mesActivo');
        $nombre = strtolower($mes->nombre);
        $idAnio = $mes->idAnio;
        $abierto = AnioRemuneracion::find($idAnio)->$nombre ? true : false;
        
        return $abierto;
    }
    
    static function isLiquidaciones($mes=null)
    {
        if(!$mes){
            $mesDeTrabajo = \Session::get('mesActivo')->id;
        }else{
            $mesDeTrabajo = MesDeTrabajo::where('mes', $mes)->first();            
            $mesDeTrabajo = $mesDeTrabajo['id'];            
        }
        $bool = Trabajador::isAllLiquidados($mesDeTrabajo);
        
        return $bool;    
    }
    
    static function isCuentas()
    {
        $bool = Aporte::isCuentas();
        
        return $bool;    
    }
    
    public function isIniciado($mes)
    {
        $id = $this->id;
        $isMes = MesDeTrabajo::where('anio_id', $id)->where('nombre', $mes)->first();
        $bool = false; 
        
        if($isMes){
            $bool = true; 
        }
        
        return $bool;
    }
    
    public function isDisponible($mes)
    {
        $empresa =  \Session::get('empresa');
        $disponible = false; 
        $isIngresado = false;
        $anio = $this->anio;
        $fechaActual = Funciones::obtenerFechaMes($mes, $anio);
        $fecha = date('Y-m-d', strtotime('-' . 1 . ' month', strtotime($fechaActual)));
        $date = date('Y-m-d');
        $textMes = strtolower($mes);
        $mesActual = $this;
        $fechaInicial = VariableSistema::where('variable', 'mes_inicial')->first();
        $fechaInicial = date($fechaInicial['valor1']);
        
        Config::set('database.default', 'admin' );                
        $isIngresado = DB::table('meses')->where('mes', $fechaActual)->first();
        Config::set('database.default', $empresa->base_datos );
        
        if($fecha<=$date && $fechaActual>=$fechaInicial && $isIngresado){
            $disponible = true;
        }
        
        return $disponible;
    }

    
    public function estadoMeses()
    {
        $estadoMeses = array();
        $anio = $this->anio;
        
        $estadoMeses[] = array(
            'nombre' => 'Enero',
            'abierto' => $this->enero==1 ? true : false,
            'iniciado' => $this->isIniciado('Enero'),
            'disponible' => $this->isDisponible('Enero'), 
            'mes' => Funciones::obtenerFechaMes('Enero', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Enero', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Enero', $anio))
        );
        
        $estadoMeses[] = array(
            'nombre' => 'Febrero',
            'abierto' => $this->febrero==1 ? true : false,
            'iniciado' => $this->isIniciado('Febrero'),
            'disponible' => $this->isDisponible('Febrero'),
            'mes' => Funciones::obtenerFechaMes('Febrero', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Febrero', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Febrero', $anio))
        );
        
        $estadoMeses[] = array(
            'nombre' => 'Marzo',
            'abierto' => $this->marzo==1 ? true : false,
            'iniciado' => $this->isIniciado('Marzo'),
            'disponible' => $this->isDisponible('Marzo'), 
            'mes' => Funciones::obtenerFechaMes('Marzo', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Marzo', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Marzo', $anio))
        );
        
        $estadoMeses[] = array(
            'nombre' => 'Abril',
            'abierto' => $this->abril==1 ? true : false,
            'iniciado' => $this->isIniciado('Abril'),
            'disponible' => $this->isDisponible('Abril'), 
            'mes' => Funciones::obtenerFechaMes('Abril', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Abril', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Abril', $anio))
        );
        
        $estadoMeses[] = array(
            'nombre' => 'Mayo',
            'abierto' => $this->mayo==1 ? true : false,
            'iniciado' => $this->isIniciado('Mayo'),
            'disponible' => $this->isDisponible('Mayo'),
            'mes' => Funciones::obtenerFechaMes('Mayo', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Mayo', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Mayo', $anio))
        );
        
        $estadoMeses[] = array(
            'nombre' => 'Junio',
            'abierto' => $this->junio==1 ? true : false,
            'iniciado' => $this->isIniciado('Junio'),
            'disponible' => $this->isDisponible('Junio'), 
            'mes' => Funciones::obtenerFechaMes('Junio', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Junio', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Junio', $anio))
        );
        
        $estadoMeses[] = array(
            'nombre' => 'Julio',
            'abierto' => $this->julio==1 ? true : false,
            'iniciado' => $this->isIniciado('Julio'),
            'disponible' => $this->isDisponible('Julio'), 
            'mes' => Funciones::obtenerFechaMes('Julio', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Julio', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Julio', $anio))
        );
        
        $estadoMeses[] = array(
            'nombre' => 'Agosto',
            'abierto' => $this->agosto==1 ? true : false,
            'iniciado' => $this->isIniciado('Agosto'),
            'disponible' => $this->isDisponible('Agosto'), 
            'mes' => Funciones::obtenerFechaMes('Agosto', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Agosto', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Agosto', $anio))
        );
        
        $estadoMeses[] = array(
            'nombre' => 'Septiembre',
            'abierto' => $this->septiembre==1 ? true : false,
            'iniciado' => $this->isIniciado('Septiembre'),
            'disponible' => $this->isDisponible('Septiembre') , 
            'mes' => Funciones::obtenerFechaMes('Septiembre', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Septiembre', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Septiembre', $anio))
        );
        
        $estadoMeses[] = array(
            'nombre' => 'Octubre',
            'abierto' => $this->octubre==1 ? true : false,
            'iniciado' => $this->isIniciado('Octubre'),
            'disponible' => $this->isDisponible('Octubre'), 
            'mes' => Funciones::obtenerFechaMes('Octubre', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Octubre', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Octubre', $anio))
        );
        
        $estadoMeses[] = array(
            'nombre' => 'Noviembre',
            'abierto' => $this->noviembre==1 ? true : false,
            'iniciado' => $this->isIniciado('Noviembre'),
            'disponible' => $this->isDisponible('Noviembre'), 
            'mes' => Funciones::obtenerFechaMes('Noviembre', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Noviembre', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Noviembre', $anio))
        );
        
        $estadoMeses[] = array(
            'nombre' => 'Diciembre',
            'abierto' => $this->diciembre==1 ? true : false,
            'iniciado' => $this->isIniciado('Diciembre'),
            'disponible' => $this->isDisponible('Diciembre'), 
            'mes' => Funciones::obtenerFechaMes('Diciembre', $anio),
            'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion('Diciembre', $anio),
            'isCentralizar' => AnioRemuneracion::isLiquidaciones(Funciones::obtenerFechaMes('Diciembre', $anio))
        );

        return $estadoMeses;
    }
    
    static function errores($datos){
         
        $rules = array(
        );

        $message = array(
            'anioRemuneracion.required' => 'Obligatorio!'
        );

        $verifier = App::make('validation.presence');
        $verifier->setConnection("principal");

        $validation = Validator::make($datos, $rules, $message);
        $validation->setPresenceVerifier($verifier);

        if($validation->fails()){
            return $validation->messages();
        }else{
            return false;
        }
    }
}