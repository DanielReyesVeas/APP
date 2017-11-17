<?php

class Jornada extends Eloquent {
    
    protected $table = 'jornadas';
    
    public function tramoHoraExtra(){
        return $this->belongsTo('TramoHoraExtra', 'tramo_hora_extra_id');
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