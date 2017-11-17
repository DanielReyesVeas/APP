<?php

class CentroCosto extends Eloquent {
    
    protected $table = 'centros_costo';
    
    static function listaCentrosCosto(){
    	$listaCentrosCosto = array();
    	$centrosCosto = CentroCosto::orderBy('nombre', 'ASC')->get();
    	if( $centrosCosto->count() ){
            foreach( $centrosCosto as $centroCosto ){
                $listaCentrosCosto[]=array(
                    'id' => $centroCosto->id,
                    'nombre' => $centroCosto->nombre
                );
            }
    	}
    	return $listaCentrosCosto;
    }
    
    static function codigosCentrosCosto(){
    	$codigosCentrosCosto = array();
    	$centrosCosto = CentroCosto::orderBy('nombre', 'ASC')->get();
    	if( $centrosCosto->count() ){
            foreach( $centrosCosto as $centroCosto ){
                $codigosTitulos[]=array(
                    'codigo' => $centroCosto->id,
                    'glosa' => $centroCosto->nombre
                );
            }
    	}
    	return $codigosCentrosCosto;
    }
        
    static function errores($datos){
         
        $rules = array(
            'nombre' => 'required'
        );

        $message = array(
            'centroCosto.required' => 'Obligatorio!'
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