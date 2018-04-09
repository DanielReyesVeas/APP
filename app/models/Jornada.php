<?php

class Jornada extends Eloquent {
    
    protected $table = 'jornadas';
    
    public function tramoHoraExtra(){
        return $this->belongsTo('TramoHoraExtra', 'tramo_hora_extra_id');
    }
    
    public function fichas(){
        return $this->hasMany('FichaTrabajador', 'tipo_jornada_id');
    }
    
    public function jornadaTramo(){
        return $this->hasMany('JornadaTramo', 'jornada_id');
    }
    
    public function tramos()
    {
        $jornadasTramos = $this->jornadaTramo;
        $datos = array();
        if($jornadasTramos->count()){
            foreach($jornadasTramos as $jornadasTramo){
                $datos[] = array(
                    'id' => $jornadasTramo->tramo->id,
                    'factor' => $jornadasTramo->tramo->factor
                );                
            }
        }
        
        return $datos;
    }
    
    static function listaJornadas(){
    	$listaJornadas = array();
    	$jornadas = Jornada::orderBy('nombre', 'ASC')->get();
    	if( $jornadas->count() ){
            foreach( $jornadas as $jornada ){
                $listaJornadas[]=array(
                    'id' => $jornada->id,
                    'nombre' => $jornada->nombre
                );
            }
    	}
    	return $listaJornadas;
    }
    
    static function codigosTiposJornada(){
    	$codigosTiposJornadas = array();
    	$jornadas = Jornada::orderBy('id', 'ASC')->get();
    	if( $jornadas->count() ){
            foreach( $jornadas as $jornada ){
                $codigosTiposJornadas[]=array(
                    'codigo' => $jornada->id,
                    'glosa' => $jornada->nombre
                );
            }
    	}
    	return $codigosTiposJornadas;
    }
    
    public function comprobarDependencias()
    {
        $fichas = $this->fichas;        
        
        if($fichas->count()){
            $errores = new stdClass();
            $errores->error = array("El Tipo de Jornada <b>" . $this->nombre . "</b> se encuentra asignada.<br /> Debe <b>reasignar</b> los trabajadores primero para poder realizar esta acción.");
            return $errores;
        }
        
        return;
    }
    
    static function errores($datos){
         
        $rules = array(
            'nombre' => 'required',
            'tramo_hora_extra_id' => 'required',
            'numero_horas' => 'required'
        );

        $message = array(
            'jornada.required' => 'Obligatorio!'
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