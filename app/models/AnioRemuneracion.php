<?php

class AnioRemuneracion extends Eloquent {
    
    protected $table = 'anios_remuneraciones';
    
    public function mesesDeTrabajo(){
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
    
    public function isNuevoAnio()
    {
        $empresa =  \Session::get('empresa');
        Config::set('database.default', 'admin' );                
        $isIngresado = DB::table('meses')->where('mes', '2018-01-01')->first();
        Config::set('database.default', $empresa->base_datos );
        
        if($isIngresado){
            return true;
        }
        
        return $isIngresado;
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
    
    static function aniosF1887()
    {
        $lista = array();
        $anios = AnioRemuneracion::orderBy('anio', 'DESC')->get();
                
        if( $anios->count() ){
            foreach( $anios as $anio ){
                $lista[]=array(
                    'id' => $anio->id,
                    'sid' => $anio->sid,
                    'nombre' => $anio->anio,
                    'isDiciembre' => $anio->isDiciembre()
                );
            }
        }
        
        return $lista;
    }
    
    public function isDiciembre()
    {
        $diciembre = MesDeTrabajo::where('anio_id', $this->id)->where('nombre', 'Diciembre')->first();
        if($diciembre){
            return true;
        }
        
        return false;
    }
    
    public function mesesFestivosVacaciones()
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
                'feriados' => FeriadoVacaciones::feriados($mes, $remuneracion)
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
        
        if($fecha<=$date && $fechaActual>=$fechaInicial && ($isIngresado || ($mes=="Enero" && $anio==2018) ) ){
            $disponible = true;
        }
        
        return $disponible;
    }
    
    public function isDisponibleSinIndicadores($mes)
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
        
        if($fecha<=$date && $fechaActual>=$fechaInicial && ($isIngresado || ($mes=="Enero" && $anio==2018) ) ){
            $disponible = true;
        }
        return true;
        return $disponible;
    }
    
    static function isCentralizado($mes)
    {
        $centralizacion = ComprobanteCentralizacion::where('mes', $mes)->get();
        if($centralizacion->count()){
            return true;
        }
        
        return false;
    }

    
    public function estadoMeses()
    {
        $estadoMeses = array();
        $anio = $this->anio;
        $meses = Config::get('constants.meses');
        
        foreach($meses as $mes){
            $nombre = strtolower($mes['value']);
            $isIniciado = $this->isIniciado($mes['value']);
            $isDisponible = true;
            if(!$isIniciado){
                $isDisponible = $this->isDisponible($mes['value']);
            }
            $estadoMeses[] = array(
                'nombre' => $mes['value'],
                'abierto' => $this->$nombre ? true : false,
                'iniciado' => $isIniciado,
                'disponible' => $isDisponible, 
                'mes' => Funciones::obtenerFechaMes($mes['value'], $anio),
                'fechaRemuneracion' => Funciones::obtenerFechaRemuneracion($mes['value'], $anio),
                'isCentralizado' => AnioRemuneracion::isCentralizado(Funciones::obtenerFechaMes($mes['value'], $anio)),
                'disponibleSinIndicadores' => $this->isDisponibleSinIndicadores($mes['value'])
            );            
        }            

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