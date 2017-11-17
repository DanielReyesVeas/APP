<?php

class Titulo extends Eloquent {
    
    protected $table = 'titulos';
    
    static function listaTitulos(){
    	$listaTitulos = array();
    	$titulos = Titulo::orderBy('nombre', 'ASC')->get();
    	if( $titulos->count() ){
            foreach( $titulos as $titulo ){
                $listaTitulos[]=array(
                    'id' => $titulo->id,
                    'nombre' => $titulo->nombre
                );
            }
    	}
    	return $listaTitulos;
    }
    
    static function codigosTitulos(){
    	$codigosTitulos = array();
    	$titulos = Titulo::orderBy('nombre', 'ASC')->get();
    	if( $titulos->count() ){
            foreach( $titulos as $titulo ){
                $codigosTitulos[]=array(
                    'codigo' => $titulo->id,
                    'glosa' => $titulo->nombre
                );
            }
    	}
    	return $codigosTitulos;
    }
        
    static function errores($datos){
         
        $rules = array(
            'nombre' => 'required'
        );

        $message = array(
            'titulo.required' => 'Obligatorio!'
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