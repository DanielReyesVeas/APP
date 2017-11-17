<?php

class TipoContrato extends Eloquent {
    
    protected $table = 'tipos_contrato';
    
    static function listaTiposContrato(){
    	$listaTiposContrato = array();
    	$tiposContrato = TipoContrato::orderBy('nombre', 'ASC')->get();
    	if( $tiposContrato->count() ){
            foreach( $tiposContrato as $tipoContrato ){
                $listaTiposContrato[]=array(
                    'id' => $tipoContrato->id,
                    'nombre' => $tipoContrato->nombre
                );
            }
    	}
    	return $listaTiposContrato;
    }
    
    static function codigosTiposContrato(){
    	$codigosTiposContrato = array();
    	$tiposContrato = TipoContrato::orderBy('id', 'ASC')->get();
    	if( $tiposContrato->count() ){
            foreach( $tiposContrato as $tipoContrato ){
                $codigosTiposContrato[]=array(
                    'codigo' => $tipoContrato->id,
                    'glosa' => $tipoContrato->nombre
                );
            }
    	}
    	return $codigosTiposContrato;
    }
    
    static function errores($datos){
         
        $rules = array(
            'nombre' => 'required'
        );

        $message = array(
            'tipoContrato.required' => 'Obligatorio!'
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