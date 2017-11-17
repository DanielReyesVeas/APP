<?php

class Tienda extends Eloquent {
    
    protected $table = 'tiendas';
    
    static function listaTiendas(){
    	$listaTiendas = array();
    	$tiendas = Tienda::orderBy('nombre', 'ASC')->get();
    	if( $tiendas->count() ){
            foreach( $tiendas as $tienda ){
                $listaTiendas[]=array(
                    'id' => $tienda->id,
                    'nombre' => $tienda->codigo . ' - ' . $tienda->nombre
                );
            }
    	}
    	return $listaTiendas;
    }
    
    static function codigosTiendas(){
    	$codigosTiendas = array();
    	$tiendas = Tienda::orderBy('nombre', 'ASC')->get();
    	if( $tiendas->count() ){
            foreach( $tiendas as $tienda ){
                $codigosTiendas[]=array(
                    'codigo' => $tienda->id,
                    'glosa' => $tienda->nombre
                );
            }
    	}
    	return $codigosTiendas;
    }
        
    static function errores($datos){
         
        $rules = array(
            'nombre' => 'required'
        );

        $message = array(
            'tienda.required' => 'Obligatorio!'
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